<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\BudgetItemTypes;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\Pages\ViewCategory;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Kategória';

    protected static ?string $pluralModelLabel = 'Kategóriák';

    protected static ?string $navigationLabel = 'Kategóriák';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Név')
                    ->required(),
                Select::make('budget_item_type')
                    ->label('Költségvetési tétel típusa')
                    ->options(BudgetItemTypes::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Név')
                    ->searchable(),
                TextColumn::make('budget_item_type')
                    ->label('Költségvetési tétel típusa')
                    ->badge()
                    ->color(fn (Category $record): string => match ($record->budget_item_type) {
                        BudgetItemTypes::INCOME => 'success',
                        BudgetItemTypes::EXPENSE => 'danger',
                        BudgetItemTypes::SAVINGS => 'warning',
                        BudgetItemTypes::INVESTMENT => 'primary',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime('Y. m. d. H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Frissítve')
                    ->dateTime('Y. m. d. H:i:s')
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
            'index' => ListCategories::route('/'),
            /* 'create' => Pages\CreateCategory::route('/create'), */
            'view' => ViewCategory::route('/{record}'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
