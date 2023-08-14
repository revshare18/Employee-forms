<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = ['code','name','desc','default_credit','status','is_visible'];



    public function employeeLeaveCredits(){
        return $this->hasMany(EmployeeLeaveCredit::class);
    }


    public function employeeRequests(){
        return $this->hasMany(Leave::class);
    }



}
