<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Filament\Resources\HolidayResource\RelationManagers;
use App\Models\Holiday;
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
use App\Models\Department;
use App\Models\Designation;
use App\Models\DeptGroup;
use App\Models\LeaveType;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use App\Models\EmployeeLeaveCredit;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\BooleanColumn;



class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Maintenance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->inlineLabel()
                        ->unique(ignoreRecord: true, callback: function (Unique $rule, callable $get) {
                            return $rule->where('name', $get('name'));
                        }),
                        DatePicker::make('actual_date')
                        ->label('Date')
                        ->required()
                        ->inlineLabel()
                    ]),
                    Grid::make(1)
                    ->schema([
                        Toggle::make('repeating')->label('Occurs Yearly'),
                        Hidden::make('sync')->default('0')
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('name')->label('Holiday')->sortable()->searchable(),
                TextColumn::make('actual_date')->label('Date')->sortable()->searchable(),
                BooleanColumn::make('repeating')->label('Repeat Yearly')->sortable()->searchable(),
                TextColumn::make('created_at')->label('timestamp')->sortable()->searchable(),
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
            'index' => Pages\ManageHolidays::route('/'),
        ];
    }    
}
