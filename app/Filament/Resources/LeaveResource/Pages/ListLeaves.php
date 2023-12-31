<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;
use App\Filament\Resources\LeaveResource\Widgets\LeaveOverview;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActionsPosition(): ?string
    {
    return Position::BeforeCells;
    }


    protected function getHeaderWidgets() : array 
    {
        return [
            LeaveOverview::class
        ];
    }
}
