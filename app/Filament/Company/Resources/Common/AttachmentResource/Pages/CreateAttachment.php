<?php

namespace App\Filament\Company\Resources\Common\AttachmentResource\Pages;

use Filament\Actions;
use App\Models\Common\Attachment;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Company\Resources\Common\AttachmentResource;

class CreateAttachment extends CreateRecord
{
    protected static string $resource = AttachmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return Attachment::createWithRelations($data);
    }
}
