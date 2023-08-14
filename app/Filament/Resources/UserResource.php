<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Department;
use App\Models\Designation;
use App\Models\DeptGroup;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\model_has_roles;
use Illuminate\Database\Eloquent\Model;

use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use App\Models\EmployeeLeaveCredit;
use Illuminate\Validation\Rules\Unique;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater; 
use Filament\Forms\Components\Fieldset;




class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $modelLabel = 'Employee';
    protected static ?string $pluralModelLabel  = 'Employee';

    public static function form(Form $form): Form
    {
       
         return $form
            ->schema([
                Grid::make(1)->schema([
                Tabs::make('Heading')
                    ->tabs([
                    Tabs\Tab::make('Information')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('emp_id')
                                ->required()
                                ->minLength(8)
                                ->maxLength(30)
                                ->disableAutocomplete(),
                                
                                Select::make('role_id')
                                ->label('Role')
                                ->options(function () {
                                    return Role::all()->pluck('name', 'id');
                                })
                                ->afterStateHydrated(function(Closure $set, $record) {
                                    if($record){
                                        $set('role_id',$record->role_id);
                                    }
                                })
                                ->required()
                                //->dehydrated(condition:false)
                                ->placeHolder('Select Role')
                                
                            ]),
                            Grid::make(3)->schema([
                            Select::make('department_id')
                            ->label('Department')
                            ->options(function () {
                                return Department::all()->pluck('name', 'id');
                            })
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('dept_group_id',null))
                            ->required()
                            ->placeHolder('Select Department'),

                            Select::make('dept_group_id')
                            ->label('Group')
                            ->options(function (callable $get) {
                                $dept_group = DeptGroup::where('department_id',$get('department_id'))->pluck('name','id');
                                $data = $dept_group->toArray();
                                if(!$data){
                                    return DeptGroup::all()->pluck('name','id');
                                }
                                return $dept_group;
                            })
                            ->reactive()
                            ->required()
                            ->placeHolder('Select Group'), 
                            Select::make('designation_id')
                            ->label('Designation')
                            ->options(function (callable $get) {
                                $Designation = Designation::where('dept_group_id',$get('dept_group_id'))->pluck('name','id');
                                $data = $Designation->toArray();
                                if(!$data){
                                    return null;
                                    //return Designation::all()->pluck('name','id');
                                }
                                return $Designation; 
                            })
                            ->required()
                            ->reactive()
                            ->placeHolder('Select Designation'), 
                            ]),
                            Grid::make(3)->schema([
                                TextInput::make('name')->required()
                                ->maxLength(30)
                                ->disableAutocomplete(),
                                TextInput::make('middlename')
                                ->required()
                                ->maxLength(30)
                                ->disableAutocomplete(),
                                TextInput::make('lastname')
                                ->required()
                                ->maxLength(30)
                                ->disableAutocomplete(),
                            ]),
                          
                            Grid::make(1)->schema([
                                TextInput::make('email')
                                ->disableAutocomplete()
                                ->required()
                                ->email()
                                ->label('Email Address')
                                ->unique(ignoreRecord: true, callback: function (Unique $rule, callable $get) {
                                    return $rule->where('email', $get('email'));
                                }),
                            ]),
                            Grid::make(3)->schema([
                                DatePicker::make('dob')->label('Date Of Birth')->required(),
                                TextInput::make('contact')
                                ->required()
                                ->numeric()
                                ->minLength(8)
                                ->maxLength(20),
                                TextInput::make('tin')
                                ->label('TIN')
                                ->required()
                                ->minLength(5)
                                ->maxLength(40)
                            ]),
                            Grid::make(1)->schema([
                                Textarea::make('address')->required()->rows(3)
                            ]),

                            
                        ])->columns(2),
                        Tabs\Tab::make('Leave Credits')
                        ->schema([
                            Card::make()
                            ->schema(function(){
                                
                               
                                    $form = [];
                                    $leave = LeaveType::all();
                                    foreach($leave as $ind => $l){
                                        $form[] = 
                                            
                                            Grid::make(2)->schema([ 
                                                Hidden::make( name: 'leave_type_id.'.$ind)
                                                ->required()
                                                ->default($l->id)
                                                ->label('')
                                                ->afterStateHydrated(function(Closure $set, $record)use($l,$ind){
                                                    if($record){ ///edit
                                                        if(@$record->employeeLeaveCredits[$ind]){
                                                            $set('leave_type_id.'.$ind,$record->employeeLeaveCredits[$ind]['leave_type_id']);
                                                        }
                                                    }
                                                }),

                                                TextInput::make( name: 'credits.'.$ind)
                                                ->label('credits')
                                                ->required()
                                                ->numeric()
                                                ->label($l->name)
                                                ->default($l->default_credit)
                                                ->afterStateHydrated(function(Closure $set,$record)use($l,$ind){
                                                    if($record){
                                                        if(@$record->employeeLeaveCredits[$ind]){
                                                            $set('credits.'.$ind,$record->employeeLeaveCredits[$ind]['credits']);
                                                        }
                                                    }
                                                })
                                            ])->inlineLabel();
                                    }
                                    return $form;
                            })  
                        ])
                    ])
                ])
            ]);



    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->hidden(true),
                TextColumn::make('emp_id')->label('Employee ID')->sortable()->searchable(),
                TextColumn::make('name')->label('First Name')->sortable()->searchable(),
                TextColumn::make('middlename')->label('Middle Name')->sortable()->searchable(),
                TextColumn::make('lastname')->label('Last Name')->sortable()->searchable(),
                TextColumn::make('email')->label('Email')->sortable()->searchable(),
                TextColumn::make('department.name')->label('Department')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make()->using(function (Model $record, array $data): Model {
                    $record->update($data);
                    model_has_roles::where('model_id',$record->id)
                    ->update(['role_id' => $data['role_id']]);
                    
                    return $record;
                }) ,
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }  
    
    public static function getEloquentQuery(): Builder
    {   
        return parent::getEloquentQuery()->where('id','!=', 1);
    }
}
