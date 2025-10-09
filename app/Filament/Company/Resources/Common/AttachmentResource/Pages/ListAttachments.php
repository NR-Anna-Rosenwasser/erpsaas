<?php

namespace App\Filament\Company\Resources\Common\AttachmentResource\Pages;

use App\Filament\Company\Resources\Common\AttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
