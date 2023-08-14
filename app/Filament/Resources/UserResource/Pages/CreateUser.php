<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\EmployeeLeaveCredit;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    protected function getRedirectUrl(): string
    {
    	return $this->getResource()::getUrl('index');
    }

  
    protected function mutateFormDataBeforeCreate(array $data): array
	{
        //dd($data);
        $data['password'] = '0p;/)P:?';
        $data['created_by'] =  auth()->id();
		$data['created_at'] = now();
		$data['updated_at'] = null;
	    return $data;

	}
	protected function getCreatedNotification(): ?Notification {
		return Notification::make()
		->success()
		->title('Created Successfully')
		->body('Employee has been created successfully');
	}

    protected function afterCreate(): void
    {
        DB::table('model_has_roles')->insert([
            'role_id' => $this->record->role_id,
            'model_type' => 'App\Models\User',
            'model_id' => $this->record->id
        ]);

        ////create EmployeeLeaveCredit/////
        $count = count($this->data['leave_type_id']);
        if($count){
            for($i=0;$i<=$count-1;$i++){
                $emp = new EmployeeLeaveCredit();
                $emp->employee_id = $this->record->id ;
                $emp->leave_type_id =$this->data['leave_type_id'][$i];
                $emp->credits = $this->data['credits'][$i];
                $emp->save();
            }
        }
        ////create EmployeeLeaveCredit/////


    }


}
