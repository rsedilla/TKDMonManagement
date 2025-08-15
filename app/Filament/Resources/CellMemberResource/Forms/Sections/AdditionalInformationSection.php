<?php

namespace App\Filament\Resources\CellMemberResource\Forms\Sections;

use App\Filament\Shared\Helpers\FormHelpers;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class AdditionalInformationSection
{
    public static function get(): Section
    {
        return Forms\Components\Section::make('Additional Information')
            ->description('Contact details and progress tracking')
            ->icon('heroicon-o-document-text')
            ->collapsible()
            ->collapsed()
            ->schema([
                self::contactInformationGrid(),
                self::statusField(),
                self::assignmentDateField(),
                self::notesField(),
            ]);
    }

    private static function contactInformationGrid(): Grid
    {
        return Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->placeholder('+63 XXX XXX XXXX')
                    ->helperText('Format: +63 9XX XXX XXXX')
                    ->maxLength(20)
                    ->disabled(FormHelpers::getDisabledClosure()),

                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->placeholder('cellmember@example.com')
                    ->maxLength(255)
                    ->disabled(FormHelpers::getDisabledClosure()),
            ]);
    }

    private static function statusField()
    {
        return Forms\Components\Select::make('status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'transferred' => 'Transferred',
                'graduated' => 'Graduated',
            ])
            ->default('active')
            ->placeholder('Select member status')
            ->native(false)
            ->live()
            ->disabled(FormHelpers::getDisabledClosure());
    }

    private static function assignmentDateField()
    {
        return Forms\Components\DateTimePicker::make('leader_assigned_at')
            ->label('Leader Assignment Date')
            ->placeholder('Date when leader was assigned')
            ->displayFormat('M d, Y h:i A')
            ->native(false)
            ->disabled()
            ->helperText('Automatically set when a leader is assigned');
    }

    private static function notesField()
    {
        return Forms\Components\Textarea::make('notes')
            ->label('Additional Notes')
            ->placeholder('Any additional information about this cell member')
            ->rows(4)
            ->maxLength(1000)
            ->helperText('Maximum 1000 characters')
            ->disabled(FormHelpers::getDisabledClosure());
    }
}
