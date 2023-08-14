<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InOutResource\Pages;
use App\Filament\Resources\InOutResource\RelationManagers;
use App\Models\InOut;
use App\Models\User;
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
use Filament\Forms\Components\TimePicker;
use Savannabits\Flatpickr\Flatpickr;
use Filament\Notifications\Notification;

class InOutResource extends Resource
{
    protected static ?string $model = InOut::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'My Forms';
    protected static ?string $navigationLabel = 'In-Out';
    protected static ?string $pluralModelLabel  = 'In-Out';


    protected static ?string $role = '';
    protected static ?string $approver = '';
    protected static ?string $dept_group_id = '';
    protected static ?string $emp_id = '';
    protected static ?string $fullname = '';
    protected static ?string $department = '';
    protected static ?string $email = '';    


    public static function getEmployeeDetails(){   
        $emp = User::where('emp_id',auth()->user()->emp_id)
        ->with(['department:departments.id,departments.name',
                'role:roles.id,roles.name',
                'designation:designations.id,is_approver,dept_group_id'])->first(); 
                
        self::$role          = $emp->role->name;
        self::$approver      = @$emp->designation->is_approver;
        self::$dept_group_id = @$emp->designation->dept_group_id;
        self::$emp_id        = @$emp['id'];
        self::$department    = @$emp->department->name;
        self::$fullname      = @$emp['firstname'].' '.$emp['lastname'];
        self::$email         = @$emp['email'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
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
                        DatePicker::make('date_applied')
                        ->label('Inclusive Date')
                        ->required()
                        ->inlineLabel()
                        ->minDate(Carbon::now()->subMonth())
                        ->maxDate(Carbon::now())
                        ->default(Carbon::now())
                    ]),
                    
                    Grid::make(2)->schema([
                        //TimePicker::make('time_in')->label('Actual Time In'),
                        TimePicker::make('time_in')
                        ->format('h:i A')
                        ->displayFormat('h:i:s a')
                        ->withoutSeconds()
                        ->reactive(),
                        //Flatpickr::make('time_in')->default(now())->enableTime()->altInput(true)->altFormat('Y F J '),
                        TimePicker::make('time_out')->label('Actual Time Out')
                        ->withoutSeconds()
                        ->format('h:i A')
                        ->displayFormat('h:i:s a')
                        ->reactive()
                    ]),

                    
                    Grid::make(1)->schema([ 
                        Textarea::make('reason')->required()->label('REASON/S   ')->rows(3)
                    ]),
                   
                ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        /*
        
        time in
        time out
        reason
        status
        remarks
        date submitted
        date marked manager
        date marked admin
        */
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('employee.fullname')->label('NAME')->sortable()->searchable(),
                TextColumn::make('date_applied')->label('INCLUSIVE DATE')->sortable()->searchable(),
                TextColumn::make('time_in')->label('TIME IN')->sortable()->searchable(),
                TextColumn::make('time_out')->label('TIME OUT')->sortable()->searchable(),
                TextColumn::make('reason')->label('REASON')->sortable()->searchable(),
                TextColumn::make('formattedStatus')->label('STATUS')->sortable()->searchable(),
                TextColumn::make('remarks')->label('REMARKS')->sortable()->searchable(),
                TextColumn::make('date_submitted')->label('DATE SUBMITTED')->sortable()->searchable(),
                TextColumn::make('date_marked_manager')->label('DATE MARKED (MANAGER)')->sortable()->searchable(),
                TextColumn::make('date_marked_admin')->label('DATE MARKED (ADMIN)')->sortable()->searchable(),
                TextColumn::make('remarks')->label('REMARKS')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')->label('')->action(function ($record){
                    $role = self::$role; 
                    $field = ($role == 'super_admin') ? 'date_marked_admin' : 'date_marked_manager';
                    $status = ($role == 'super_admin') ? '2' : '1';
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
                    $role = self::$role; 
                    $approver = self::$approver;
                    $emp_id = self::$emp_id;
                    if($role == 'super_admin'){
                        $return_value = is_null($record->date_marked_admin) && $record->status != 5 ? false : true;
                    }else{
                        $return_value = (is_null($record->date_marked_manager) && is_null($record->date_marked_admin) && 
                                        $approver == 1 && $record->employee_id != $emp_id && $record->status != 5) ? false : true;
                    }
                    return $return_value;
                }),
                Tables\Actions\Action::make('decline')->label('')
                ->action(function (array $data ,$record){
                    $role = self::$role; 
                    $field = ($role == 'super_admin') ? 'date_marked_admin' : 'date_marked_manager';
                    $status = ($role == 'super_admin') ? '4' : '3';

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
                    
                    $role = self::$role; 
                    $approver = self::$approver;
                    $emp_id = self::$emp_id;
                    if($role == 'super_admin'){
                        $return_value = is_null($record->date_marked_admin) && $record->status != 5 ? false : true;
                    }else{
                        
                        $return_value = (is_null($record->date_marked_manager) && is_null($record->date_marked_admin) &&
                                         $approver == 1 && $record->employee_id != $emp_id && $record->status != 5) ? false : true;
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

  

    public static function getEloquentQuery(): Builder{
        $emp = self::getEmployeeDetails();  
        $role = self::$role; 
        $dept_group_id = self::$dept_group_id;
        $array = (self::$approver == 1 || self::$role == 'super_admin' ) ? ['trans_type' => 2 ] : ['trans_type' => 2, 'employee_id' => self::$emp_id];

        //dd($array);
        $query = InOut::whereHas('employee', function ($query) use($dept_group_id,$role) {
            $operator = ($role == 'super_admin') ? '!=' : '=';
            $value_dept_group = (self::$role == 'super_admin') ? 0 : $dept_group_id;    
            return $query->where('dept_group_id', $operator, $value_dept_group);
        })->where($array); 
        return $query;
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
            'index' => Pages\ListInOuts::route('/'),
            'create' => Pages\CreateInOut::route('/create'),
            //'edit' => Pages\EditInOut::route('/{record}/edit'),
        ];
    }    
}
