<?php

namespace App\Filament\Resources\ConsolidationResource\Forms\Sections;

use App\Filament\Shared\Helpers\CalendarActions;
use App\Filament\Shared\Helpers\FormHelpers;
use App\Filament\Shared\Factories\SectionFactory;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;

class VipProgressTrackingSection
{
    public static function get(): Forms\Components\Section
    {
        return SectionFactory::vipProgressTracking()
            ->schema([
                self::getSuynlHeader(),
                self::getSuynlLessonsRow1(),
                self::getSuynlLessonsRow2(),
                self::getSundayServicesHeader(),
                self::getSundayServicesGrid(),
                self::getCellGroupHeader(),
                self::getCellGroupGrid(),
            ]);
    }

    private static function getSuynlHeader(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('suynl_header')
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
            ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']);
    }

    private static function getSuynlLessonsRow1(): Forms\Components\Grid
    {
        return Forms\Components\Grid::make(5)
            ->schema([
                self::getSuynlLessonField(1),
                self::getSuynlLessonField(2),
                self::getSuynlLessonField(3),
                self::getSuynlLessonField(4),
                self::getSuynlLessonField(5),
            ]);
    }

    private static function getSuynlLessonsRow2(): Forms\Components\Grid
    {
        return Forms\Components\Grid::make(5)
            ->schema([
                self::getSuynlLessonField(6),
                self::getSuynlLessonField(7),
                self::getSuynlLessonField(8),
                self::getSuynlLessonField(9),
                self::getSuynlLessonField(10),
            ]);
    }

    private static function getSuynlLessonField(int $lessonNumber): DatePicker
    {
        return DatePicker::make("suynl_lesson_{$lessonNumber}_date")
            ->label("Lesson {$lessonNumber}")
            ->placeholder('Select date')
            ->displayFormat('M d, Y')
            ->format('Y-m-d')
            ->closeOnDateSelection()
            ->native(false)
            ->suffixActions(CalendarActions::getCalendarActions("suynl_lesson_{$lessonNumber}_date"))
            ->disabled(FormHelpers::getDisabledClosure())
            ->live();
    }

    private static function getSundayServicesHeader(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('sunday_header')
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
            ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']);
    }

    private static function getSundayServicesGrid(): Forms\Components\Grid
    {
        return Forms\Components\Grid::make(4)
            ->schema([
                self::getSundayServiceField(1),
                self::getSundayServiceField(2),
                self::getSundayServiceField(3),
                self::getSundayServiceField(4),
            ]);
    }

    private static function getSundayServiceField(int $serviceNumber): DatePicker
    {
        return DatePicker::make("sunday_service_{$serviceNumber}_date")
            ->label("Service {$serviceNumber}")
            ->placeholder('Select date')
            ->displayFormat('M d, Y')
            ->format('Y-m-d')
            ->closeOnDateSelection()
            ->native(false)
            ->suffixActions(CalendarActions::getCalendarActions("sunday_service_{$serviceNumber}_date"))
            ->disabled(FormHelpers::getDisabledClosure())
            ->live();
    }

    private static function getCellGroupHeader(): Forms\Components\Placeholder
    {
        return Forms\Components\Placeholder::make('cellgroup_header')
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
            ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']);
    }

    private static function getCellGroupGrid(): Forms\Components\Grid
    {
        return Forms\Components\Grid::make(4)
            ->schema([
                self::getCellGroupSessionField(1),
                self::getCellGroupSessionField(2),
                self::getCellGroupSessionField(3),
                self::getCellGroupSessionField(4),
            ]);
    }

    private static function getCellGroupSessionField(int $sessionNumber): DatePicker
    {
        return DatePicker::make("cell_group_{$sessionNumber}_date")
            ->label("Session {$sessionNumber}")
            ->placeholder('Select date')
            ->displayFormat('M d, Y')
            ->format('Y-m-d')
            ->closeOnDateSelection()
            ->native(false)
            ->suffixActions(CalendarActions::getCalendarActions("cell_group_{$sessionNumber}_date"))
            ->disabled(FormHelpers::getDisabledClosure())
            ->live();
    }
}
