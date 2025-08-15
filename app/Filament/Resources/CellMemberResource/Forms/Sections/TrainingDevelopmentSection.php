<?php

namespace App\Filament\Resources\CellMemberResource\Forms\Sections;

use App\Filament\Shared\Helpers\CalendarActions;
use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class TrainingDevelopmentSection
{
    public static function get(): Section
    {
        return Forms\Components\Section::make('Training & Development')
            ->description('Track learning and growth progress')
            ->icon('heroicon-o-academic-cap')
            ->collapsible()
            ->collapsed()
            ->schema([
                self::trainingStatusField(),
                self::trainingDatesGrid(),
                self::auxiliaryClassesGrid(),
                self::ministriesLeadershipGrid(),
            ]);
    }

    private static function trainingStatusField()
    {
        return Forms\Components\Select::make('training_status')
            ->label('Pre-Encounter Training Status')
            ->options([
                'not_started' => 'Not Started',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
            ])
            ->default('not_started')
            ->placeholder('Select training status')
            ->native(false)
            ->live()
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function trainingDatesGrid(): Grid
    {
        return Forms\Components\Grid::make(3)
            ->schema([
                Forms\Components\DatePicker::make('encounter_date')
                    ->label('Encounter Date')
                    ->placeholder('Date of encounter session')
                    ->displayFormat('M d, Y')
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->suffixActions(CalendarActions::getCalendarActions('encounter_date'))
                    ->disabled(FormHelpers::getDisabledClosure()),

                Forms\Components\DatePicker::make('re_encounter_date')
                    ->label('Re-Encounter Date')
                    ->placeholder('Date of re-encounter session')
                    ->displayFormat('M d, Y')
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->suffixActions(CalendarActions::getCalendarActions('re_encounter_date'))
                    ->disabled(FormHelpers::getDisabledClosure()),

                Forms\Components\DatePicker::make('post_encounter_date')
                    ->label('Post-Encounter Date')
                    ->placeholder('Date of post-encounter session')
                    ->displayFormat('M d, Y')
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->suffixActions(CalendarActions::getCalendarActions('post_encounter_date'))
                    ->disabled(FormHelpers::getDisabledClosure()),
            ]);
    }

    private static function auxiliaryClassesGrid(): Grid
    {
        return Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\DatePicker::make('auxiliary_date')
                    ->label('Auxiliary Classes Date')
                    ->placeholder('Date auxiliary classes completed')
                    ->displayFormat('M d, Y')
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->native(false)
                    ->suffixActions(CalendarActions::getCalendarActions('auxiliary_date'))
                    ->disabled(FormHelpers::getDisabledClosure()),

                Forms\Components\TextInput::make('auxiliary_classes_attended')
                    ->label('Auxiliary Classes Attended')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Number of auxiliary classes')
                    ->suffix('classes')
                    ->disabled(FormHelpers::getDisabledClosure()),
            ]);
    }

    private static function ministriesLeadershipGrid(): Grid
    {
        return Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\TagsInput::make('ministries')
                    ->label('Ministries Involved')
                    ->placeholder('Add ministries')
                    ->suggestions([
                        'Worship Ministry',
                        'Teaching Ministry',
                        'Children Ministry',
                        'Youth Ministry',
                        'Prayer Ministry',
                        'Outreach Ministry',
                        'Media Ministry',
                        'Administration',
                        'Finance Ministry',
                        'Security Ministry',
                        'Hospitality Ministry',
                        'Pastoral Care',
                    ])
                    ->nestedRecursiveRules([
                        'min:2',
                        'max:100',
                    ])
                    ->helperText('Add the ministries this member is involved in')
                    ->disabled(FormHelpers::getDisabledClosure()),

                Forms\Components\Select::make('leadership_level')
                    ->label('Leadership Level')
                    ->options([
                        'member' => 'Member',
                        'auxiliary' => 'Auxiliary',
                        'leader' => 'Leader',
                        'coordinator' => 'Coordinator',
                        'pastor' => 'Pastor',
                    ])
                    ->placeholder('Select leadership level')
                    ->native(false)
                    ->disabled(FormHelpers::getDisabledClosure()),
            ]);
    }
}
