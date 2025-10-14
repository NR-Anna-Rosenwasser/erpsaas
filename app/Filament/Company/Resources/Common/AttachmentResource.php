<?php

namespace App\Filament\Company\Resources\Common;

use App\Filament\Company\Resources\Common\AttachmentResource\Pages;
use App\Filament\Company\Resources\Common\AttachmentResource\RelationManagers;
use App\Models\Common\Attachment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('filepath')
                    ->required()
                    ->getUploadedFileNameForStorageUsing(fn (Forms\Components\FileUpload $component, \Illuminate\Http\UploadedFile $file): string => (string) str($file->getClientOriginalName())->prepend(time() . '-'))
                    ->storeFileNamesIn('filename')
                    ->directory('attachments/company-' . auth()->user()->current_company_id . "/" . date('Y/m'))
                    ->maxSize(10240) // 10 MB
                    ->disk('public')
                    ->preserveFilenames(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttachments::route('/'),
            'create' => Pages\CreateAttachment::route('/create'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
        ];
    }
}
