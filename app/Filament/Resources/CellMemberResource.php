<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CellMemberResource\Pages;
use App\Filament\Resources\CellMemberResource\RelationManagers;
use App\Filament\Resources\CellMemberResource\Tables\CellMemberTable;
use App\Filament\Resources\CellMemberResource\Forms\CellMemberForm;
use App\Models\CellMember;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CellMemberResource extends Resource
{
    protected static ?string $model = CellMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(CellMemberForm::schema())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(CellMemberTable::columns())
            ->filters(CellMemberTable::filters())
            ->actions(CellMemberTable::actions())
            ->bulkActions(CellMemberTable::bulkActions())
            ->defaultSort(CellMemberTable::defaultSort());
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
            'index' => Pages\ListCellMembers::route('/'),
            'create' => Pages\CreateCellMember::route('/create'),
            'view' => Pages\ViewCellMember::route('/{record}'),
            'edit' => Pages\EditCellMember::route('/{record}/edit'),
        ];
    }
}
