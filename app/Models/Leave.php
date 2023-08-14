<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $table = 'employee_requests';
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


    public function getdateAppliedAttribute(){
        return $this->req_date_from.' TO '.$this->req_date_to;
    }

    public function getfullNameAttribute(){
        return $this->firstname.' '.$this->lastname;
    }

    public function getdaysAppAttribute(){
        return number_format($this->days_applied,1);
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
