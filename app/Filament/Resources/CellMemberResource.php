<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CellMemberResource\Pages;
use App\Filament\Resources\CellMemberResource\RelationManagers;
use App\Models\CellMember;
use App\Models\Leader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CellMemberResource extends Resource
{
    protected static ?string $model = CellMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->description('Enter the basic personal details')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter cell member full name')
                            ->helperText('Please enter the complete name')
                            ->prefixIcon('heroicon-o-user')
                            ->disabled(fn (string $operation) => $operation === 'view'),
                        
                        Forms\Components\Grid::make(2)
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
                                    // ->required() removed
                                    ->maxDate(now())
                                    ->minDate(now()->subYears(120)) // Allow up to 120 years old
                                    ->placeholder('Select birthday')
                                    ->displayFormat('M d, Y')
                                    ->format('Y-m-d')
                                    ->closeOnDateSelection()
                                    ->live()
                                    ->native(false) // Use Filament's date picker instead of native
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state) {
                                            $birthday = \Carbon\Carbon::parse($state);
                                            $age = $birthday->age;
                                            $set('age', $age);
                                        }
                                    })
                                    ->disabled(fn (string $operation) => $operation === 'view'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('network')
                                    ->required()
                                    ->options([
                                        'mens' => 'Mens',
                                        'womens' => 'Womens',
                                    ])
                                    ->placeholder('Select network')
                                    ->native(false)
                                    ->disabled(fn (string $operation) => $operation === 'view'),

                                Forms\Components\Select::make('civil_status')
                                    ->label('Civil Status')
                                    // ->required() removed
                                    ->options([
                                        'single' => 'Single',
                                        'married' => 'Married',
                                        'widow' => 'Widow',
                                    ])
                                    ->placeholder('Select civil status')
                                    ->native(false)
                                    ->disabled(fn (string $operation) => $operation === 'view'),
                            ]),
                        
                        Forms\Components\Select::make('leader_id')
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
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Forms\Components\Select::make('cell_group_id')
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
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ]),
                
                Forms\Components\Section::make('Training & Development')
                    ->description('Equipping and leadership development information')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Select::make('training_attended')
                            ->label('Training Attended (SOL = School of Leaders)')
                            ->options([
                                'SUYNL' => 'SUYNL',
                                'LIFECLASS' => 'LIFECLASS',
                                'ENCOUNTER' => 'ENCOUNTER',
                                'SOL1' => 'SOL1',
                                'SOL2' => 'SOL2',
                                'SOL3' => 'SOL3',
                                'SOL GRADUATE' => 'SOL GRADUATE',
                            ])
                            ->placeholder('Select training level')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && $record->equipping) {
                                    $component->state($record->equipping->training_attended);
                                }
                            })
                            ->disabled(fn (string $operation) => $operation === 'view'),
                        
                        Forms\Components\Toggle::make('have_cell_group')
                            ->label('Have Cell Group?')
                            ->default(false)
                            ->helperText('Toggle on if this member has their own cell group')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && $record->equipping) {
                                    $component->state((bool) $record->equipping->have_cell_group);
                                } else {
                                    $component->state(false);
                                }
                            })
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->placeholder('Any additional information about the cell member...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn (string $operation) => $operation === 'view'),
                        
                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Join Date')
                            ->default(now())
                            ->maxDate(now())
                            ->disabled(fn (string $operation) => $operation === 'view'),
                        
                        Forms\Components\Toggle::make('status')
                            ->label('Active Status')
                            ->default(true)
                            ->helperText('Toggle to activate/deactivate the cell member')
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark')
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('birthday')
                    ->date('M d, Y')
                    ->sortable()
                    ->placeholder('Not specified')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('network')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mens' => 'blue',
                        'womens' => 'pink',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->placeholder('Not specified'),
                
                Tables\Columns\TextColumn::make('civil_status')
                    ->label('Civil Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'info',
                        'married' => 'success',
                        'widow' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->placeholder('Not specified'),
                
                Tables\Columns\TextColumn::make('leader.name')
                    ->label('Cell Leader')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No cell leader assigned')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('leader.position')
                    ->label('Cell Leader Position')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('N/A'),
                
                Tables\Columns\TextColumn::make('cellGroup.cell_group_id')
                    ->label('Cell Group')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No cell group')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('equipping.training_attended')
                    ->label('Training Attended')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SUYNL' => 'gray',
                        'LIFECLASS' => 'blue',
                        'ENCOUNTER' => 'green',
                        'SOL1' => 'yellow',
                        'SOL2' => 'orange',
                        'SOL3' => 'red',
                        'SOL GRADUATE' => 'purple',
                        default => 'gray',
                    })
                    ->placeholder('Not specified'),
                
                Tables\Columns\BooleanColumn::make('equipping.have_cell_group')
                    ->label('Have Cell Group')
                    ->placeholder('Not specified'),
                
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Join Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active cell members')
                    ->falseLabel('Inactive cell members')
                    ->placeholder('All cell members'),
                
                Tables\Filters\SelectFilter::make('leader')
                    ->relationship('leader', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Direct Cell Leader'),
                
                Tables\Filters\SelectFilter::make('network')
                    ->options([
                        'mens' => 'Mens',
                        'womens' => 'Womens',
                    ]),
                
                Tables\Filters\SelectFilter::make('training_attended')
                    ->options([
                        'SUYNL' => 'SUYNL',
                        'LIFECLASS' => 'LIFECLASS',
                        'ENCOUNTER' => 'ENCOUNTER',
                        'SOL1' => 'SOL1',
                        'SOL2' => 'SOL2',
                        'SOL3' => 'SOL3',
                        'SOL GRADUATE' => 'SOL GRADUATE',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('equipping', function (Builder $query) use ($value) {
                                $query->where('training_attended', $value);
                            })
                        );
                    })
                    ->label('Training Attended')
                    ->indicator('Training'),
                
                Tables\Filters\TernaryFilter::make('have_cell_group')
                    ->label('Have Cell Group')
                    ->boolean()
                    ->trueLabel('Has cell group')
                    ->falseLabel('No cell group')
                    ->placeholder('All members')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            isset($data['value']),
                            fn (Builder $query): Builder => $query->whereHas('equipping', function (Builder $query) use ($data) {
                                $query->where('have_cell_group', $data['value']);
                            })
                        );
                    })
                    ->indicator('Cell Group Status'),
                
                Tables\Filters\SelectFilter::make('network_leader')
                    ->label('Network Cell Leader (Including Hierarchy)')
                    ->options(fn () => Leader::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['value']) {
                            return $query;
                        }
                        
                        $leader = Leader::find($data['value']);
                        if (!$leader) {
                            return $query;
                        }
                        
                        return $query->where(function($q) use ($leader) {
                            // Direct cell members under this leader
                            $q->where('leader_id', $leader->id)
                              // OR cell members under any descendant leader in the network
                              ->orWhereHas('leader', function ($subQuery) use ($leader) {
                                  $subQuery->where('path', 'LIKE', $leader->path . $leader->id . '/%');
                              });
                        });
                    })
                    ->indicator('Network Leader'),
                
                Tables\Filters\Filter::make('unassigned')
                    ->label('Unassigned Cell Members')
                    ->query(fn (Builder $query): Builder => $query->whereNull('leader_id'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('assign_leader')
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
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('name');
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
