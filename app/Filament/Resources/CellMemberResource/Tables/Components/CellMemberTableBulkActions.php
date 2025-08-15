<?php

namespace App\Filament\Resources\CellMemberResource\Tables\Components;

use Filament\Forms;
use Filament\Tables;

class CellMemberTableBulkActions
{
    public static function get(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                self::assignLeaderBulkAction(),
            ]),
        ];
    }

    private static function assignLeaderBulkAction()
    {
        return Tables\Actions\BulkAction::make('assign_leader')
            ->label('Assign Leader')
            ->icon('heroicon-o-user-plus')
            ->form([
                Forms\Components\Select::make('leader_id')
                    ->label('Leader')
                    ->relationship('leader', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    $record->update(['leader_id' => $data['leader_id']]);
                }
            })
            ->deselectRecordsAfterCompletion();
    }
}
