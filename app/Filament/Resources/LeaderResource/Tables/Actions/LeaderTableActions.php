<?php

namespace App\Filament\Resources\LeaderResource\Tables\Actions;

use App\Models\Leader;
use Filament\Forms;
use Filament\Tables;

class LeaderTableActions
{
    /**
     * Get table actions configuration
     */
    public static function get(): array
    {
        return [
            self::viewAction(),
            self::editAction(),
            self::assignLeaderAction(),
            self::deleteAction(),
        ];
    }

    private static function viewAction(): Tables\Actions\ViewAction
    {
        return Tables\Actions\ViewAction::make();
    }

    private static function editAction(): Tables\Actions\EditAction
    {
        return Tables\Actions\EditAction::make();
    }

    private static function assignLeaderAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('assign_leader')
            ->label('Assign Leader')
            ->icon('heroicon-o-user-plus')
            ->color('info')
            ->form([
                Forms\Components\Select::make('leader_id')
                    ->label('Assign to Leader')
                    ->options(Leader::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data, Leader $record): void {
                $record->update(['parent_leader_id' => $data['leader_id']]);
            })
            ->requiresConfirmation()
            ->modalHeading('Assign Cell Leader')
            ->modalDescription('Select a leader to assign this person to.')
            ->modalSubmitActionLabel('Assign');
    }

    private static function deleteAction(): Tables\Actions\DeleteAction
    {
        return Tables\Actions\DeleteAction::make()
            ->before(function (Leader $record) {
                if ($record->hasDependencies()) {
                    $message = "Cannot delete this leader because they have: " . $record->getDependencySummary() . ". Please reassign or remove these dependencies first.";
                    throw new \Filament\Support\Exceptions\Halt($message);
                }
            })
            ->successNotification(
                \Filament\Notifications\Notification::make()
                    ->success()
                    ->title('Leader deleted')
                    ->body('The leader has been successfully deleted.')
            );
    }
}
