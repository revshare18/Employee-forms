<?php

namespace App\Filament\Resources\DeptGroupResource\Pages;

use App\Filament\Resources\DeptGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDeptGroups extends ManageRecords
{
    protected static string $resource = DeptGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
