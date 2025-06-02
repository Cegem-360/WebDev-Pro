<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Filament\Resources\IncomeResource\Pages\CreateIncome;
use App\Filament\Resources\IncomeResource\Pages\EditIncome;
use App\Filament\Resources\IncomeResource\Pages\ListIncomes;
use App\Filament\Resources\IncomeResource\Pages\ViewIncome;
use App\Models\Income;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->relationship('category', 'name'),
                DatePicker::make('payment_date')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('payment_type')
                    ->options(PaymentTypes::class)
                    ->required(),
                Select::make('status')
                    ->options(PaymentStatuses::class)
                    ->enum(PaymentStatuses::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListIncomes::route('/'),
            /* 'create' => CreateIncome::route('/create'), */
            'view' => ViewIncome::route('/{record}'),
            'edit' => EditIncome::route('/{record}/edit'),
        ];
    }
}
