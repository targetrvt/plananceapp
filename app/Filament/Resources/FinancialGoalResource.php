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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinancialGoalResource extends Resource
{
    protected static ?string $model = FinancialGoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('target_amount')
                    ->required()
                    ->numeric()
                    ->prefix('EUR'),
                Forms\Components\TextInput::make('current_amount')
                    ->numeric()
                    ->prefix('EUR')
                    ->default(0),
                Forms\Components\DatePicker::make('target_date')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_amount')
                    ->money('EUR'),
                    Tables\Columns\TextColumn::make('progress')
                    ->getStateUsing(function ($record) {
                        if ($record->target_amount == 0) {
                            return '0%'; // Avoid division by zero
                        }
                        return round(($record->current_amount / $record->target_amount) * 100, 2) . '%';
                    }),
                Tables\Columns\TextColumn::make('target_date')
                    ->date(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
