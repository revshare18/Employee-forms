<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveTypeResource\Pages;
use App\Filament\Resources\LeaveTypeResource\RelationManagers;
use App\Models\LeaveType;
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
use Filament\Forms\Components\Toggle;


class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Maintenance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')->label('Code')->maxLength(5)->autofocus()->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('Name')->required()->unique(ignoreRecord: true),
                TextInput::make('desc')->label('Description'),
                TextInput::make('default_credit')->numeric()->label('Credit')->required(),
                Toggle::make('is_visible')->label('Visible Credit Counts')->default(true)->inline(),
                Select::make('status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'In-Active'
                    ])->label('Status')->default('1')->disablePlaceholderSelection()->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('code')->label('Code')->sortable()->searchable(),
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('desc')->label('Description')->sortable()->searchable(),
                TextColumn::make('default_credit')->label('Credit')->sortable(),
                //TextColumn::make('status')->label('Status')->sortable(),
                BooleanColumn::make('is_visible')->label('Visible Credit Counts')->searchable(),
                BooleanColumn::make('status')->searchable(),  
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['code'] = strtoupper($data['code']);
                    $data['name'] = strtoupper($data['name']);
                    $data['desc'] = strtoupper($data['desc']);
            
                    return $data;
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeaveTypes::route('/'),
        ];
    }    
}
