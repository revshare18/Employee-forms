<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\EmployeeLeaveCredit;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $count = count($this->data['leave_type_id']);
        if($count){
            for($i=0;$i<=$count-1;$i++){
                $emp = EmployeeLeaveCredit::where(
                                           ['employee_id'   => $this->record->id,
                                            'leave_type_id' => $this->data['leave_type_id'][$i]
                                           ])
                                            ->update(['credits'=>  $this->data['credits'][$i]]);
            }
        }
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        //dd($data);
        $data['updated_by'] = auth()->id();
        return $data;
    }


}
