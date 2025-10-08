<?php

namespace App\Filament\Company\Resources\Hr\SalaryPartResource\Pages;

use App\Filament\Company\Resources\Hr\SalaryPartResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryParts extends ListRecords
{
    protected static string $resource = SalaryPartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
