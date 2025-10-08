<?php

namespace App\Models\Hr;

use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use App\Models\Company;
use App\Models\Accounting\Account;
use App\Enums\Hr\SalaryPartType;
use App\Enums\Hr\SalaryPartBasis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryPart extends Model
{
    use CompanyOwned;
    use Blamable;
    protected $table = 'salary_parts';

    protected $fillable = [
        'part_number',
        'name',
        'type',
        'basis',
        'in_net_salary',
        'amount',
        'debit_account_id',
        'credit_account_id',
        'description',
    ];

    protected $casts = [
        'type' => SalaryPartType::class,
        'basis' => SalaryPartBasis::class,
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function getNextSalaryPartNumber(SalaryPartType $type): string
    {
        $lastPart = self::where('type', $type)
            ->orderBy('part_number', 'desc')
            ->first();

        $nextNumber = $lastPart ? (int) $lastPart->part_number + 1 : 1;

        return str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function spss(): HasMany
    {
        return $this->hasMany(SalaryPartSalaryStructure::class);
    }

    public function debitAccount()
    {
        return $this->hasOne(Account::class, 'id', 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->hasOne(Account::class, 'id', 'credit_account_id');
    }

}
