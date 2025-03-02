<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Budget;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BudgetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BudgetResource\RelationManagers;

class BudgetResource extends Resource
{

    protected static ?string $model = Budget::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Define your form fields here
                Forms\Components\TextInput::make('name')
                    ->label('Budget Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('EUR')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required() 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(Budget::query()->where('user_id', auth()->id())) // Filter by user_id
            ->columns([
                // Define your table columns here
                Tables\Columns\TextColumn::make('name')
                    ->label('Budget Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('EUR')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
            ])
            // ->filters([
            //     // Define your filters here
            //     Tables\Filters\Filter::make('active')
            //         ->query(fn (Builder $query) => $query->where('active', true)),
            // ])
            ->actions([
                // Define your actions here
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Define your bulk actions here
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define your relation managers here

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}