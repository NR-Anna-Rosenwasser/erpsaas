<?php

namespace App\Models\Hr;

use App\Models\Company;
use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use App\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryStructure extends Model
{
    use CompanyOwned;
    use Blamable;
    protected $table = 'salary_structures';
    protected $fillable = [
        'name',
        'description',
        'account_id',
        'effective_date',
        'termination_date',
    ];

    /**
     * HasMany relationship with SalaryPart through pivot table salary_part_salary_structure
     */
    public function spss(): HasMany
    {
        return $this->hasMany(SalaryPartSalaryStructure::class);
    }

    /**
     * HasOne relationship with Account «payrollLiabilitiesAccount»
     */
    public function payrollLiabilitiesAccount()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
