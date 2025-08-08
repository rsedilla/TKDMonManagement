<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsolidationResource\Pages;
use App\Models\Consolidation;
use App\Models\Leader;
use App\Models\CellMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;

class ConsolidationResource extends Resource
{
    protected static ?string $model = Consolidation::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Consolidation';

    protected static ?string $pluralModelLabel = 'Consolidations';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('VIP Information')
                    ->description('Enter the VIP (Very Important Person) details')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        TextInput::make('vip_name')
                            ->label('VIP Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter VIP full name')
                            ->prefixIcon('heroicon-o-user')
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Textarea::make('vip_contact_details')
                            ->label('VIP Contact Details')
                            ->required()
                            ->placeholder('Enter phone number, email, etc.')
                            ->rows(3)
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Textarea::make('vip_address')
                            ->label('VIP Address')
                            ->required()
                            ->placeholder('Enter complete address')
                            ->rows(3)
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Select::make('vip_status')
                            ->label('VIP Status')
                            ->options(Consolidation::getVipStatusOptions())
                            ->required()
                            ->native(false)
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ])->columns(1),

                Forms\Components\Section::make('Consolidation Details')
                    ->description('Consolidation process information')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('change_consolidator')
                            ->label('Change Consolidator?')
                            ->default(false)
                            ->live()
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),

                        Forms\Components\Placeholder::make('current_consolidator')
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
                            ),

                        Select::make('consolidator_selection')
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
                            ),

                        Forms\Components\Hidden::make('consolidator_type'),
                        Forms\Components\Hidden::make('consolidator_id'),

                        DatePicker::make('consolidation_date')
                            ->label('Consolidation Date')
                            ->required()
                            ->maxDate(now())
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Select::make('consolidation_place')
                            ->label('Consolidation Place')
                            ->options(Consolidation::getConsolidationPlaceOptions())
                            ->required()
                            ->native(false)
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ])->columns(2),

                Forms\Components\Section::make('VIP Progress Tracking')
                    ->description('Track VIP progress through SUYNL lessons, Sunday services, and cell group attendance')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->schema([
                        // SUYNL Lessons Header with Progress Counter
                        Forms\Components\Placeholder::make('suynl_header')
                            ->label('SUYNL Lessons Progress')
                            ->content(function (callable $get) {
                                $completed = 0;
                                for ($i = 1; $i <= 10; $i++) {
                                    if ($get("suynl_lesson_{$i}_date")) {
                                        $completed++;
                                    }
                                }
                                return "✅ {$completed}/10 lessons completed";
                            })
                            ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']),

                        // SUYNL Lessons Row 1 (1-5)
                        Forms\Components\Grid::make(5)
                            ->schema([
                                DatePicker::make('suynl_lesson_1_date')
                                    ->label('Lesson 1')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_2_date')
                                    ->label('Lesson 2')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_3_date')
                                    ->label('Lesson 3')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_4_date')
                                    ->label('Lesson 4')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_5_date')
                                    ->label('Lesson 5')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                            ]),
                            
                        // SUYNL Lessons Row 2 (6-10)
                        Forms\Components\Grid::make(5)
                            ->schema([
                                DatePicker::make('suynl_lesson_6_date')
                                    ->label('Lesson 6')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_7_date')
                                    ->label('Lesson 7')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_8_date')
                                    ->label('Lesson 8')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_9_date')
                                    ->label('Lesson 9')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('suynl_lesson_10_date')
                                    ->label('Lesson 10')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                            ]),

                        // Sunday Services Header with Progress Counter
                        Forms\Components\Placeholder::make('sunday_header')
                            ->label('Sunday Services Progress')
                            ->content(function (callable $get) {
                                $completed = 0;
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($get("sunday_service_{$i}_date")) {
                                        $completed++;
                                    }
                                }
                                return "⛪ {$completed}/4 services attended";
                            })
                            ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']),

                        // Sunday Services
                        Forms\Components\Grid::make(4)
                            ->schema([
                                DatePicker::make('sunday_service_1_date')
                                    ->label('Service 1')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('sunday_service_2_date')
                                    ->label('Service 2')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('sunday_service_3_date')
                                    ->label('Service 3')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('sunday_service_4_date')
                                    ->label('Service 4')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                            ]),

                        // Cell Group Header with Progress Counter
                        Forms\Components\Placeholder::make('cellgroup_header')
                            ->label('Cell Group Sessions Progress')
                            ->content(function (callable $get) {
                                $completed = 0;
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($get("cell_group_{$i}_date")) {
                                        $completed++;
                                    }
                                }
                                return "👥 {$completed}/4 sessions attended";
                            })
                            ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']),

                        // Cell Group Sessions
                        Forms\Components\Grid::make(4)
                            ->schema([
                                DatePicker::make('cell_group_1_date')
                                    ->label('Session 1')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('cell_group_2_date')
                                    ->label('Session 2')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('cell_group_3_date')
                                    ->label('Session 3')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                                    
                                DatePicker::make('cell_group_4_date')
                                    ->label('Session 4')
                                    ->placeholder('Select date')
                                    ->displayFormat('M d, Y')
                                    ->disabled(fn (string $operation) => $operation === 'view')
                                    ->live(),
                            ]),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vip_name')
                    ->label('VIP Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('consolidator_name')
                    ->label('Consolidator Name')
                    ->getStateUsing(function (Consolidation $record): string {
                        return $record->consolidator?->name . ' (' . 
                               ($record->consolidator_type === 'App\\Models\\Leader' ? 'Leader' : 'Cell Member') . ')' ?? 'N/A';
                    }),

                TextColumn::make('direct_leader')
                    ->label('Direct Leader')
                    ->getStateUsing(function (Consolidation $record): string {
                        if ($record->consolidator_type === 'App\\Models\\Leader') {
                            return $record->consolidator?->parentLeader?->name ?? 'Top Level';
                        } elseif ($record->consolidator_type === 'App\\Models\\CellMember') {
                            return $record->consolidator?->leader?->name ?? 'No Leader';
                        }
                        return 'N/A';
                    }),

                TextColumn::make('consolidation_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('consolidation_place')
                    ->label('Place')
                    ->colors([
                        'primary' => 'services',
                        'success' => 'cell_group',
                        'warning' => 'ove',
                    ]),

                BadgeColumn::make('vip_status')
                    ->label('VIP Status')
                    ->colors([
                        'info' => 'other_church',
                        'success' => 'new_christian',
                        'warning' => 'recommitment',
                    ]),

                TextColumn::make('vip_contact_details')
                    ->label('Contact')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('SUYNL Progress')
                    ->label('SUYNL')
                    ->getStateUsing(function (Consolidation $record): string {
                        return $record->getSuynlProgressFormatted();
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        $progress = (int) explode('/', $state)[0];
                        if ($progress >= 8) return 'success';
                        if ($progress >= 5) return 'warning';
                        if ($progress > 0) return 'info';
                        return 'gray';
                    }),

                TextColumn::make('Services')
                    ->getStateUsing(function (Consolidation $record): string {
                        return $record->getSundayServiceProgressFormatted();
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        $progress = (int) explode('/', $state)[0];
                        if ($progress === 4) return 'success';
                        if ($progress >= 2) return 'warning';
                        if ($progress > 0) return 'info';
                        return 'gray';
                    }),

                TextColumn::make('Cell Group')
                    ->label('Cell Group')
                    ->getStateUsing(function (Consolidation $record): string {
                        return $record->getCellGroupProgressFormatted();
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        $progress = (int) explode('/', $state)[0];
                        if ($progress === 4) return 'success';
                        if ($progress >= 2) return 'warning';
                        if ($progress > 0) return 'info';
                        return 'gray';
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('consolidation_place')
                    ->label('Place')
                    ->options(Consolidation::getConsolidationPlaceOptions()),

                SelectFilter::make('vip_status')
                    ->label('VIP Status')
                    ->options(Consolidation::getVipStatusOptions()),

                SelectFilter::make('consolidator_type')
                    ->label('Consolidator Type')
                    ->options([
                        'App\\Models\\Leader' => 'Leader',
                        'App\\Models\\CellMember' => 'Cell Member',
                    ]),

                Tables\Filters\Filter::make('suynl_completed')
                    ->label('SUYNL Completed (8+ lessons)')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereRaw('JSON_LENGTH(suynl_lessons_completed) >= 8')
                    ),

                Tables\Filters\Filter::make('services_completed')
                    ->label('All Services Attended')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereRaw('JSON_LENGTH(sunday_services_attended) = 4')
                    ),

                Tables\Filters\Filter::make('cellgroup_completed')
                    ->label('All Cell Groups Attended')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereRaw('JSON_LENGTH(cell_group_attended) = 4')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('consolidation_date', 'desc');
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
