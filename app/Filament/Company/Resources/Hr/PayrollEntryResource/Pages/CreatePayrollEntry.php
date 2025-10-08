<?php

namespace App\Filament\Company\Resources\Hr\PayrollEntryResource\Pages;

use Filament\Actions;
use App\Models\Hr\PayrollEntry;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Company\Resources\Hr\PayrollEntryResource;

class CreatePayrollEntry extends CreateRecord
{
    protected static string $resource = PayrollEntryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return PayrollEntry::createWithTransaction($data);
    }
}
