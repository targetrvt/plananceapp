<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserBalanceResource\Pages;
use App\Models\UserBalance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserBalanceResource extends Resource
{
    protected static ?string $model = UserBalance::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'My Balance';
    protected static ?int $navigationSort = -3;
    
    public static function getNavigationBadge(): ?string
    {
        $userBalance = UserBalance::where('user_id', auth()->id())->first();
        if ($userBalance) {
            return number_format($userBalance->balance, 2) . ' €';
        }
        return '0.00 €';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('balance')
                    ->label('Your Balance')
                    ->required()
                    ->numeric()
                    ->prefix('EUR')
                    ->helperText('Enter your current financial balance'),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
                Forms\Components\Hidden::make('currency')
                    ->default('EUR'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(UserBalance::query()->where('user_id', auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('balance')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListUserBalances::route('/'),
            'create' => Pages\CreateUserBalance::route('/create'),
            'edit' => Pages\EditUserBalance::route('/{record}/edit'),
        ];
    }
}
