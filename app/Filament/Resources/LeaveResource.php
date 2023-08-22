<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\LeaveResource\RelationManagers;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Holiday;
use App\Models\EmployeeLeaveCredit;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Illuminate\Support\Str;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;


use Filament\Forms\Components\DatePicker;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\TemporaryUploadedFile;
use Filament\Notifications\Notification;

use App\Filament\Resources\LeaveResource\Widgets\LeaveOverview;



use Filament\Tables\Filters\Layout;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'My Forms';
    protected static ?string $navigationLabel = 'Leave';
    protected static ?string $pluralModelLabel  = 'Leave';

    protected static ?string $role = '';
    protected static ?string $approver = '';
    protected static ?string $dept_group_id = '';
    protected static ?string $emp_id = '';
    protected static ?string $fullname = '';
    protected static ?string $department = '';
    protected static ?string $email = ''; 
    
    protected $queryString = [
        'tableFilters'
    ];

    public static function getEmployeeDetails(){   
        $emp = User::where('emp_id',auth()->user()->emp_id)->with(['department:departments.id,departments.name','role:roles.id,roles.name','designation:designations.id,is_approver,dept_group_id'])->first(); 
        
        self::$role          = @$emp->role->name;
        self::$approver      = @$emp->designation->is_approver;
        self::$dept_group_id = @$emp->designation->dept_group_id;
        self::$emp_id        = @$emp['id'];
        self::$department    = @$emp->department->name;
        self::$fullname      = @$emp['firstname'].' '.$emp['lastname'];
        self::$email         = @$emp['email'];
    }

    public static function compute_date($days_applied,$req_date_from,$allow_weekends,$Holiday){

        $no_days =  ($days_applied != '' ) ? round($days_applied) : 0;
        $from = date('Y-m-d',strtotime($req_date_from));
        $from_tmp = date('Y-m-d',strtotime($req_date_from."-1 days"));
        
        $range_check = $no_days + 10;
        $to = date('Y-m-d', strtotime($from . " +".$range_check." days")); 
        $period = CarbonPeriod::create($from, $to)
        ->filter(function ($period) { return $period->isSunday() || $period->isSaturday(); });

        foreach ($period as $date) { $dates[] = $date->format("Y-m-d"); }
        $d = ($allow_weekends) ? $Holiday : array_merge($Holiday, $dates);
        sort($d);
        
        while($no_days > 0){
            $NewDate = date('Y-m-d', strtotime($from_tmp . " +1 days"));
            while (in_array($NewDate, $d) ) { 
                $NewDate = date('Y-m-d', strtotime($NewDate . " +1 days"));  
            }
            $from_tmp = $NewDate;
            $no_days--;
        }
        return $NewDate;

    }


    public static function form(Form $form): Form
    {
        $Holiday = Holiday::all()->pluck('actual_date')->toArray();
        return $form
            ->schema([
                
                Card::make()
                ->schema([

                    Grid::make(2)->schema(function(Closure $set){
                        $fullname = '';
                        if(auth()->user()->role_id != 1){
                            $emp = User::where('emp_id',auth()->user()->emp_id)->first();
                            $department = $emp->department;
                            $fullname = $emp['firstname'].' '.$emp['lastname'];
                            $set('Department',$department['name']);
                            $set('Email',$emp['email']);
                        }
                        
                        $form[] = TextInput::make('Employee_name')
                        ->label('Employee name')
                        ->default($fullname)
                        ->required()
                        ->disabled()
                        ->dehydrated(condition:false)
                        ->inlineLabel();
                        return $form;
                    }),
                    Grid::make(2)->schema([
                        TextInput::make('Department')
                        ->label('Department')
                        ->required()
                        ->dehydrated(condition:false)
                        ->disabled()
                        ->inlineLabel(),

                        Hidden::make('employee_id')
                        ->afterStateHydrated(function(Closure $set){
                            $emp = User::select('id')->where('emp_id',auth()->user()->emp_id)->first(); 
                            $set('employee_id',$emp['id']);
                        }),
                        Hidden::make('status')->default(0),
                        Hidden::make('date_submitted')->default(date('Y-m-d'))
                        
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('Email')
                        ->label('Email')
                        ->required()
                        ->dehydrated(condition:false)
                        ->disabled()
                        ->inlineLabel()
                        
                    ]),
                    Grid::make(2)->schema([
                        Select::make('leave_type_id')
                        ->label('Leave Type')
                        ->options(function () {
                            $arr = null;
                            if(auth()->user()->role_id != 1){
                                $emp = User::where('emp_id',auth()->user()->emp_id)->first();
                                $leave_cred = EmployeeLeaveCredit::leftJoin('leave_types', function($join) {
                                    $join->on('leave_types.id', '=', 'employee_leave_credits.leave_type_id');
                                })
                                ->select('credits','name','leave_types.id as id','leave_types.is_visible')
                                ->where('employee_leave_credits.employee_id',$emp['id'])
                                ->where('credits','>',0)
                                ->get();
                                $arr = [];
                                foreach($leave_cred as $l){
                                    $append = ($l['is_visible']) ? ' ('.$l['credits'].')' : '';
                                    $arr[$l['id']] = $l['name'] .$append;
                                    //$arr[$l['id']] = $l['name'] .' ('.$l['credits'].')';
                                }
                            }
                            
                            return $arr;
                        })
                        ->reactive()
                        ->required()
                        ->placeHolder('Select Leave Type')
                        ->inlineLabel()
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('days_applied')
                        ->label('Day(s)')
                        //->minLength(1)
                        ->minValue(0.5)
                        ->maxValue(10)
                        ->required()
                        ->numeric()
                        ->inlineLabel()
                        ->afterStateUpdated(function(callable $get,$set) {
                            $set('req_date_from',null);
                            $set('req_date_to',null);
                        })
                        ->reactive(),
                       
                        Checkbox::make('allow_weekends')
                        ->label('Weekends / Holidays')
                        ->afterStateUpdated(function(callable $get,$set) use($Holiday) {
                            $NewDate = self::compute_date($get('days_applied'),$get('req_date_from'),$get('allow_weekends'),$Holiday);
                            $from = date('Y-m-d',strtotime($get('req_date_from')));
                            $set('req_date_from',$from);
                            $set('req_date_to',$NewDate);
                        })
                        ->reactive()
                    ]),
                    Grid::make(4)->schema([
                        DatePicker::make('req_date_from')
                        ->label('From')
                        ->required()
                        ->reactive()
                        ->disabled(function(Closure $get){
                            return ($get('days_applied') != '') ? false : true;
                        })
                        ->afterStateUpdated(function(callable $get,$set) use($Holiday) {
                            $no_days =  ($get('days_applied') != '' ) ? round($get('days_applied')) : 0;
                            $from = date('Y-m-d',strtotime($get('req_date_from')));
                            $NewDate = self::compute_date($get('days_applied'),$get('req_date_from'),$get('allow_weekends'),$Holiday);
                            $set('req_date_from',$from);
                            $set('req_date_to',$NewDate);
                        })
                        ->disabledDates( function (callable $get) use($Holiday)  {
                               $allow_weekend = $get('allow_weekends');
                               $value = date('Y-m-d');
                               $before_date = date('Y-m-d', strtotime($value . " -1 days"));
                               
                               $NewDate = date('Y-m-d', strtotime($value . " +180 days")); 
                               $period = CarbonPeriod::create($value,$NewDate)->filter(function ($period) {
                                    return $period->isSunday() || $period->isSaturday();
                                });
                                
                                foreach ($period as $date) { $dates[] = $date->format("Y-m-d");  }
                                $dates[] = $before_date;
                                $d = ($allow_weekend) ? $Holiday : array_merge($Holiday, $dates);
                                return $d;
                        })
                        //
                        ->minDate(Carbon::now()->subDay())
                        ->maxDate(Carbon::now()->addMonths(6))
                        ->inlineLabel()
                        
                        ->format('Y-m-d'),

                        DatePicker::make('req_date_to')
                        ->label('To')
                        ->required()
                        ->disabled()
                        ->inlineLabel()
                    ]),
                    Grid::make(1)->schema([
                        Textarea::make('reason')->required()->rows(3)
                    ]),
                    Grid::make(1)->schema([ 
                       
                        FileUpload::make('attachment')
                        ->disk('employee_images')
                        ->maxSize(3200)
                        ->acceptedFileTypes(['application/pdf','image/*'])
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                            $f = explode('.',strrev($file->getClientOriginalName()));
                            $ext = strrev($f[0]);
                            $filename = time().str_shuffle('12345abcde').'.'.$ext;
                            return (string) str($filename)->prepend('Employee-');

                        })
                        ->enableDownload()
                        ->enableOpen()
                        ->preserveFilenames(),
                      
                        Placeholder::make('')
                        ->content('Please attach your medical certificate / prescription image in case of sick / emergency leave.')
                    ]),
                   
                ])
                ->columns(2)
            ]);
    }


    public static function getEloquentQuery(): Builder{

        //dd(Leave::all());
        $emp = self::getEmployeeDetails();  
        $role = self::$role; 
        $dept_group_id = self::$dept_group_id;
        $array = (self::$approver == 1 || self::$role == 'super_admin' ) ? ['trans_type' => 0 ] : ['trans_type' => 0, 'employee_id' => self::$emp_id];

        $query = Leave::whereHas('employee', function ($query) use($dept_group_id,$role) {
            $operator = ($role == 'super_admin') ? '!=' : '=';
            $value_dept_group = (self::$role == 'super_admin') ? 0 : $dept_group_id;    
            return $query->where('dept_group_id', $operator, $value_dept_group);
        })->where($array); 
        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('id')->label('ID')->sortable()->searchable(),
                 TextColumn::make('employee.fullname')->label('NAME')->sortable()->searchable(),
                 TextColumn::make('leaveType.name')->label('LEAVE TYPE')->sortable()->searchable(),
                 TextColumn::make('date_applied')->label('DATE')->sortable()->searchable(),
                 TextColumn::make('daysApp')->label('DAYS APPLIED')->sortable()->searchable(),
                 TextColumn::make('reason')->label('REASON')->sortable()->searchable(),
                 TextColumn::make('formattedStatus')->label('STATUS')->sortable()->searchable(),
                 TextColumn::make('date_submitted')->label('DATE SUBMITTED')->sortable()->searchable(),
                 TextColumn::make('date_marked_manager')->label('DATE MARKED (MANAGER)')->sortable()->searchable(),
                 TextColumn::make('date_marked_admin')->label('DATE MARKED (ADMIN)')->sortable()->searchable(),
                 TextColumn::make('remarks')->label('REMARKS')->sortable()->searchable(),
                 TextColumn::make('attachment')->label('ATTACHMENT')
            ])
            ->filters([
                SelectFilter::make('Leave_type')
                ->options(function () {
                    return LeaveType::all()->pluck('name', 'id');
                })
                ->attribute('leaveType.name')->label('Leave Type')  
            ],layout: Layout::AboveContent)
            ->actions([
                
                Tables\Actions\Action::make('approve')->label('')->action(function ($record){
                    $field = (self::$role == 'super_admin') ? 'date_marked_admin' : 'date_marked_manager';
                    $status = (self::$role == 'super_admin') ? '2' : '1';
                    $record->status = $status;
                    $record->$field = Carbon::now();
                    $record->update();

                    Notification::make() 
                    ->title('Approved Successfully')
                    ->success()
                    ->send(); 
                })->icon('heroicon-s-check-circle')
                ->requiresConfirmation()
                ->modalHeading('')
                ->modalSubheading('Are you sure you\'d like to approve?')
                ->modalButton('Yes')
                ->hidden(function($record){
                    if(self::$role == 'super_admin'){
                        $return_value = is_null($record->date_marked_admin) && $record->status != 5 ? false : true;
                    }else{
                        $return_value = (is_null($record->date_marked_manager) && is_null($record->date_marked_admin) && self::$approver == 1 && $record->employee_id != self::$emp_id && $record->status != 5) ? false : true;
                    }
                    return $return_value;
                }),
                Tables\Actions\Action::make('decline')->label('')
                ->action(function (array $data ,$record){
                    $field = (self::$role == 'super_admin') ? 'date_marked_admin' : 'date_marked_manager';
                    $status = (self::$role == 'super_admin') ? '4' : '3';

                    $record->status = $status;
                    $record->remarks = $data['remarks'];
                    $record->$field = Carbon::now();
                    $record->update();

                    Notification::make() 
                    ->title('Declined Successfully')
                    ->success()
                    ->send(); 
                })
                ->icon('heroicon-s-x-circle')
                ->requiresConfirmation()
                ->modalHeading('')
                ->modalSubheading('Are you sure you\'d like to decline?')
                ->modalButton('Yes')
                ->form([
                    Forms\Components\Textarea::make('remarks')
                    ->label('REMARKS')
                ])
                ->hidden(function($record){
                    if(self::$role == 'super_admin'){
                        $return_value = is_null($record->date_marked_admin) && $record->status != 5 ? false : true;
                    }else{
                        $return_value = (is_null($record->date_marked_manager) && is_null($record->date_marked_admin) 
                                        && self::$approver == 1 && $record->employee_id != self::$emp_id && $record->status != 5) ? false : true;
                    }
                    return $return_value;
                }),
                Tables\Actions\Action::make('cancel')->label('')
                ->action(function (array $data ,$record){
                    $role = self::$role; 
                    $status =  '5';

                    $record->status = $status;
                    $record->update();

                    Notification::make() 
                    ->title('Cancel Successfully')
                    ->success()
                    ->send(); 
                })
                ->icon('heroicon-s-x-circle')
                ->requiresConfirmation()
                ->modalHeading('')
                ->modalSubheading('Are you sure you\'d like to cancel?')
                ->modalButton('Yes')
                
                ->hidden(function($record){
                    $emp_id = self::$emp_id;
                    $return_value = (is_null($record->date_marked_manager) && is_null($record->date_marked_admin) && $record->employee_id == $emp_id && $record->status != 5) ? false : true;
                    return $return_value;
                })
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            //'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }  




    public static function getWidgets() : array
    {
        return [
            LeaveOverview::class,
        ];
    }
    
    

    

}
