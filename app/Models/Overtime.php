<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Overtime extends Model
{
    protected $table = 'employee_requests';
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
                           'trans_type',
                           'date_applied',
                           'time_in',
                           'time_out',
                           'OT_in',
                           'OT_out',
                           'total_hours',
                           'task'

                        ];

    public function employee(){
        return $this->belongsTo(User::class,'employee_id','id');
    } 


    public function designation(){
        return $this->hasManyThrough(
            Designation::class,
            Employee::class,
            'id', // Foreign key on the types table...
            'id', // Foreign key on the items table...
            'employee_id', // Local key on the users table...
            'designation_id' // Local key on the categories table...
        );
       
    }
                        
    public function getfullNameAttribute(){
        return $this->firstname.' '.$this->lastname;
    }

    
    public function getoverTimeInOutAttribute(){
        $OT_in  = Carbon::parse($this->OT_in)->format('h:i A');
        $OT_out = Carbon::parse($this->OT_out)->format('h:i A');
        return $OT_in.' TO '.$OT_out;
    }
    public function gettimeINOUTAttribute(){
        $time_in  = Carbon::parse($this->time_in)->format('h:i A');
        $time_out = Carbon::parse($this->time_out)->format('h:i A');

        return $time_in.' TO '.$time_out;
    }

    

    public function getformattedStatusAttribute(){
        /*
        0 - PENDING
        1 - APPROVED BY MANAGER
        2 - APPROVED BY ADMIN
        3 - DECLINED BY MANAGER
        4 - DECLINED BY ADMIN
        */
        switch($this->status){
            case "0":
                $st = 'PENDING';     
            break;
            case "1":
                $st = 'APPROVED BY MANAGER';     
            break;
            case "2":
                $st = 'APPROVED BY ADMIN';     
            break;
            case "3":
                $st = 'DECLINED BY MANAGER';     
            break;
            case "4":
                $st = 'DECLINED BY ADMIN';     
            break;
            case "5":
                $st = 'CANCELLED';     
            break;
                
            default:
                $st = '';   
        }
        return $st;
    }                     
}
