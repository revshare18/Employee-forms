<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesignationResource\Pages;
use App\Filament\Resources\DesignationResource\RelationManagers;
use App\Models\Designation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\BooleanColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\Toggle;

use App\Models\Department;
use App\Models\DeptGroup;

class DesignationResource extends Resource
{
    protected static ?string $model = Designation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Maintenance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                ///
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
                    $DeptGroup = DeptGroup::where('department_id',$get('department_id'));
                    if(! $DeptGroup){
                        return DeptGroup::all()->pluck('name','id');
                    }
                    return $DeptGroup->pluck('name','id');
                })
                ->required()
                
                ->placeHolder('Select Group'),
                ///
                //TextInput::make('name')->label('Name')->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('Name')
                    ->disableAutoComplete()
                    ->required()
                    ->unique(ignoreRecord: true, callback: function (Unique $rule, callable $get) {
                        return $rule->where('name', $get('name'))->where('dept_group_id',$get('dept_group_id'));
                    }),
                TextInput::make('desc')->label('Description'),
                Toggle::make('is_approver')->label('Approver')->inline(),
                Select::make('status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'In-Active'
                    ])->label('Status')->disablePlaceholderSelection()->default('1')->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('department.name')->label('Department')->sortable()->searchable(),
                TextColumn::make('deptGroup.name')->label('Group')->sortable()->searchable(),
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('desc')->label('Description')->sortable()->searchable(),
                //TextColumn::make('status')->label('Status')->sortable(),
                BooleanColumn::make('status')->searchable(),  
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDesignations::route('/'),
        ];
    }    
}
