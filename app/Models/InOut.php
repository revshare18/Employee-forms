<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InOut extends Model
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
                           'date_applied',
                           'attachment',
                           'trans_type',
                           'time_in',
                           'time_out'
                        ];

    public function employee(){
        return $this->belongsTo(User::class,'employee_id','id');
    }       
    
    public function leaveType(){
        return $this->belongsTo(LeaveType::class);
    }

    public function getfullNameAttribute(){
        return $this->firstname.' '.$this->lastname;
    }

    
    public function getformattedStatusAttribute(){
        /*
        0 - PENDING
        1 - APPROVED BY MANAGER
        2 - APPROVED BY ADMIN
        3 - DECLINED BY MANAGER
        4 - DECLINED BY ADMIN
        5 - CANCELLED
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
