<?php

namespace App\Filament\Resources\InOutResource\Pages;

use App\Filament\Resources\InOutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInOut extends EditRecord
{
    protected static string $resource = InOutResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
