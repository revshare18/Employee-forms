<?php

namespace App\Filament\Resources\LeaveTypeResource\Pages;

use App\Filament\Resources\LeaveTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLeaveTypes extends ManageRecords
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->mutateFormDataUsing(function (array $data): array {
                $data['code'] = strtoupper($data['code']);
                $data['name'] = strtoupper($data['name']);
                $data['desc'] = strtoupper($data['desc']);
                return $data;
            }),
            
        ];
    }
}
