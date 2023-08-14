<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Employee;
use App\Models\EmployeeLeaveCredit;
use Carbon\Carbon;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;




    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function beforeFill(): void
    {
        
        //dd(auth()->user());
        
     
        // Runs before the form fields are populated with their default values.
    }
 
    protected function afterFill(): void
    {
       
        // Runs after the form fields are populated with their default values.
        
    }
 
    protected function beforeValidate(): void
    {
        //dd($this->data);
        // Runs before the form fields are validated when the form is submitted.
    }
 
    protected function afterValidate(): void
    {
        //dd($this->data['attachment']);
       
        // Runs after the form fields are validated when the form is submitted.
        
    }
 
    protected function beforeCreate(): void
    {
       
        // Runs before the form fields are saved to the database.
    }
 
    protected function afterCreate(): void
    {
        //dd($this->record);
        ////deduct credit leave base on type of leave
        $days_applied = $this->record['days_applied'];
        EmployeeLeaveCredit::where(array('employee_id'=>$this->record['employee_id'],
                                        'leave_type_id'=>$this->record['leave_type_id'])
                                        )
                            ->decrement('credits', $days_applied);
        ///
    }  

     
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //dd($this->data['days_applied']);
        $img = '';
        foreach($this->data['attachment'] as $key => $value){
            $img = $value;
        }
       
        $emp = Employee::select('id')->where('emp_id',auth()->user()->emp_id)->first();
        $data['employee_id']    = $emp['id'];
        $data['status']         = '0';
        $data['days_applied']   = $this->data['days_applied'];
        $data['leave_type_id']  = $this->data['leave_type_id'];
        $data['req_date_from']  = $this->data['req_date_from'];
        $data['req_date_to']    = $this->data['req_date_to'];
        $data['reason']         = strtoupper($this->data['reason']) ;
        $data['date_submitted'] = Carbon::now();
        $data['attachment']     = $img;
        $data['trans_type']     = 0;
        return $data;
        
    }
    


}



