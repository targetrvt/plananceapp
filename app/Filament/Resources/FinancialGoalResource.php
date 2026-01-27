<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialGoalResource\Pages;
use App\Filament\Resources\FinancialGoalResource\RelationManagers;
use App\Models\FinancialGoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinancialGoalResource extends Resource
{
    protected static ?string $model = FinancialGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;
    
    public static function getNavigationLabel(): string
    {
        return __('financial-goal.navigation.label');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('financial-goal.navigation.label');
    }
    
    public static function getModelLabel(): string
    {
        return __('financial-goal.navigation.label');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Management'; // Must match the group name registered in AppPanelProvider
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('financial-goal.form.name.label'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('target_amount')
                    ->label(__('financial-goal.form.target_amount.label'))
                    ->required()
                    ->numeric()
                    ->prefix('EUR'),
                Forms\Components\TextInput::make('current_amount')
                    ->label(__('financial-goal.form.current_amount.label'))
                    ->numeric()
                    ->prefix('EUR')
                    ->default(0),
                Forms\Components\DatePicker::make('target_date')
                    ->label(__('financial-goal.form.target_date.label'))
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label(__('financial-goal.form.notes.label'))
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(FinancialGoal::query()->where('user_id', auth()->id())) // Filter by user_id
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('financial-goal.table.name.label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_amount')
                    ->label(__('financial-goal.table.current_amount.label'))
                    ->money('EUR')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('target_amount')
                    ->label(__('financial-goal.table.target_amount.label'))
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label(__('financial-goal.table.progress.label'))
                    ->getStateUsing(function ($record) {
                        if ($record->target_amount == 0) {
                            return '0%'; // Avoid division by zero
                        }
                        return round(($record->current_amount / $record->target_amount) * 100, 2) . '%';
                    }),
                Tables\Columns\TextColumn::make('target_date')
                    ->label(__('financial-goal.table.target_date.label'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('target_date')
                    ->label(__('financial-goal.filter.target_date.label'))
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('financial-goal.filter.target_date.from.label')),
                        Forms\Components\DatePicker::make('until')->label(__('financial-goal.filter.target_date.until.label')),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($query) => $query->whereDate('target_date', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn($query) => $query->whereDate('target_date', '<=', $data['until'])
                            );
                    }),
            ])
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialGoals::route('/'),
            'create' => Pages\CreateFinancialGoal::route('/create'),
            'edit' => Pages\EditFinancialGoal::route('/{record}/edit'),
        ];
    }
}