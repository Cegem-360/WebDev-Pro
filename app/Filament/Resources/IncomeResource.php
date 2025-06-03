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
use Filament\Forms\Get;
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

    protected static ?string $modelLabel = 'Bevétel';

    protected static ?string $pluralModelLabel = 'Bevételek';

    protected static ?string $navigationLabel = 'Bevételek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategória'),
                DatePicker::make('payment_date')
                    ->label('Befizetés dátuma')
                    ->required(),
                Textarea::make('description')
                    ->label('Leírás')

                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->label('Összeg')
                    ->required()
                    ->numeric(),
                Select::make('payment_type')
                    ->label('Fizetési mód')
                    ->live()
                    ->options(PaymentTypes::class)
                    ->required(),
                TextInput::make('recurring_times')
                    ->label('Ismétlődő alkalmak száma')
                 /*    ->visible(fn (string $operation, Get $get) => $get('payment_type') === PaymentTypes::RECURRING) */
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Select::make('status')
                    ->label('Állapot')
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
                    ->label('Kategória')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Leírás')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('payment_date')
                    ->label('Befizetés dátuma')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Összeg')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Állapot')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Frissítve')
                    ->dateTime('Y-m-d H:i:s')
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
            /*  'create' => CreateIncome::route('/create'), */
            'view' => ViewIncome::route('/{record}'),
            'edit' => EditIncome::route('/{record}/edit'),
        ];
    }
}
