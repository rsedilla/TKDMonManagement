<?php

namespace App\Filament\Resources\LeaderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChildLeadersRelationManager extends RelationManager
{
    protected static string $relationship = 'childLeaders';

    protected static ?string $title = 'Team Hierarchy';

    protected static ?string $label = 'Subordinate Leader';

    protected static ?string $pluralLabel = 'Subordinate Leaders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Leader Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('position')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('department')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->description('Leaders directly reporting to this leader and their teams')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('cell_members_count')
                    ->label('Direct Cell Members')
                    ->counts('cellMembers')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('child_leaders_count')
                    ->label('Sub-Leaders')
                    ->counts('childLeaders')
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('total_network')
                    ->label('Total Network')
                    ->getStateUsing(fn ($record) => $record->getNetworkSize())
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active leaders')
                    ->falseLabel('Inactive leaders')
                    ->placeholder('All leaders'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Subordinate Leader'),
                Tables\Actions\Action::make('view_complete_hierarchy')
                    ->label('View Complete Hierarchy')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->action(function () {
                        $this->dispatch('open-modal', id: 'hierarchy-modal');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_team')
                    ->label('View Team')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.resources.leaders.edit', $record)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('level');
    }
}
