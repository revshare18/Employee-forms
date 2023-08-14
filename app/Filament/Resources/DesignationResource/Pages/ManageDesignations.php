<?php

namespace App\Filament\Resources\DesignationResource\Pages;

use App\Filament\Resources\DesignationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Pages\Actions\CreateAction;

class ManageDesignations extends ManageRecords
{
    protected static string $resource = DesignationResource::class;

   

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
            
            ->beforeFormFilled(function () {
                // Runs before the form fields are populated with their default values.
            })
            ->afterFormFilled(function () {
                // Runs after the form fields are populated with their default values.
            })
            ->beforeFormValidated(function () {
                // Runs before the form fields are validated when the form is submitted.
            })
            ->afterFormValidated(function ( array $data ) {
                // Runs after the form fields are validated when the form is submitted.
            })
            ->before(function () {
                // Runs before the form fields are saved to the database.
            })
            ->after(function () {
                // Runs after the form fields are saved to the database.
            })
        ];
    }

}
