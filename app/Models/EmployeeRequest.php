<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRequest extends Model
{
    use HasFactory;
   
    protected $fillable = ['employee_id',
                           'leave_type_id',
                           'days_applied',
                           'req_date_from',
                           'req_date_to',
                           'reason',
                           'status',
                           'date_submitted',
                           'remarks',
                           'date_submitted',
                           'attachment',
                           'trans_type'
                        ];



    public function leaveType(){
        return $this->belongsTo(LeaveType::class);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function getfullNameAttribute(){
        return $this->firstname.' '.$this->lastname;
    }

   
    public function getdateAppliedAttribute(){
        return $this->req_date_from.' TO '.$this->req_date_to;
    }

    public function getdaysAppAttribute(){
        return number_format($this->days_applied,1);
    }
    
}
