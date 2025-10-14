<?php

namespace App\Filament\Company\Resources\Purchases\BillResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
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
                    ->label('File')
                    ->preserveFilenames(),
            ])->columns(0);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('filename')
            ->columns([
                Tables\Columns\TextColumn::make('filename')
                    ->url(static fn ($record) => Storage::disk('public')->url($record->filepath))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
