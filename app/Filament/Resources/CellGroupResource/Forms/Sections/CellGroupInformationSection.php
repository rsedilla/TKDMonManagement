<?php

namespace App\Filament\Resources\CellGroupResource\Forms\Sections;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class CellGroupInformationSection
{
    public static function get(): Section
    {
        return Forms\Components\Section::make('Cell Group Information')
            ->schema([
                self::getCellGroupIdField(),
                self::getLeaderIdField(),
                self::getCellGroupTypeField(),
            ])
            ->columns(2);
    }

    private static function getCellGroupIdField()
    {
        return Forms\Components\Hidden::make('cell_group_id');
    }

    private static function getLeaderIdField()
    {
        return Select::make('leader_id')
            ->label('Cell Leader')
            ->relationship('leader', 'name')
            ->searchable()
            ->preload()
            ->required()
            ->disabled(self::isDisabled())
            ->createOptionForm([
                self::getLeaderNameField(),
                self::getLeaderEmailField(),
                self::getLeaderPhoneField(),
            ]);
    }

    private static function getCellGroupTypeField()
    {
        return Select::make('cell_group_type')
            ->label('Cell Group Type')
            ->options([
                'Cell Group' => 'Cell Group',
                'Open Cell' => 'Open Cell',
                'G12 Cell' => 'G12 Cell',
            ])
            ->required()
            ->disabled(self::isDisabled())
            ->default('Cell Group');
    }

    private static function getLeaderNameField()
    {
        return TextInput::make('name')
            ->required()
            ->maxLength(255);
    }

    private static function getLeaderEmailField()
    {
        return TextInput::make('email')
            ->email()
            ->maxLength(255);
    }

    private static function getLeaderPhoneField()
    {
        return TextInput::make('phone')
            ->tel()
            ->maxLength(255);
    }

    private static function isDisabled(): \Closure
    {
        return function (string $operation, $livewire = null) {
            if ($operation === 'view') {
                return true;
            }
            
            // Check if we're in edit mode and the page has isEditing property
            if ($operation === 'edit' && $livewire && property_exists($livewire, 'isEditing')) {
                return !$livewire->isEditing;
            }
            
            return false;
        };
    }
}
