<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsolidationResource\Pages;
use App\Filament\Resources\ConsolidationResource\Forms\ConsolidationForm;
use App\Filament\Resources\ConsolidationResource\Tables\ConsolidationTable;
use App\Models\Consolidation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ConsolidationResource extends Resource
{
    protected static ?string $model = Consolidation::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Consolidation';

    protected static ?string $pluralModelLabel = 'Consolidations';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema(ConsolidationForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return ConsolidationTable::getTable($table);
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
            'index' => Pages\ListConsolidations::route('/'),
            'create' => Pages\CreateConsolidation::route('/create'),
            'view' => Pages\ViewConsolidation::route('/{record}'),
            'edit' => Pages\EditConsolidation::route('/{record}/edit'),
        ];
    }
}
