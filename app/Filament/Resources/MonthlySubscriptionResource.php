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
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Carbon;

class MonthlySubscriptionResource extends Resource
{
    protected static ?string $model = MonthlySubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('is_active', true)->count();
    }
    
    public static function getNavigationBadgeColor(): string
    {
        $count = static::getEloquentQuery()->where('is_active', true)->count();
        
        return $count > 5 ? 'warning' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->description('Enter the basic information about this subscription')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Subscription Name')
                            ->placeholder('Netflix, Spotify, etc.')
                            ->columnSpan(['sm' => 1, 'md' => 2]),
                            
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
                            ->required()
                            ->preload()
                            ->native(false)
                            ->columnSpan(['sm' => 1, 'md' => 2]),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpan('full')
                            ->placeholder('Additional details about this subscription'),
                    ])
                    ->columns(4),
                    
                Forms\Components\Section::make('Billing Information')
                    ->description('Manage payment details and billing schedule')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('EUR')
                                    ->label('Billing Amount')
                                    ->helperText('The amount charged for this billing cycle')
                                    ->placeholder('0.00'),
                                    
                                Forms\Components\Select::make('billing_cycle')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'quarterly' => 'Quarterly (Every 3 Months)',
                                        'biannual' => 'Biannual (Every 6 Months)',
                                        'annual' => 'Annual (Yearly)',
                                    ])
                                    ->default('monthly')
                                    ->required()
                                    ->reactive()
                                    ->helperText(function (callable $get) {
                                        $cycle = $get('billing_cycle');
                                        $amount = floatval($get('amount') ?: 0);
                                        
                                        if (!$amount) return null;
                                        
                                        $divisor = match($cycle) {
                                            'monthly' => 1,
                                            'quarterly' => 3,
                                            'biannual' => 6,
                                            'annual' => 12,
                                            default => 1,
                                        };
                                        
                                        $monthly = $amount / $divisor;
                                        
                                        return "Monthly equivalent: €" . number_format($monthly, 2);
                                    }),
                            ]),
                            
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('billing_date')
                                    ->required()
                                    ->label('Next Billing Date')
                                    ->helperText('When is the next payment due?')
                                    ->default(now()),
                                    
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'paid' => 'Paid',
                                        'unpaid' => 'Unpaid',
                                    ])
                                    ->default('unpaid')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        if ($state === 'paid') {
                                            $set('last_paid_date', now()->format('Y-m-d'));
                                        }
                                    }),
                            ]),
                            
                        Forms\Components\DatePicker::make('last_paid_date')
                            ->label('Last Payment Date')
                            ->visible(fn ($get) => $get('status') === 'paid')
                            ->before(function (callable $get) {
                                $billingDate = $get('billing_date');
                                return $billingDate ? Carbon::parse($billingDate)->addDay() : null;
                            }),
                    ]),
                    
                Forms\Components\Section::make('Advanced Options')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active Subscription')
                                    ->default(true)
                                    ->helperText('Inactive subscriptions won\'t appear in reports')
                                    ->onColor('success')
                                    ->offColor('danger'),
                                    
                                Forms\Components\Toggle::make('auto_create_transaction')
                                    ->label('Auto-create Transaction')
                                    ->helperText('Automatically create a transaction when payment is due')
                                    ->default(false),
                            ]),
                            
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->default(now())
                                    ->helperText('When did this subscription begin?'),
                                    
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->helperText('Optional: Leave empty for ongoing subscriptions')
                                    ->after('start_date'),
                            ]),
                    ])
                    ->collapsible(),
                    
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(MonthlySubscription::query()->where('user_id', auth()->id()))
            ->defaultSort('billing_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (MonthlySubscription $record): ?string => $record->description ? 
                        (strlen($record->description) > 30 ? 
                            substr($record->description, 0, 30) . '...' : 
                            $record->description) : 
                        null)
                    ->weight('medium'),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable()
                    ->description(function (MonthlySubscription $record): string {
                        $divisor = match($record->billing_cycle) {
                            'monthly' => 1,
                            'quarterly' => 3,
                            'biannual' => 6,
                            'annual' => 12,
                            default => 1,
                        };
                        
                        $monthly = $record->amount / $divisor;
                        
                        return '€' . number_format($monthly, 2) . '/mo';
                    }),
                    
                Tables\Columns\TextColumn::make('billing_date')
                    ->label('Next Payment')
                    ->date()
                    ->sortable()
                    ->description(function (MonthlySubscription $record): string {
                        $billingDate = Carbon::parse($record->billing_date);
                        $daysLeft = Carbon::now()->diffInDays($billingDate, false);
                        
                        if ($daysLeft < 0) {
                            return 'Overdue!';
                        } else if ($daysLeft === 0) {
                            return 'Due today!';
                        } else {
                            return $daysLeft . ' days left';
                        }
                    })
                    ->color(function (MonthlySubscription $record): string {
                        $billingDate = Carbon::parse($record->billing_date);
                        $daysLeft = Carbon::now()->diffInDays($billingDate, false);
                        
                        if ($daysLeft < 0) {
                            return 'danger';
                        } else if ($daysLeft <= 3) {
                            return 'warning';
                        } else {
                            return 'gray';
                        }
                    }),
                    
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->colors([
                        'primary' => 'streaming',
                        'secondary' => 'software',
                        'success' => 'cloud',
                        'warning' => 'utilities',
                        'danger' => 'phone',
                        'info' => fn ($state) => in_array($state, ['education', 'health', 'gaming', 'news', 'membership']),
                        'gray' => 'other',
                    ]),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->toggleable(),
                    
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
                    ->toggleable()
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
                    ])
                    ->indicator('Category'),
                    
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Subscriptions')
                    ->toggle()
                    ->default(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->label('Payment Status')
                    ->indicator('Status'),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('Due in 30 days')
                    ->query(function (Builder $query): Builder {
                        return $query->where('billing_date', '<=', now()->addDays(30));
                    })
                    ->toggle()
                    ->indicator('Due Soon'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                        
                    Tables\Actions\Action::make('togglePaid')
                        ->label(fn (MonthlySubscription $record): string => $record->status === 'paid' ? 'Mark Unpaid' : 'Mark Paid')
                        ->icon(fn (MonthlySubscription $record): string => $record->status === 'paid' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (MonthlySubscription $record): string => $record->status === 'paid' ? 'danger' : 'success')
                        ->action(function (MonthlySubscription $record): void {
                            if ($record->status === 'paid') {
                                $record->status = 'unpaid';
                                $record->last_paid_date = null;
                            } else {
                                $record->last_paid_date = now();
                                
                                $currentBillingDate = Carbon::parse($record->billing_date);
                                
                                $newBillingDate = match($record->billing_cycle) {
                                    'monthly' => $currentBillingDate->addMonth(),
                                    'quarterly' => $currentBillingDate->addMonths(3),
                                    'biannual' => $currentBillingDate->addMonths(6),
                                    'annual' => $currentBillingDate->addYear(),
                                    default => $currentBillingDate->addMonth(),
                                };
                                
                                $record->billing_date = $newBillingDate;
                                $record->status = 'unpaid';
                            }
                            
                            $record->save();
                            
                            Notification::make()
                                ->title($record->status === 'unpaid' ? 
                                    'Payment recorded! Next payment: ' . Carbon::parse($record->billing_date)->format('M d, Y') : 
                                    'Subscription marked as unpaid')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function (MonthlySubscription $record): void {
                            $newSubscription = $record->replicate();
                            $newSubscription->name = $record->name . ' (Copy)';
                            $newSubscription->save();
                            
                            Notification::make()
                                ->title('Subscription duplicated')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
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
                            
                            Notification::make()
                                ->title('Subscriptions marked as paid')
                                ->success()
                                ->send();
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
                            
                            Notification::make()
                                ->title('Subscriptions marked as unpaid')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('setInactive')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->is_active = false;
                                $record->save();
                            }
                            
                            Notification::make()
                                ->title('Subscriptions set as inactive')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('setActive')
                        ->label('Set Active')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->is_active = true;
                                $record->save();
                            }
                            
                            Notification::make()
                                ->title('Subscriptions set as active')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('calculate_total')
                    ->label('Monthly Total')
                    ->icon('heroicon-o-calculator')
                    ->color('primary')
                    ->action(function () {
                    })
                    ->modalHeading('Subscription Cost Summary')
                    ->modalWidth(MaxWidth::ExtraSmall)
                    ->modalContent(function () {
                        $subscriptions = MonthlySubscription::query()
                            ->where('user_id', auth()->id())
                            ->where('is_active', true)
                            ->get();
                        
                        $monthlyTotal = $subscriptions->sum(function($subscription) {
                            $divisor = match($subscription->billing_cycle) {
                                'monthly' => 1,
                                'quarterly' => 3,
                                'biannual' => 6,
                                'annual' => 12,
                                default => 1,
                            };
                            
                            return $subscription->amount / $divisor;
                        });
                        
                        $annualTotal = $monthlyTotal * 12;
                        
                        return view('filament.modals.subscription-totals', [
                            'monthlyTotal' => $monthlyTotal,
                            'annualTotal' => $annualTotal,
                            'count' => $subscriptions->count(),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn ($action) => $action->label('Close')),
                    
                Tables\Actions\CreateAction::make()
                    ->label('Add Subscription')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->emptyStateIcon('heroicon-o-credit-card')
            ->emptyStateHeading('No subscriptions yet')
            ->emptyStateDescription('Start tracking your recurring payments by adding your first subscription.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Subscription')
                    ->icon('heroicon-o-plus'),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlySubscriptions::route('/'),
            'create' => Pages\CreateMonthlySubscription::route('/create'),
            'edit' => Pages\EditMonthlySubscription::route('/{record}/edit'),
        ];
    }
    
}