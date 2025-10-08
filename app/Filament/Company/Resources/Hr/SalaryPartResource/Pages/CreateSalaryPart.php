<?php

namespace App\Filament\Company\Resources\Hr\SalaryPartResource\Pages;

use Filament\Actions;
use App\Models\Hr\SalaryPart;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Company\Resources\Hr\SalaryPartResource;

class CreateSalaryPart extends CreateRecord
{
    protected static string $resource = SalaryPartResource::class;
}
