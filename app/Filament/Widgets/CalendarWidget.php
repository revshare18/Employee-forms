<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Filament\Forms\Components\Grid;
use App\Models\Leave;
use Carbon\Carbon;
class CalendarWidget extends FullCalendarWidget
{
    /**
     * Return events that should be rendered statically on calendar.
     */
    
    
    public function getViewData(): array{
        $schedules = Leave::select('id','req_date_from','req_date_to','leave_type_id')
                    ->where(['trans_type' => '0'])
                    ->where('status','!=',5)
                    ->with(['leaveType:id,code'])
                    ->get();
        
        $sched = [];
        foreach($schedules as $s){
            $sched[] = [
                        'id'    => $s['id'],
                        'title' => $s->leaveType->code.' Baltazar',
                        'start' => $s['req_date_from'],
                        'end'   => $s['req_date_to'].' 01:00:00',
                        'displayEventTime' => false,
                       
                    ];
        }
        //exit;
        //dd($sched);
        return $sched;
    }


    public static function canView(?array $event = null): bool
    {
        // When event is null, MAKE SURE you allow View otherwise the entire widget/calendar won't be rendered
        if ($event === null) {
            return true;
        }
        // Returning 'false' will not show the event Modal.
        return false;
    }


    public static function canCreate(): bool
    {
    // Returning 'false' will remove the 'Create' button on the calendar.
    return false;
    }

    public static function canEdit(?array $event = null): bool
    {
        // Returning 'false' will disable the edit modal when clicking on a event.
        return false;
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        //print_r($fetchInfo);
        // You can use $fetchInfo to filter events by date.
        //dd($fetchInfo);
        return [];
    }
}