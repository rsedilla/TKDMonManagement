<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderResource\Pages;
use App\Filament\Resources\LeaderResource\RelationManagers;
use App\Filament\Resources\LeaderResource\Forms\LeaderForm;
use App\Filament\Resources\LeaderResource\Tables\LeaderTable;
use App\Models\Leader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaderResource extends Resource
{
    protected static ?string $model = Leader::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Cell Leaders';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema(LeaderForm::schema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(LeaderTable::columns())
            ->filters(LeaderTable::filters())
            ->actions(LeaderTable::actions())
            ->bulkActions(LeaderTable::bulkActions())
            ->defaultSort(...LeaderTable::defaultSort());
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
            'index' => Pages\ListLeaders::route('/'),
            'create' => Pages\CreateLeader::route('/create'),
            'view' => Pages\ViewLeader::route('/{record}'),
            'edit' => Pages\EditLeader::route('/{record}/edit'),
        ];
    }
}
