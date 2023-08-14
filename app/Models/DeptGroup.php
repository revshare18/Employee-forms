<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeptGroup extends Model
{
    use HasFactory;
    protected $fillable = ['department_id','name','desc'];

    
    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function employees(){
        return $this->hasMany(User::class,'dept_group_id','id');
    }

}
