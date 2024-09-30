<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DefaultNoAccessMainDashboard extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 12;

    protected function getStats(): array
    {
        return [
            Stat::make('', null)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->description('You are seeing this because you have not been granted access to view the dashboard information. Request access from your administrator.')
                ->descriptionIcon('heroicon-m-key')
                ->color('danger')
        ];
    }

    public function getColumns(): int
    {
        return 1;
    }
}
