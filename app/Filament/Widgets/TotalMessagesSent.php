<?php

namespace App\Filament\Widgets;

use App\Models\Message;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TotalMessagesSent extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 4;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $this->filters['endDate'] ?? Carbon::now()->format('Y-m-d');
        $messageStatus = $this->filters['messageStatus'] ?? null;


        return [
            Stat::make(
                label: '',
                value: Message::query()
                    ->whereNull('messages.deleted_at')
                    ->when($startDate, fn(Builder $query) => $query->whereDate('messages.created_at', '>=', $startDate))
                    ->when($endDate, fn(Builder $query) => $query->whereDate('messages.created_at', '<=', $endDate))
                    ->when($messageStatus, fn(Builder $query) => $query->where('messages.status', '=', $messageStatus))
                    ->get()
                    ->count(),
            )
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->description('Total SMS Sent')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }

    public function getColumns(): int
    {
        return 1; // Number of columns in the grid
    }
}
