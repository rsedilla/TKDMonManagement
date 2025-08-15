<?php

namespace App\Filament\Resources\EquippingResource\Forms;

use App\Models\Equipping;
use App\Models\Leader;
use App\Models\CellMember;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class EquippingForm
{
    /**
     * Get complete form schema
     */
    public static function schema(): array
    {
        return [
            self::personTypeField(),
            self::personField(),
            self::trainingAttendedField(),
            self::haveCellGroupField(),
        ];
    }

    /**
     * Person Type field
     */
    private static function personTypeField(): Select
    {
        return Select::make('equippable_type')
            ->label('Person Type')
            ->options([
                'App\\Models\\Leader' => 'Leader',
                'App\\Models\\CellMember' => 'Cell Member',
            ])
            ->required()
            ->live();
    }

    /**
     * Person selection field
     */
    private static function personField(): Select
    {
        return Select::make('equippable_id')
            ->label('Person')
            ->options(function (callable $get) {
                $type = $get('equippable_type');
                
                if ($type === 'App\\Models\\Leader') {
                    return Leader::all()->pluck('name', 'id');
                } elseif ($type === 'App\\Models\\CellMember') {
                    return CellMember::all()->pluck('name', 'id');
                }
                
                return [];
            })
            ->required()
            ->searchable();
    }

    /**
     * Training attended field
     */
    private static function trainingAttendedField(): Select
    {
        return Select::make('training_attended')
            ->label('Training Level')
            ->options(Equipping::getTrainingOptions())
            ->required();
    }

    /**
     * Have cell group field
     */
    private static function haveCellGroupField(): Toggle
    {
        return Toggle::make('have_cell_group')
            ->label('Has Cell Group')
            ->default(false);
    }
}
