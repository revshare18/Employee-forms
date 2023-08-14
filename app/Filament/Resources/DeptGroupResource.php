<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeptGroupResource\Pages;
use App\Filament\Resources\DeptGroupResource\RelationManagers;
use App\Models\DeptGroup;
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

use App\Models\Department;
class DeptGroupResource extends Resource
{
    protected static ?string $model = DeptGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Maintenance';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('department_id')
            ->label('Department')
            ->options(function () {
                return Department::all()->pluck('name', 'id');
            })
            ->required()
            ->searchable()
            ->disablePlaceholderSelection(),
            TextInput::make('name')->label('Name')->required()->unique(ignoreRecord: true),
            TextInput::make('desc')->label('Description')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('department.name')->label('Department')->sortable()->searchable(),
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('desc')->label('Description')->sortable()->searchable(),
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
            'index' => Pages\ManageDeptGroups::route('/'),
        ];
    }    
}
