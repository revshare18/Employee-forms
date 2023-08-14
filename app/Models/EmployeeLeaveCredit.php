<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveCredit extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id','leave_type_id','credits'];
    


    public function employee(){
        return $this->belongsTo(User::class,'employee_id','id');
    }    

    public function leaveType(){
        return $this->belongsTo(LeaveType::class);
    }


     

}
