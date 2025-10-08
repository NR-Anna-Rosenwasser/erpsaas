<?php

namespace App\Filament\Company\Resources\Hr;

use App\Filament\Company\Resources\Hr\PayrollEntryResource\Pages;
use App\Filament\Company\Resources\Hr\PayrollEntryResource\RelationManagers;
use App\Models\Hr\PayrollEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollEntryResource extends Resource
{
    protected static ?string $model = PayrollEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("General")
                    ->schema([
                        Forms\Components\TextInput::make('entry_number')
                            ->required()
                            ->prefix('PE-')
                            ->default(fn () => PayrollEntry::getNextPayrollEntryNumber())
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('from_date')
                            ->live()
                            ->default(fn () => now()->day > 10 ? now()->addMonth()->startOfMonth() : now()->startOfMonth())
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Calculate the to_date as the last day of the month of from_date
                                $toDate = \Carbon\Carbon::parse($state)->endOfMonth()->toDateString();
                                $set('to_date', $toDate);
                            })
                            ->required(),
                        Forms\Components\DatePicker::make('to_date')
                            ->default(fn () => now()->day > 10 ? now()->addMonth()->endOfMonth() : now()->endOfMonth())
                            ->required(),
                    ])->columns(),
                Forms\Components\Section::make("Employee Details")
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee',"id")
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->contact->first_name} {$record->contact->last_name} ({$record->employee_number})")
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('salary_structure_id')
                            ->label('Salary Structure')
                            ->relationship('salaryStructure', 'name')
                            ->required(),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entry_number')
                    ->label('Entry #')
                    ->prefix('PE-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label('Employee #')
                    ->searchable()
                    ->sortable(),

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
            'index' => Pages\ListPayrollEntries::route('/'),
            'create' => Pages\CreatePayrollEntry::route('/create'),
            'edit' => Pages\EditPayrollEntry::route('/{record}/edit'),
        ];
    }
}
