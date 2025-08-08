<?php

namespace App\Filament\Widgets;

use App\Models\CellMember;
use App\Models\Leader;
use App\Models\Equipping;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Cell Leaders', Leader::count())
                ->description('Total number of cell leaders')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success')
                ->chart([4, 8, 12, 16, 20, 24, 28])
                ->chartColor('success'),
                
            Stat::make('Total Cell Members', CellMember::count())
                ->description('Total number of cell members')
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->chart([2, 6, 10, 14, 18, 22, 26])
                ->chartColor('info'),
                
            Stat::make('Active Cell Members', CellMember::where('status', true)->count())
                ->description('Currently active members')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('warning')
                ->chart([1, 3, 7, 11, 15, 19, 23])
                ->chartColor('warning'),
                
            Stat::make('LIFECLASS', Equipping::where('training_attended', 'LIFECLASS')->count())
                ->description('Members who attended LIFECLASS')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('primary')
                ->chart([2, 3, 5, 4, 6, 7, 8])
                ->chartColor('primary'),
            
            Stat::make('SOL 1', Equipping::where('training_attended', 'SOL1')->count())
                ->description('Members who completed SOL 1')
                ->descriptionIcon('heroicon-o-star')
                ->color('indigo')
                ->chart([1, 2, 3, 2, 4, 5, 6])
                ->chartColor('indigo'),
            
            Stat::make('SOL 2', Equipping::where('training_attended', 'SOL2')->count())
                ->description('Members who completed SOL 2')
                ->descriptionIcon('heroicon-o-fire')
                ->color('orange')
                ->chart([1, 1, 2, 3, 3, 4, 5])
                ->chartColor('orange'),
            
            Stat::make('SOL 3', Equipping::where('training_attended', 'SOL3')->count())
                ->description('Members who completed SOL 3')
                ->descriptionIcon('heroicon-o-bolt')
                ->color('purple')
                ->chart([0, 1, 1, 2, 2, 3, 4])
                ->chartColor('purple'),
            
            Stat::make('SOL GRADUATE', Equipping::where('training_attended', 'SOL GRADUATE')->count())
                ->description('Members who are SOL graduates')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('emerald')
                ->chart([0, 0, 1, 1, 2, 2, 3])
                ->chartColor('emerald'),
        ];
    }
}
