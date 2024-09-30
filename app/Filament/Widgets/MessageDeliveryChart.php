<?php

namespace App\Filament\Widgets;

use App\Models\Message;
use App\Models\Tracking;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class MessageDeliveryChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected int|string|array $columnSpan = 4;
    protected static ?string $maxHeight = '300px';
    protected static ?string $heading = 'Message Delivery Stats';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $status = $this->filters['messageStatus'] ?? null;
        $results = Message::query()
            ->whereNull('messages.deleted_at')
            ->when($startDate, fn(Builder $query) => $query->whereDate('trackings.created_at', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('trackings.created_at', '<=', $endDate))
            ->when($status, fn(Builder $query) => $query->where('messages.status', '=', $status))
            ->selectRaw('status, count(*) as value')
            ->groupBy(['status'])
            ->get();

        $labels = $results->pluck('status')->toArray();
        $data = $results->pluck('value')->toArray();
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Message Delivery Stats',
                    'data' => $data,
                    'backgroundColor' => ['#36A2EB', '#FFCE56', '#9966FF','#9966FF', '#FF9F40', '#CEDF9F','#FF6384', '#36A2EB', '#FFCE56', '#9966FF','#A1D6B2', '#51829B', '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF', '#FF9F40', '#CEDF9F', '#F1F3C2', '#E8B86D'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
