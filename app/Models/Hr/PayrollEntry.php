<?php

namespace App\Models\Hr;

use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use App\Models\Accounting\Transaction;
use Illuminate\Database\Eloquent\Model;

class PayrollEntry extends Model
{
    use CompanyOwned;
    use Blamable;

    protected $table = 'payroll_entries';
    protected $fillable = [
        'entry_number',
        'from_date',
        'to_date',
        'employee_id',
        'salary_structure_id',
        'transaction_id',
    ];

    public static function getNextPayrollEntryNumber(): string
    {
        $lastEntry = self::orderBy('entry_number', 'desc')
            ->first();
        $nextNumber = $lastEntry ? (int) $lastEntry->entry_number + 1 : 1;
        return str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a PayrollEntry along with its associated Journal Entries within a transaction.
     */
    public static function createWithTransaction(array $data): self
    {
        $salaryStructure = SalaryStructure::find($data['salary_structure_id']);
        $salaryParts = $salaryStructure->spss->map(function ($sps) {
            return $sps->salaryPart;
        });
        $baseSalary = $salaryParts->where("type", "base_salary")->sum("amount");
        $netSalary = 0;

        $journalEntries = [];
        foreach ($salaryParts as $part) {
            if ($part->basis == \App\Enums\Hr\SalaryPartBasis::PercentageOfBaseSalary) {
                $amount = ($part->amount / 100) * $baseSalary;
            } else {
                $amount = $part->amount;
            }
            if ($part->in_net_salary) {
                if (in_array($part->type, [\App\Enums\Hr\SalaryPartType::Deduction])) {
                    $netSalary -= $amount;
                } else {
                    $netSalary += $amount;
                }
            }

            if ($part->debitAccount && $part->creditAccount) {
                $journalEntries[] = [
                    'account_id' => $part->debitAccount->id,
                    'type' => 'debit',
                    'amount' => $amount * 100,
                    'description' => $part->name . ($part->description ? '(' . $part->description  . ')' : ''),
                ];
                $journalEntries[] = [
                    'account_id' => $part->creditAccount->id,
                    'type' => 'credit',
                    'amount' => $amount * 100,
                    'description' => $part->name . ($part->description ? '(' . $part->description  . ')' : ''),
                ];
            } else if ($part->debitAccount || $part->creditAccount) {
                $account = $part->debitAccount ?? $part->creditAccount;
                $type = $part->debitAccount ? 'debit' : 'credit';
                $journalEntries[] = [
                    'account_id' => $account->id,
                    'type' => $type,
                    'amount' => $amount * 100,
                    'description' => $part->name . ($part->description ? '(' . $part->description  . ')' : '')
                ];
            }
        }

        $journalEntries[] = [
            'account_id' => $salaryStructure->payrollLiabilitiesAccount->id,
            'type' => 'credit',
            'amount' => $netSalary * 100,
            "description" => "Net Salary Payable",
        ];

        // Check that debit and credit amounts balance
        $totalDebit = collect($journalEntries)->where('type', 'debit')->sum('amount');
        $totalCredit = collect($journalEntries)->where('type', 'credit')->sum('amount');
        if ($totalDebit !== $totalCredit) {
            throw new \Exception("Error: Journal entries do not balance. Debit = $totalDebit, Credit = $totalCredit");
        }

        $payrollEntry = self::create($data);

        $transaction = Transaction::create([
            'transactionable_type' => self::class,
            'transactionable_id' => $payrollEntry->id,
            "type" => "journal",
            "amount" => $totalDebit,
            "posted_at" => $data['to_date'],
            "description" => "Payroll for " . $payrollEntry->employee->contact->first_name . " " . $payrollEntry->employee->contact->last_name . " for period " . $payrollEntry->from_date . " to " . $payrollEntry->to_date,
        ]);

        foreach ($journalEntries as $entry) {
            $entry['transaction_id'] = $transaction->id;
            $transaction->journalEntries()->create($entry);
        }

        $payrollEntry->transaction_id = $transaction->id;
        $payrollEntry->save();
        return $payrollEntry;

    }

    /**
     * BelongsTo relationship with Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * BelongsTo relationship with SalaryStructure
     */
    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class, 'salary_structure_id', 'id');
    }

    /**
     * BelongsTo relationship with Transaction
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }
}
