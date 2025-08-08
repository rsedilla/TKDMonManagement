<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquippingResource\Pages;
use App\Models\Equipping;
use App\Models\Leader;
use App\Models\CellMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class EquippingResource extends Resource
{
    protected static ?string $model = Equipping::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Equipping Levels';

    protected static ?string $pluralModelLabel = 'Equipping Levels';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('equippable_type')
                    ->label('Person Type')
                    ->options([
                        'App\\Models\\Leader' => 'Leader',
                        'App\\Models\\CellMember' => 'Cell Member',
                    ])
                    ->required()
                    ->live(),

                Select::make('equippable_id')
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
                    ->searchable(),

                Select::make('training_attended')
                    ->label('Training Level')
                    ->options(Equipping::getTrainingOptions())
                    ->required(),

                Toggle::make('have_cell_group')
                    ->label('Has Cell Group')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Name')
                    ->getStateUsing(function (Equipping $record, $rowLoop): string {
                        $name = $record->equippable?->name ?? 'N/A';
                        return "{$name}";
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Type')
                    ->getStateUsing(function (Equipping $record): string {
                        return $record->equippable_type === 'App\\Models\\Leader' ? 'Leader' : 'Cell Member';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Leader' => 'success',
                        'Cell Member' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('Reports To')
                    ->getStateUsing(function (Equipping $record): string {
                        if ($record->equippable_type === 'App\\Models\\CellMember') {
                            return $record->equippable?->leader?->name ?? 'No Leader';
                        } elseif ($record->equippable_type === 'App\\Models\\Leader') {
                            return $record->equippable?->parentLeader?->name ?? 'Top Level';
                        }
                        return 'N/A';
                    }),

                BadgeColumn::make('training_attended')
                    ->label('Training Level')
                    ->colors([
                        'danger' => 'SUYNL',
                        'warning' => 'LIFECLASS',
                        'info' => 'ENCOUNTER',
                        'success' => 'SOL1',
                        'success' => 'SOL2',
                        'success' => 'SOL3',
                        'primary' => 'SOL GRADUATE',
                    ]),

                Tables\Columns\IconColumn::make('have_cell_group')
                    ->label('Has Cell Group')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('training_attended')
                    ->label('Training Level')
                    ->options(Equipping::getTrainingOptions()),

                SelectFilter::make('equippable_type')
                    ->label('Person Type')
                    ->options([
                        'App\\Models\\Leader' => 'Leader',
                        'App\\Models\\CellMember' => 'Cell Member',
                    ]),

                Tables\Filters\TernaryFilter::make('have_cell_group')
                    ->label('Has Cell Group'),
            ])
            ->actions([
                // Edit and Delete actions are disabled for security/read-only access
            ])
            ->bulkActions([
                // Bulk actions disabled for security
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
            'index' => Pages\ListEquippings::route('/'),
            'create' => Pages\CreateEquipping::route('/create'),
            'edit' => Pages\EditEquipping::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        // Make records read-only for security
        return false;
    }

    public static function canDelete($record): bool
    {
        // Disable delete for security
        return false;
    }

    public static function canDeleteAny(): bool
    {
        // Disable bulk delete for security
        return false;
    }
}
