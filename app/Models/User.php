<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];
    */

    protected $fillable = ['created_by','updated_by','password','role_id','tin','dept_group_id','department_id','designation_id','emp_id','name','middlename','lastname','dob','contact','email','address'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ( $value) => (!is_null($value)) ? \Hash::make($value) : '',
        );
    }



    public function department(){
        return $this->belongsTo(Department::class,'department_id','id');
    }

    public function designation(){
        return $this->belongsTo(Designation::class,'designation_id','id');
    }

    public function deptGroup(){
        return $this->belongsTo(DeptGroup::class,'dept_group_id','id');
    }

    public function employeeLeaveCredits(){
        return $this->hasMany(EmployeeLeaveCredit::class,'employee_id','id');
    }

    public function getFullNameAttribute(){
        return $this->name.' '.$this->lastname;
    }

    public function model_roles(){
        return $this->hasOne(model_has_roles::class,'model_id','id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }


    public static function boot() {
        parent::boot();
        self::deleting(function($user) { // before delete() method call this
             $user->employeeLeaveCredits()->each(function($credit) {
                $credit->delete(); 
             });
            
        });
    }
   

}
