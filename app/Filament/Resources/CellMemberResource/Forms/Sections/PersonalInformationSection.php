<?php

namespace App\Filament\Resources\CellMemberResource\Forms\Sections;

use App\Filament\Shared\Helpers\CalendarActions;
use App\Filament\Shared\Helpers\FormHelpers;
use App\Filament\Shared\Factories\SectionFactory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class PersonalInformationSection
{
    public static function get(): Section
    {
        return SectionFactory::personalInformation()
            ->schema([
                self::nameField(),
                self::ageAndBirthdayGrid(),
                self::networkAndCivilStatusGrid(),
                self::leaderField(),
                self::cellGroupField(),
            ]);
    }

    private static function nameField()
    {
        return Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->placeholder('Enter cell member full name')
            ->helperText('Please enter the complete name')
            ->prefixIcon('heroicon-o-user')
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function ageAndBirthdayGrid(): Grid
    {
        return Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\TextInput::make('age')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(120)
                    ->placeholder('Auto-calculated from birthday')
                    ->suffix('years old')
                    ->rules(['integer', 'min:1', 'max:120'])
                    ->disabled()
                    ->helperText('Age is automatically calculated from birthday'),
                
                Forms\Components\DatePicker::make('birthday')
                    ->label('Birthday')
                    ->maxDate(now())
                    ->minDate(now()->subYears(120))
                    ->placeholder('Select birthday')
                    ->displayFormat('M d, Y')
                    ->format('Y-m-d')
                    ->closeOnDateSelection()
                    ->live()
                    ->native(false)
                    ->suffixActions(CalendarActions::getCalendarActions('birthday'))
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $birthday = \Carbon\Carbon::parse($state);
                            $age = $birthday->age;
                            $set('age', $age);
                        }
                    })
                    ->disabled(FormHelpers::getDisabledClosure()),
            ]);
    }

    private static function networkAndCivilStatusGrid(): Grid
    {
        return Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\Select::make('network')
                    ->required()
                    ->options([
                        'mens' => 'Mens',
                        'womens' => 'Womens',
                    ])
                    ->placeholder('Select network')
                    ->native(false)
                    ->disabled(FormHelpers::getDisabledClosure()),

                Forms\Components\Select::make('civil_status')
                    ->label('Civil Status')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                        'widow' => 'Widow',
                    ])
                    ->placeholder('Select civil status')
                    ->native(false)
                    ->disabled(FormHelpers::getDisabledClosure()),
            ]);
    }

    private static function leaderField()
    {
        return Forms\Components\Select::make('leader_id')
            ->label('Assigned Cell Leader')
            ->relationship('leader', 'name')
            ->searchable()
            ->preload()
            ->createOptionForm([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ])
            ->placeholder('Select a cell leader or create new')
            ->helperText('Choose the cell leader responsible for this cell member')
            ->reactive()
            ->afterStateUpdated(fn (callable $set, $state) => 
                $set('leader_assigned_at', $state ? now() : null)
            )
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function cellGroupField()
    {
        return Forms\Components\Select::make('cell_group_id')
            ->label('Cell Group')
            ->relationship(
                name: 'cellGroup', 
                titleAttribute: 'cell_group_id',
                modifyQueryUsing: fn ($query) => $query->with('leader')
            )
            ->getOptionLabelFromRecordUsing(fn ($record) => 
                $record->cell_group_id . ' - ' . ($record->leader->name ?? 'No Leader')
            )
            ->getSearchResultsUsing(function (string $search) {
                return \App\Models\CellGroup::with('leader')
                    ->where(function($query) use ($search) {
                        $query->where('cell_group_id', 'like', "%{$search}%")
                              ->orWhereHas('leader', function($q) use ($search) {
                                  $q->where('name', 'like', "%{$search}%");
                              });
                    })
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn ($record) => [
                        $record->id => $record->cell_group_id . ' - ' . ($record->leader->name ?? 'No Leader')
                    ]);
            })
            ->searchable()
            ->preload()
            ->placeholder('Select a cell group (optional)')
            ->helperText('Search by cell group ID or leader name')
            ->disabled(FormHelpers::getDisabledClosure());
    }
}
