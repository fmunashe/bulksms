<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DefaultNoAccessMainDashboard;
use App\Filament\Widgets\MessageDeliveryChart;
use App\Filament\Widgets\TotalMessagesSent;
use App\Filament\Widgets\TotalMessagesSentDelivered;
use App\Filament\Widgets\TotalMessagesSentExpired;
use App\Filament\Widgets\TotalMessagesSentPending;
use App\Filament\Widgets\TotalMessagesSentUndelivered;
use App\Models\Merchant;
use App\Models\Message;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersAction;

    protected static ?string $title = 'Dashboard';
    protected static string $routePath = 'main-dashboard';

    public function getWidgets(): array
    {

        if ($this->checkRole()) {
            return [
                TotalMessagesSent::class,
                TotalMessagesSentDelivered::class,
                TotalMessagesSentPending::class,
                TotalMessagesSentUndelivered::class,
                TotalMessagesSentExpired::class,
                MessageDeliveryChart::class
            ];
        }
        return [
            DefaultNoAccessMainDashboard::class
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
            'md' => 6,
            'xl' => 12,
        ];
    }

    protected function getHeaderActions(): array
    {
        if ($this->checkRole()) {
            return [
                FilterAction::make()
                    ->form([
                        Select::make('messageStatus')
                            ->label('Message Status')
                            ->searchable()
                            ->options(Message::MESSAGE_STATUS_SELECT)
                            ->preload(),
                        Select::make('merchant')
                            ->label('Merchant')
                            ->searchable()
                            ->options(Merchant::getMerchants(Auth::user()->merchant->trade_name ?? null))
                            ->preload(),

                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                    ]),
            ];
        }
        return [];
    }

    private function checkRole()
    {
        return auth()->user()->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'main_dashboard_access');
        })->exists();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'main_dashboard_access');
        })->exists();
    }
}
