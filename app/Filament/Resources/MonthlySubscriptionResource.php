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
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationLabel(): string
    {
        return __('monthly-subscription.navigation.label');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('monthly-subscription.navigation.label');
    }
    
    public static function getModelLabel(): string
    {
        return __('monthly-subscription.navigation.label');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Management'; // Must match the group name registered in AppPanelProvider
    }
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
                Forms\Components\Section::make(__('monthly-subscription.form.subscription_details.section'))
                    ->description(__('monthly-subscription.form.subscription_details.section_description'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('monthly-subscription.form.subscription_details.name.label'))
                            ->placeholder(__('monthly-subscription.form.subscription_details.name.placeholder'))
                            ->columnSpan(['sm' => 1, 'md' => 2]),
                            
                        Forms\Components\Select::make('category')
                            ->label(__('monthly-subscription.form.subscription_details.category.label'))
                            ->options([
                                'streaming' => __('messages.dashboard.subscriptions.categories.streaming'),
                                'software' => __('messages.dashboard.subscriptions.categories.software'),
                                'cloud' => __('messages.dashboard.subscriptions.categories.cloud'),
                                'membership' => __('messages.dashboard.subscriptions.categories.membership'),
                                'utilities' => __('messages.dashboard.subscriptions.categories.utilities'),
                                'phone' => __('messages.dashboard.subscriptions.categories.phone'),
                                'education' => __('messages.dashboard.subscriptions.categories.education'),
                                'health' => __('messages.dashboard.subscriptions.categories.health'),
                                'gaming' => __('messages.dashboard.subscriptions.categories.gaming'),
                                'news' => __('messages.dashboard.subscriptions.categories.news'),
                                'other' => __('messages.dashboard.subscriptions.categories.other'),
                            ])
                            ->searchable()
                            ->required()
                            ->preload()
                            ->native(false)
                            ->columnSpan(['sm' => 1, 'md' => 2]),
                            
                        Forms\Components\Textarea::make('description')
                            ->label(__('monthly-subscription.form.subscription_details.description.label'))
                            ->maxLength(255)
                            ->columnSpan('full')
                            ->placeholder(__('monthly-subscription.form.subscription_details.description.placeholder')),
                    ])
                    ->columns(4),
                    
                Forms\Components\Section::make(__('monthly-subscription.form.billing_information.section'))
                    ->description(__('monthly-subscription.form.billing_information.description'))
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('EUR')
                                    ->label(__('monthly-subscription.form.billing_information.amount.label'))
                                    ->helperText(__('monthly-subscription.form.billing_information.amount.helper'))
                                    ->placeholder(__('monthly-subscription.form.billing_information.amount.placeholder')),
                                    
                                Forms\Components\Select::make('billing_cycle')
                                    ->label(__('monthly-subscription.form.billing_information.billing_cycle.label'))
                                    ->options([
                                        'monthly' => __('monthly-subscription.form.billing_information.billing_cycle.options.monthly'),
                                        'quarterly' => __('monthly-subscription.form.billing_information.billing_cycle.options.quarterly'),
                                        'biannual' => __('monthly-subscription.form.billing_information.billing_cycle.options.biannual'),
                                        'annual' => __('monthly-subscription.form.billing_information.billing_cycle.options.annual'),
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
                                        
                                        return __('monthly-subscription.form.billing_information.billing_cycle.helper', ['amount' => number_format($monthly, 2)]);
                                    }),
                            ]),
                            
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('billing_date')
                                    ->required()
                                    ->label(__('monthly-subscription.form.billing_information.billing_date.label'))
                                    ->helperText(__('monthly-subscription.form.billing_information.billing_date.helper'))
                                    ->default(now()),
                                    
                                Forms\Components\Select::make('status')
                                    ->label(__('monthly-subscription.form.billing_information.status.label'))
                                    ->options([
                                        'paid' => __('monthly-subscription.form.billing_information.status.options.paid'),
                                        'unpaid' => __('monthly-subscription.form.billing_information.status.options.unpaid'),
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
                            ->label(__('monthly-subscription.form.billing_information.last_paid_date.label'))
                            ->visible(fn ($get) => $get('status') === 'paid')
                            ->before(function (callable $get) {
                                $billingDate = $get('billing_date');
                                return $billingDate ? Carbon::parse($billingDate)->addDay() : null;
                            }),
                    ]),
                    
                Forms\Components\Section::make(__('monthly-subscription.form.advanced_options.section'))
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('monthly-subscription.form.advanced_options.is_active.label'))
                                    ->default(true)
                                    ->helperText(__('monthly-subscription.form.advanced_options.is_active.helper'))
                                    ->onColor('success')
                                    ->offColor('danger'),
                                    
                                Forms\Components\Toggle::make('auto_create_transaction')
                                    ->label(__('monthly-subscription.form.advanced_options.auto_create_transaction.label'))
                                    ->helperText(__('monthly-subscription.form.advanced_options.auto_create_transaction.helper'))
                                    ->default(true),
                            ]),
                            
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label(__('monthly-subscription.form.advanced_options.start_date.label'))
                                    ->default(now())
                                    ->helperText(__('monthly-subscription.form.advanced_options.start_date.helper')),
                                    
                                Forms\Components\DatePicker::make('end_date')
                                    ->label(__('monthly-subscription.form.advanced_options.end_date.label'))
                                    ->helperText(__('monthly-subscription.form.advanced_options.end_date.helper'))
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
                        
                        return __('monthly-subscription.table.amount.monthly_equivalent', ['amount' => number_format($monthly, 2)]);
                    }),
                    
                    Tables\Columns\TextColumn::make('billing_date')
                    ->label(__('monthly-subscription.table.billing_date.label'))
                    ->date()
                    ->sortable()
                    ->description(function (MonthlySubscription $record): string {
                        if ($record->status === 'paid') {
                            return __('monthly-subscription.table.billing_date.paid');
                        }
                        $billingDate = Carbon::parse($record->billing_date);
                        $today = Carbon::today(); // Use today() instead of now() for consistent day comparison
                        $daysLeft = (int) $today->diffInDays($billingDate, false); // Cast to integer
                        
                        if ($daysLeft < 0) {
                            return __('monthly-subscription.table.billing_date.overdue');
                        } else if ($daysLeft === 0) {
                            return __('monthly-subscription.table.billing_date.due_today');
                        } else {
                            return __('monthly-subscription.table.billing_date.days_left', ['days' => $daysLeft]);
                        }
                    })
                    ->color(function (MonthlySubscription $record): string {
                        if ($record->status === 'paid') {
                            return 'success';
                        }
                        $billingDate = Carbon::parse($record->billing_date);
                        $today = Carbon::today();
                        $daysLeft = (int) $today->diffInDays($billingDate, false);
                        
                        if ($daysLeft < 0) {
                            return 'danger';
                        } else if ($daysLeft <= 3) {
                            return 'warning';
                        } else {
                            return 'gray';
                        }
                    }),
                    
                Tables\Columns\TextColumn::make('category')
                    ->label(__('monthly-subscription.table.category.label'))
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => __('messages.dashboard.subscriptions.categories.' . $state))
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
                    ->label(__('monthly-subscription.table.is_active.label'))
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('status')
                    ->label(__('monthly-subscription.table.status.label'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn (MonthlySubscription $record): bool => $record->status === 'paid'),
                    
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label(__('monthly-subscription.table.billing_cycle.label'))
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => __('monthly-subscription.form.billing_information.billing_cycle.options.' . $state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('monthly-subscription.filter.category.label'))
                    ->options([
                        'streaming' => __('messages.dashboard.subscriptions.categories.streaming'),
                        'software' => __('messages.dashboard.subscriptions.categories.software'),
                        'cloud' => __('messages.dashboard.subscriptions.categories.cloud'),
                        'membership' => __('messages.dashboard.subscriptions.categories.membership'),
                        'utilities' => __('messages.dashboard.subscriptions.categories.utilities'),
                        'phone' => __('messages.dashboard.subscriptions.categories.phone'),
                        'education' => __('messages.dashboard.subscriptions.categories.education'),
                        'health' => __('messages.dashboard.subscriptions.categories.health'),
                        'gaming' => __('messages.dashboard.subscriptions.categories.gaming'),
                        'news' => __('messages.dashboard.subscriptions.categories.news'),
                        'other' => __('messages.dashboard.subscriptions.categories.other'),
                    ])
                    ->indicator(__('monthly-subscription.filter.category.indicator')),
                    
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label(__('monthly-subscription.filter.active.label'))
                    ->toggle()
                    ->default(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('monthly-subscription.filter.status.label'))
                    ->options([
                        'paid' => __('monthly-subscription.filter.status.options.paid'),
                        'unpaid' => __('monthly-subscription.filter.status.options.unpaid'),
                    ])
                    ->indicator(__('monthly-subscription.filter.status.indicator')),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label(__('monthly-subscription.filter.upcoming.label'))
                    ->query(function (Builder $query): Builder {
                        return $query->where('billing_date', '<=', now()->addDays(30));
                    })
                    ->toggle()
                    ->indicator(__('monthly-subscription.filter.upcoming.indicator')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                        
                    Tables\Actions\Action::make('togglePaid')
                        ->label(fn (MonthlySubscription $record): string => $record->status === 'paid' ? __('monthly-subscription.actions.toggle_paid.mark_unpaid') : __('monthly-subscription.actions.toggle_paid.mark_paid'))
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
                                    __('monthly-subscription.notifications.payment_recorded', ['date' => Carbon::parse($record->billing_date)->format('M d, Y')]) : 
                                    __('monthly-subscription.notifications.marked_unpaid'))
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\Action::make('duplicate')
                        ->label(__('monthly-subscription.actions.duplicate.label'))
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function (MonthlySubscription $record): void {
                            $newSubscription = $record->replicate();
                            $newSubscription->name = $record->name . ' (Copy)';
                            $newSubscription->save();
                            
                            Notification::make()
                                ->title(__('monthly-subscription.notifications.duplicated'))
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
                        ->label(__('monthly-subscription.actions.toggle_paid.mark_paid'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = 'paid';
                                $record->last_paid_date = now();
                                $record->save();
                            }
                            
                            Notification::make()
                                ->title(__('monthly-subscription.notifications.marked_paid'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('markAsUnpaid')
                        ->label(__('monthly-subscription.actions.toggle_paid.mark_unpaid'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = 'unpaid';
                                $record->save();
                            }
                            
                            Notification::make()
                                ->title(__('monthly-subscription.notifications.marked_unpaid_bulk'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('setInactive')
                        ->label(__('monthly-subscription.actions.toggle_paid.mark_unpaid'))
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->is_active = false;
                                $record->save();
                            }
                            
                            Notification::make()
                                ->title(__('monthly-subscription.notifications.set_inactive'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('setActive')
                        ->label(__('monthly-subscription.actions.toggle_paid.mark_paid'))
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->is_active = true;
                                $record->save();
                            }
                            
                            Notification::make()
                                ->title(__('monthly-subscription.notifications.set_active'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('calculate_total')
                    ->label(__('monthly-subscription.actions.calculate_total.label'))
                    ->icon('heroicon-o-calculator')
                    ->color('primary')
                    ->action(function () {
                    })
                    ->modalHeading(__('monthly-subscription.actions.calculate_total.modal_heading'))
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
                    ->modalCancelAction(fn ($action) => $action->label(__('monthly-subscription.actions.calculate_total.close'))),
                    
                Tables\Actions\CreateAction::make()
                    ->label(__('monthly-subscription.actions.add_subscription.label'))
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->emptyStateIcon('heroicon-o-credit-card')
            ->emptyStateHeading(__('monthly-subscription.empty_state.heading'))
            ->emptyStateDescription(__('monthly-subscription.empty_state.description'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('monthly-subscription.actions.add_subscription.label'))
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