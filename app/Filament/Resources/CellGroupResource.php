<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CellGroupResource\Pages;
use App\Models\CellGroup;
use App\Models\Leader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CellGroupResource extends Resource
{
    protected static ?string $model = CellGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Cell Groups';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $modelLabel = 'Cell Group';

    protected static ?string $pluralModelLabel = 'Cell Groups';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cell Group Information')
                    ->schema([
                        Forms\Components\Hidden::make('cell_group_id'),

                        Forms\Components\Select::make('leader_id')
                            ->label('Cell Leader')
                            ->relationship('leader', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Select::make('cell_group_type')
                            ->label('Cell Group Type')
                            ->options([
                                'Cell Group' => 'Cell Group',
                                'Open Cell' => 'Open Cell',
                                'G12 Cell' => 'G12 Cell',
                            ])
                            ->required()
                            ->default('Cell Group'),
                    ])->columns(2),

                Forms\Components\Section::make('Meeting Schedule')
                    ->schema([
                        Forms\Components\Select::make('meeting_day')
                            ->label('Meeting Day')
                            ->options([
                                'Monday' => 'Monday',
                                'Tuesday' => 'Tuesday',
                                'Wednesday' => 'Wednesday',
                                'Thursday' => 'Thursday',
                                'Friday' => 'Friday',
                                'Saturday' => 'Saturday',
                                'Sunday' => 'Sunday',
                            ])
                            ->required(),

                        Forms\Components\TimePicker::make('meeting_time')
                            ->label('Meeting Time')
                            ->required()
                            ->seconds(false),

                        Forms\Components\TextInput::make('meeting_location')
                            ->label('Meeting Location')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->rows(3),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cell_group_id')
                    ->label('Cell Group ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('leader.name')
                    ->label('Cell Leader')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('cell_group_type')
                    ->label('Type')
                    ->colors([
                        'success' => 'Cell Group',
                        'warning' => 'Open Cell',
                        'info' => 'G12 Cell',
                    ]),

                Tables\Columns\TextColumn::make('meeting_day')
                    ->label('Meeting Day')
                    ->sortable(),

                Tables\Columns\TextColumn::make('meeting_time')
                    ->label('Meeting Time')
                    ->time('g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('meeting_location')
                    ->label('Location')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('total_members')
                    ->label('Members')
                    ->getStateUsing(fn ($record) => $record->getTotalMembersCount())
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cell_group_type')
                    ->label('Cell Group Type')
                    ->options([
                        'Cell Group' => 'Cell Group',
                        'Open Cell' => 'Open Cell',
                        'G12 Cell' => 'G12 Cell',
                    ]),

                Tables\Filters\SelectFilter::make('meeting_day')
                    ->label('Meeting Day')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            'index' => Pages\ListCellGroups::route('/'),
            'create' => Pages\CreateCellGroup::route('/create'),
            'view' => Pages\ViewCellGroup::route('/{record}'),
            'edit' => Pages\EditCellGroup::route('/{record}/edit'),
        ];
    }
}
