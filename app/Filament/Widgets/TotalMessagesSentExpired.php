<?php

namespace App\Filament\Widgets;

use App\Models\Message;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TotalMessagesSentExpired extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 6;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $this->filters['endDate'] ?? Carbon::now()->format('Y-m-d');
        $messageStatus = $this->filters['messageStatus'] ?? Message::MESSAGE_STATUS_SELECT['Expired'];
        $merchant = $this->filters['merchant'] ?? Auth::user()->merchant->id ?? null;

        return [
            Stat::make(
                label: '',
                value: Message::query()
                    ->whereNull('messages.deleted_at')
                    ->where('messages.status','=','Expired')
                    ->where('merchant_id', '=', $merchant)
                    ->when($startDate, fn(Builder $query) => $query->whereDate('messages.created_at', '>=', $startDate))
                    ->when($endDate, fn(Builder $query) => $query->whereDate('messages.created_at', '<=', $endDate))
                    ->when($messageStatus, fn(Builder $query) => $query->where('messages.status', '=', $messageStatus))
                    ->get()
                    ->count(),
            )
                ->chart([7, 2, 10, 3, 15, 4, 0,1,2,1,2,1])
                ->description('Total Expired SMS Before Delivery')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }

    public function getColumns(): int
    {
        return 1; // Number of columns in the grid
    }
}
