<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Pages\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;

class ManageDepartments extends ManageRecords
{
    protected static string $resource = DepartmentResource::class;

    
    protected function getActions(): array
    {
        return [
            CreateAction::make()
            ->using(function (array $data): Model {
                $data['created_by'] = auth()->id();
                return static::getModel()::create($data);
            })   
        ];
    }


}
