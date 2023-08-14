<?php

namespace App\Filament\Resources\InOutResource\Pages;

use App\Filament\Resources\InOutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Position;

class ListInOuts extends ListRecords
{
    protected static string $resource = InOutResource::class;

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
}
