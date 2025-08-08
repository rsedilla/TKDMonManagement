<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderResource\Pages;
use App\Filament\Resources\LeaderResource\RelationManagers;
use App\Models\Leader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaderResource extends Resource
{
    protected static ?string $model = Leader::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Cell Leaders';

    protected static ?int $navigationSort = 2;

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
                            ->placeholder('Enter cell leader full name')
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
                                    ->helperText('Age will be calculated from birthday if provided')
                                    ->disabled(fn (string $operation) => $operation === 'view'),

                                Forms\Components\DatePicker::make('birthday')
                                    ->placeholder('Select birth date')
                                    ->maxDate(now())
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state) {
                                            $age = \Carbon\Carbon::parse($state)->age;
                                            $set('age', $age);
                                        }
                                    })
                                    ->helperText('Age will be automatically calculated')
                                    ->disabled(fn (string $operation) => $operation === 'view'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter email address')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->disabled(fn (string $operation) => $operation === 'view'),

                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('Enter phone number')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->disabled(fn (string $operation) => $operation === 'view'),
                            ]),
                    ])->columns(1),

                Forms\Components\Section::make('Leadership Information')
                    ->description('Position and organizational details')
                    ->icon('heroicon-o-briefcase')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('position')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter leadership position')
                                    ->helperText('e.g., Senior Pastor, Associate Pastor, etc.')
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->disabled(fn (string $operation) => $operation === 'view'),

                                Forms\Components\Select::make('network')
                                    ->options([
                                        'mens' => 'Men\'s Network',
                                        'womens' => 'Women\'s Network',
                                    ])
                                    ->placeholder('Select network')
                                    ->native(false)
                                    ->disabled(fn (string $operation) => $operation === 'view'),
                            ]),

                        Forms\Components\Select::make('civil_status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'widowed' => 'Widowed',
                                'divorced' => 'Divorced',
                            ])
                            ->placeholder('Select civil status')
                            ->native(false)
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Forms\Components\Select::make('parent_leader_id')
                            ->label('Reports To')
                            ->relationship('parentLeader', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select parent leader (leave empty for top-level)')
                            ->helperText('Choose the leader this person reports to')
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
                    ])->columns(2),

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
                                'SOL1' => 'SOL 1',
                                'SOL2' => 'SOL 2',
                                'SOL3' => 'SOL 3',
                                'SOL GRADUATE' => 'SOL GRADUATE',
                            ])
                            ->placeholder('Select training level')
                            ->helperText('Choose the highest level completed')
                            ->reactive()
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Forms\Components\Toggle::make('have_cell_group')
                            ->label('Has Cell Group')
                            ->helperText('Does this leader have their own cell group?')
                            ->default(false)
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->description('Bio and status information')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Enter a brief biography or description')
                            ->disabled(fn (string $operation) => $operation === 'view'),

                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Whether this leader is currently active')
                            ->disabled(fn (string $operation) => $operation === 'view'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable()
                    ->suffix(' yrs')
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('parentLeader.name')
                    ->label('Reports To')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Top Level')
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color('info')
                    ->sortable(),

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
                    ->placeholder('Not specified')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cellGroup.cell_group_id')
                    ->label('Cell Group')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No cell group')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('equipping.training_attended')
                    ->label('Training Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SUYNL' => 'gray',
                        'LIFECLASS' => 'blue',
                        'ENCOUNTER' => 'green',
                        'SOL1' => 'yellow',
                        'SOL2' => 'orange',
                        'SOL3' => 'red',
                        'SOL GRADUATE' => 'purple',
                        default => 'secondary'
                    })
                    ->placeholder('No training')
                    ->toggleable(),

                Tables\Columns\BooleanColumn::make('status')
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('network')
                    ->options([
                        'mens' => 'Men\'s Network',
                        'womens' => 'Women\'s Network',
                    ]),

                Tables\Filters\SelectFilter::make('parent_leader_id')
                    ->label('Reports To')
                    ->relationship('parentLeader', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('top_level')
                    ->label('Top Level Leaders')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_leader_id'))
                    ->toggle(),

                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active Status'),

                Tables\Filters\SelectFilter::make('training_level')
                    ->label('Training Level')
                    ->options([
                        'SUYNL' => 'SUYNL',
                        'LIFECLASS' => 'LIFECLASS',
                        'ENCOUNTER' => 'ENCOUNTER',
                        'SOL1' => 'SOL 1',
                        'SOL2' => 'SOL 2',
                        'SOL3' => 'SOL 3',
                        'SOL GRADUATE' => 'SOL GRADUATE',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $training) => $query->whereHas('equipping', 
                                fn (Builder $query) => $query->where('training_attended', $training)
                            )
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('assign_leader')
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
                    ->modalSubmitActionLabel('Assign'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
