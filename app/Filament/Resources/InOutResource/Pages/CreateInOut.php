<?php

namespace App\Filament\Resources\InOutResource\Pages;

use App\Filament\Resources\InOutResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Carbon\Carbon;

class CreateInOut extends CreateRecord
{
    protected static string $resource = InOutResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['date_applied'] = carbon::now();
        $data['trans_type'] = 2;
        return $data;
        
    }
}
