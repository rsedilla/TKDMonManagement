<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CellGroupResource\Pages;
use App\Filament\Resources\CellGroupResource\Forms\CellGroupForm;
use App\Filament\Resources\CellGroupResource\Tables\CellGroupTable;
use App\Models\CellGroup;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CellGroupResource extends Resource
{
    protected static ?string $model = CellGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Cell Groups';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $modelLabel = 'Cell Group';

    protected static ?string $pluralModelLabel = 'Cell Groups';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema(CellGroupForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return CellGroupTable::getTable($table);
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
            'index' => Pages\ListCellGroups::route('/'),
            'create' => Pages\CreateCellGroup::route('/create'),
            'view' => Pages\ViewCellGroup::route('/{record}'),
            'edit' => Pages\EditCellGroup::route('/{record}/edit'),
        ];
    }
}
