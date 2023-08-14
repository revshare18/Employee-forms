<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name','desc','status','created_by','updated_by','created_at','updated_at'];


    public function deptGroups(){
        return $this->hasMany(DeptGroup::class);
    }

    public function designations(){
        return $this->hasMany(Designation::class);
    }


    public function employees(){
        return $this->hasMany(User::class,'department_id','id');
    } 


}
