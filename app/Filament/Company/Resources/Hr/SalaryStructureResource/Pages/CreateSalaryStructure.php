<?php

namespace App\Filament\Company\Resources\Hr\SalaryStructureResource\Pages;

use Filament\Actions;
use App\Models\Hr\SalaryStructure;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Company\Resources\Hr\SalaryStructureResource;

class CreateSalaryStructure extends CreateRecord
{
    protected static string $resource = SalaryStructureResource::class;
}
