<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPartSalaryStructure extends Pivot
{
    protected $table = 'salary_part_salary_structures';
    public $incrementing = true;

    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function salaryPart(): BelongsTo
    {
        return $this->belongsTo(SalaryPart::class);
    }
}
