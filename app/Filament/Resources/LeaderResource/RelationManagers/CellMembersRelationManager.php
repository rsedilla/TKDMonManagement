<?php

namespace App\Filament\Resources\LeaderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CellMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'cellMembers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(120),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Additional Notes')
                    ->rows(3),
                
                Forms\Components\DatePicker::make('enrollment_date')
                    ->label('Join Date')
                    ->default(now()),
                
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Join Date')
                    ->date()
                    ->sortable(),
                
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
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active cell members')
                    ->falseLabel('Inactive cell members')
                    ->placeholder('All cell members'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
