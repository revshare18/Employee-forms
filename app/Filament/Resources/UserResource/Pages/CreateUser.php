<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\EmployeeLeaveCredit;
use Illuminate\Support\Facades\Mail;
use App\Mail\CreateUsers;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    public $pass = '';

    protected function getRedirectUrl(): string
    {
    	return $this->getResource()::getUrl('index');
    }

  
    protected function mutateFormDataBeforeCreate(array $data): array
	{
        $length = 8;
        $randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
        $this->pass = $randomletter;
        $data['password'] = $randomletter;
        $data['created_by'] =  auth()->id();
		$data['created_at'] = now();
		$data['updated_at'] = null;
        $data['password'] = \Hash::make($data['password']);
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

        $params = array('name'=>$this->data['name'],'password'=>$this->pass);
        Mail::to($this->data['email'])->send(new CreateUsers($params));

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
