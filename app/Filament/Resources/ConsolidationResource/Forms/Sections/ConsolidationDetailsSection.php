<?php

namespace App\Filament\Resources\ConsolidationResource\Forms\Sections;

use App\Models\Consolidation;
use App\Filament\Shared\Helpers\CalendarActions;
use App\Filament\Shared\Helpers\FormHelpers;
use App\Filament\Shared\Factories\SectionFactory;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class ConsolidationDetailsSection
{
    public static function get(): Forms\Components\Section
    {
        return SectionFactory::consolidationDetails()
            ->schema([
                self::getChangeConsolidatorToggle(),
                self::getCurrentConsolidatorPlaceholder(),
                self::getConsolidatorSelectionField(),
                self::getConsolidatorTypeField(),
                self::getConsolidatorIdField(),
                self::getConsolidationDateField(),
                self::getConsolidationPlaceField(),
            ]);
    }

    private static function getChangeConsolidatorToggle(): Forms\Components\Toggle
    {
        return Forms\Components\Toggle::make('change_consolidator')
            ->label('Change Consolidator?')
            ->default(false)
            ->live()
            ->visible(fn (string $operation): bool => $operation === 'edit')
            ->dehydrated(false);
    }

    private static function getCurrentConsolidatorPlaceholder(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('current_consolidator')
            ->label('Current Consolidator')
            ->content(function ($record) {
                if (!$record) return 'N/A';
                
                if ($record->consolidator_type === 'App\\Models\\Leader') {
                    $consolidator = \App\Models\Leader::find($record->consolidator_id);
                    return $consolidator?->name . ' (Leader)' ?? 'N/A';
                } elseif ($record->consolidator_type === 'App\\Models\\CellMember') {
                    $consolidator = \App\Models\CellMember::find($record->consolidator_id);
                    return $consolidator?->name . ' (Cell Member)' ?? 'N/A';
                }
                return 'N/A';
            })
            ->visible(fn (string $operation, callable $get): bool => 
                $operation === 'edit' && !$get('change_consolidator')
            );
    }

    private static function getConsolidatorSelectionField(): Select
    {
        return Select::make('consolidator_selection')
            ->label('Consolidator')
            ->options(function () {
                return Consolidation::getConsolidatorOptions();
            })
            ->required()
            ->searchable()
            ->live()
            ->afterStateUpdated(function (callable $set, $state) {
                if ($state) {
                    [$type, $id] = explode(':', $state);
                    $set('consolidator_type', $type);
                    $set('consolidator_id', $id);
                }
            })
            ->helperText('Only people with SOL2, SOL3, or SOL Graduate training are shown')
            ->visible(fn (string $operation, callable $get): bool => 
                $operation === 'create' || ($operation === 'edit' && $get('change_consolidator'))
            );
    }

    private static function getConsolidatorTypeField(): Forms\Components\Hidden
    {
        return Forms\Components\Hidden::make('consolidator_type');
    }

    private static function getConsolidatorIdField(): Forms\Components\Hidden
    {
        return Forms\Components\Hidden::make('consolidator_id');
    }

    private static function getConsolidationDateField(): DatePicker
    {
        return DatePicker::make('consolidation_date')
            ->label('Consolidation Date')
            ->required()
            ->maxDate(now())
            ->displayFormat('M d, Y')
            ->format('Y-m-d')
            ->closeOnDateSelection()
            ->native(false)
            ->suffixActions(CalendarActions::getCalendarActions('consolidation_date'))
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function getConsolidationPlaceField(): Select
    {
        return Select::make('consolidation_place')
            ->label('Consolidation Place')
            ->options(Consolidation::getConsolidationPlaceOptions())
            ->required()
            ->native(false)
            ->disabled(FormHelpers::getDisabledClosure());
    }
}
