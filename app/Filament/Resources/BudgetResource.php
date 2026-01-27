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
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;
    
    public static function getNavigationLabel(): string
    {
        return __('budget.navigation.label');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('budget.navigation.label');
    }
    
    public static function getModelLabel(): string
    {
        return __('budget.navigation.label');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Management'; // Must match the group name registered in AppPanelProvider
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Define your form fields here
                Forms\Components\TextInput::make('name')
                    ->label(__('budget.form.name.label'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label(__('budget.form.amount.label'))
                    ->numeric()
                    ->prefix('EUR')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('budget.form.start_date.label'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('budget.form.end_date.label'))
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
                    ->label(__('budget.table.name.label'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('budget.table.amount.label'))
                    ->numeric()
                    ->money('EUR')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('budget.table.start_date.label'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('budget.table.end_date.label'))
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