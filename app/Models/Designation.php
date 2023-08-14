<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;
    protected $fillable = ['department_id','dept_group_id','name','desc','status','is_approver'];

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function deptGroup(){
        return $this->belongsTo(DeptGroup::class);
    }

    public function employees(){
        return $this->hasMany(User::class,'designation_id','id');
    }


}
