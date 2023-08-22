<?php

/**
 * Consider this file the root configuration object for FullCalendar.
 * Any configuration added here, will be added to the calendar.
 * @see https://fullcalendar.io/docs#toc
 */

return [
    'timeZone' => config('app.timezone'),

    'locale' => config('app.locale'),

    'headerToolbar' => [
        'left' => 'prev,next today,testButton',
        'center' => 'title',
        'right' => 'dayGridMonth,dayGridWeek,dayGridDay',
    ],

    'customButtons' => [
        'testButton' => [
          'text' => 'custom!',
           'click' =>  function(){   
            echo "console.log('test')";
            }
        ]
        ],
    
    'navLinks' => false,
    'scrollTimeReset' => false,

    'editable' => false,

    'selectable' => false,

    'dayMaxEvents' => true,
];
