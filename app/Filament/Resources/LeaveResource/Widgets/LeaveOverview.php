<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Tables\Filters\Filter;

class LeaveOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '180s';
    protected function getCards(): array
    {
        //->queryString
       dd($this);
       
        return [
            Card::make('Unique views', '192.1k'),
            Card::make('Bounce rate', '21%'),
            Card::make('Average time on page', '3:12'),
        ];
    }

    protected function getFilters(): ?array
    {
        dd($this->tableFilters);
    } 
}
