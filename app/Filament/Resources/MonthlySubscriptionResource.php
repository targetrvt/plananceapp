<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlySubscriptionResource\Pages;
use App\Models\MonthlySubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MonthlySubscriptionResource extends Resource
{
    protected static ?string $model = MonthlySubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Subscription Name'),
                            
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('EUR')
                            ->label('Monthly Amount'),
                            
                        Forms\Components\DatePicker::make('billing_date')
                            ->required()
                            ->label('Next Billing Date'),
                            
                        Forms\Components\Select::make('category')
                            ->options([
                                'streaming' => 'Streaming Services',
                                'software' => 'Software & Apps',
                                'cloud' => 'Cloud Storage',
                                'membership' => 'Memberships',
                                'utilities' => 'Utilities',
                                'phone' => 'Phone & Internet',
                                'education' => 'Education',
                                'health' => 'Health & Wellness',
                                'gaming' => 'Gaming',
                                'news' => 'News & Magazines',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->required(),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Subscription')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                            
                        Forms\Components\Toggle::make('status')
                            ->label('Paid This Month')
                            ->helperText('Mark as paid for the current billing cycle')
                            ->onIcon('heroicon-s-check-circle')
                            ->offIcon('heroicon-s-x-circle')
                            ->onColor('success')
                            ->offColor('danger')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('last_paid_date', now()->format('Y-m-d'));
                                }
                            }),
                            
                        Forms\Components\DatePicker::make('last_paid_date')
                            ->label('Last Paid Date')
                            ->visible(fn ($get) => $get('status') === 'paid'),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Billing Details')
                    ->schema([
                        Forms\Components\Select::make('billing_cycle')
                            ->options([
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly (Every 3 Months)',
                                'biannual' => 'Biannual (Every 6 Months)',
                                'annual' => 'Annual (Yearly)',
                            ])
                            ->default('monthly')
                            ->required()
                            ->reactive(),
                            
                        Forms\Components\Toggle::make('auto_create_transaction')
                            ->label('Auto-create Transaction')
                            ->helperText('Automatically create a transaction when payment is due')
                            ->default(false),
                            
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default(now()),
                            
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->helperText('Optional: Leave empty for ongoing subscriptions')
                            ->after('start_date'),
                    ]),
                    
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(MonthlySubscription::query()->where('user_id', auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'primary' => 'streaming',
                        'secondary' => 'software',
                        'success' => 'cloud',
                        'warning' => 'utilities',
                        'danger' => 'phone',
                        'info' => fn ($state) => in_array($state, ['education', 'health', 'gaming', 'news', 'membership']),
                        'gray' => 'other',
                    ]),
                    
                Tables\Columns\TextColumn::make('billing_date')
                    ->label('Next Payment')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label('Paid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn (MonthlySubscription $record): bool => $record->status === 'paid'),
                    
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Cycle')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'streaming' => 'Streaming Services',
                        'software' => 'Software & Apps',
                        'cloud' => 'Cloud Storage',
                        'membership' => 'Memberships',
                        'utilities' => 'Utilities',
                        'phone' => 'Phone & Internet',
                        'education' => 'Education',
                        'health' => 'Health & Wellness',
                        'gaming' => 'Gaming',
                        'news' => 'News & Magazines',
                        'other' => 'Other',
                    ]),
                    
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Subscriptions')
                    ->toggle(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->label('Payment Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('togglePaid')
                    ->label(fn (MonthlySubscription $record): string => $record->status === 'paid' ? 'Mark Unpaid' : 'Mark Paid')
                    ->icon(fn (MonthlySubscription $record): string => $record->status === 'paid' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (MonthlySubscription $record): string => $record->status === 'paid' ? 'danger' : 'success')
                    ->action(function (MonthlySubscription $record): void {
                        $record->status = $record->status === 'paid' ? 'unpaid' : 'paid';
                        $record->last_paid_date = $record->status === 'paid' ? now() : null;
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = 'paid';
                                $record->last_paid_date = now();
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('markAsUnpaid')
                        ->label('Mark as Unpaid')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = 'unpaid';
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('calculate_total')
                    ->label('Monthly Total')
                    ->icon('heroicon-o-calculator')
                    ->action(function () {
                        // This will be handled by a livewire component
                    })
                    ->modalContent(function () {
                        $total = MonthlySubscription::query()
                            ->where('user_id', auth()->id())
                            ->where('is_active', true)
                            ->sum('amount');
                        
                        return view('filament.modals.subscription-totals', [
                            'total' => $total,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn ($action) => $action->label('Close')),
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
            'index' => Pages\ListMonthlySubscriptions::route('/'),
            'create' => Pages\CreateMonthlySubscription::route('/create'),
            'edit' => Pages\EditMonthlySubscription::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('is_active', true)->count();
    }
}