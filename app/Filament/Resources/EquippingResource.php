<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquippingResource\Pages;
use App\Filament\Resources\EquippingResource\Forms\EquippingForm;
use App\Filament\Resources\EquippingResource\Tables\EquippingTable;
use App\Models\Equipping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class EquippingResource extends Resource
{
    protected static ?string $model = Equipping::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Equipping Levels';

    protected static ?string $pluralModelLabel = 'Equipping Levels';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema(EquippingForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(EquippingTable::columns())
            ->filters(EquippingTable::filters())
            ->actions(EquippingTable::actions())
            ->bulkActions(EquippingTable::bulkActions())
            ->defaultSort(...EquippingTable::defaultSort());
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
            'index' => Pages\ListEquippings::route('/'),
            'create' => Pages\CreateEquipping::route('/create'),
            'edit' => Pages\EditEquipping::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        // Make records read-only for security
        return false;
    }

    public static function canDelete($record): bool
    {
        // Disable delete for security
        return false;
    }

    public static function canDeleteAny(): bool
    {
        // Disable bulk delete for security
        return false;
    }
}
