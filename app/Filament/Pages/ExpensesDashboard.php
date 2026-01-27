<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as PageAction;

class ExpensesDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = null;
    protected static ?string $title = null;
    protected static ?string $slug = 'expenses-dashboard';
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 2;
    
    public static function getNavigationLabel(): string
    {
        return __('expenses-dashboard.navigation.label');
    }
    
    public function getTitle(): string
    {
        return __('expenses-dashboard.title');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Overview'; // Must match the group name registered in AppPanelProvider
    }
    
    protected static string $view = 'filament.pages.expenses-dashboard';
    
    public $timeframe = 'month';
    public $startDate;
    public $endDate;
    public $category = 'all';
    public $currentMonth;
    public $currentYear;
    public $dataUpdated = false;
    
    protected $listeners = ['refreshDashboard' => '$refresh'];
    
    public function mount()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        
        $this->setDateRangeFromTimeframe();
        $this->dataUpdated = true;
    }
    
    protected function setDateRangeFromTimeframe()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        
        if ($this->timeframe === 'week') {
            $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($this->timeframe === 'month') {
            $this->startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate = $date->copy()->endOfMonth()->format('Y-m-d');
        } elseif ($this->timeframe === 'quarter') {
            $this->startDate = $date->copy()->startOfQuarter()->format('Y-m-d');
            $this->endDate = $date->copy()->endOfQuarter()->format('Y-m-d');
        } elseif ($this->timeframe === 'year') {
            $this->startDate = $date->copy()->startOfYear()->format('Y-m-d');
            $this->endDate = $date->copy()->endOfYear()->format('Y-m-d');
        }
        
        $this->dataUpdated = true;
    }
    
    public function previousPeriod()
    {
        if ($this->timeframe === 'month') {
            if ($this->currentMonth == 1) {
                $this->currentMonth = 12;
                $this->currentYear--;
            } else {
                $this->currentMonth--;
            }
        } elseif ($this->timeframe === 'quarter') {
            $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonths(3);
            $this->currentMonth = $date->month;
            $this->currentYear = $date->year;
        } elseif ($this->timeframe === 'year') {
            $this->currentYear--;
        } elseif ($this->timeframe === 'week') {
            $date = Carbon::parse($this->startDate)->subDays(7);
            $this->startDate = $date->format('Y-m-d');
            $this->endDate = $date->addDays(6)->format('Y-m-d');
            $this->dataUpdated = true;
            $this->dispatch('refreshCharts');
            return;
        }
        
        $this->setDateRangeFromTimeframe();
        $this->dispatch('refreshCharts');
    }
    
    public function nextPeriod()
    {
        if ($this->timeframe === 'month') {
            if ($this->currentMonth == 12) {
                $this->currentMonth = 1;
                $this->currentYear++;
            } else {
                $this->currentMonth++;
            }
        } elseif ($this->timeframe === 'quarter') {
            $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonths(3);
            $this->currentMonth = $date->month;
            $this->currentYear = $date->year;
        } elseif ($this->timeframe === 'year') {
            $this->currentYear++;
        } elseif ($this->timeframe === 'week') {
            $date = Carbon::parse($this->startDate)->addDays(7);
            $this->startDate = $date->format('Y-m-d');
            $this->endDate = $date->addDays(6)->format('Y-m-d');
            $this->dataUpdated = true;
            $this->dispatch('refreshCharts');
            return;
        }
        
        $this->setDateRangeFromTimeframe();
        $this->dispatch('refreshCharts');
    }
    
    public function updateTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        
        $this->setDateRangeFromTimeframe();

        $this->redirect(request()->header('Referer'));
    }
    
    public function resetToCurrentPeriod()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->setDateRangeFromTimeframe();
        $this->dispatch('refreshCharts');
    }
    
    public function resetFilters()
    {
        $oldTimeframe = $this->timeframe;
        $oldCategory = $this->category;
        $oldStartDate = $this->startDate;
        $oldEndDate = $this->endDate;
        
        $this->timeframe = 'month';
        $this->category = 'all';
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->setDateRangeFromTimeframe();
        
        if (
            $oldTimeframe !== $this->timeframe ||
            $oldCategory !== $this->category ||
            $oldStartDate !== $this->startDate ||
            $oldEndDate !== $this->endDate
        ) {
            Notification::make()
                ->title(__('expenses-dashboard.notifications.filters_reset'))
                ->success()
                ->send();
                
            $this->dispatch('refreshCharts');
        }
    }
    
    public function getExpenseFormSchema(): array
    {
        return [
            Section::make(__('expenses-dashboard.form.new_expense.section'))
                ->schema([
                    Hidden::make('type')
                        ->default('expense'),
                    
                    TextInput::make('amount')
                        ->label(__('expenses-dashboard.form.new_expense.amount.label'))
                        ->required()
                        ->numeric()
                        ->prefix('EUR')
                        ->placeholder(__('expenses-dashboard.form.new_expense.amount.placeholder')),
                    
                    DatePicker::make('date')
                        ->label(__('expenses-dashboard.form.new_expense.date.label'))
                        ->required()
                        ->default(now()),
                    
                    Select::make('category')
                        ->label(__('expenses-dashboard.form.new_expense.category.label'))
                        ->options([
                            'food' => __('messages.categories.expense.food'),
                            'shopping' => __('messages.categories.expense.shopping'),
                            'entertainment' => __('messages.categories.expense.entertainment'),
                            'transportation' => __('messages.categories.expense.transportation'),
                            'housing' => __('messages.categories.expense.housing'),
                            'utilities' => __('messages.categories.expense.utilities'),
                            'health' => __('messages.categories.expense.health'),
                            'education' => __('messages.categories.expense.education'),
                            'travel' => __('messages.categories.expense.travel'),
                            'unhealthy_habits' => __('messages.categories.expense.unhealthy_habits'),
                            'other_expense' => __('messages.categories.expense.other_expense'),
                        ])
                        ->searchable()
                        ->required(),
                    
                    TextInput::make('description')
                        ->label(__('expenses-dashboard.form.new_expense.description.label'))
                        ->maxLength(255)
                        ->placeholder(__('expenses-dashboard.form.new_expense.description.placeholder'))
                ])
        ];
    }
    
    protected function getQuickExpenseAction(): Action
    {
        return Action::make('quickAddExpense')
            ->label(__('expenses-dashboard.actions.quick_add.label'))
            ->color('success')
            ->icon('heroicon-m-plus-circle')
            ->form($this->getExpenseFormSchema())
            ->modalWidth('md')
            ->modalHeading(__('expenses-dashboard.actions.quick_add.modal_heading'))
            ->modalDescription(__('expenses-dashboard.actions.quick_add.modal_description'))
            ->modalSubmitActionLabel(__('expenses-dashboard.actions.quick_add.submit_label'))
            ->action(function (array $data) {
                $data['user_id'] = auth()->id();
                $transaction = Transaction::create($data);
                
                $this->updateUserBalance($data['amount']);
                
                Notification::make()
                    ->title(__('expenses-dashboard.notifications.expense_added'))
                    ->success()
                    ->send();
                
                $this->dispatch('refreshCharts');
            });
    }
    
    protected function updateUserBalance($amount)
    {
        $userBalance = \App\Models\UserBalance::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0, 'currency' => 'EUR']
        );
        
        $userBalance->balance -= $amount;
        $userBalance->save();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                $this->getQuickExpenseAction(),
                Action::make('addExpense')
                    ->label(__('expenses-dashboard.actions.add_expense.label'))
                    ->url(fn (): string => url('/app/transactions/create'))
                    ->color('primary')
                    ->icon('heroicon-m-plus-circle'),
            ])->label(__('expenses-dashboard.actions.add_expense.label'))
              ->color('success')
              ->icon('heroicon-m-plus-circle'),
                
            Action::make('filter')
                ->label(__('expenses-dashboard.actions.filter.label'))
                ->icon('heroicon-m-funnel')
                ->iconPosition(IconPosition::After)
                ->form([
                    Section::make()
                        ->schema([
                            Select::make('timeframe')
                                ->label(__('expenses-dashboard.actions.filter.timeframe.label'))
                                ->options([
                                    'week' => __('messages.dashboard.expenses.period_labels.week'),
                                    'month' => __('messages.dashboard.expenses.period_labels.month'),
                                    'quarter' => __('messages.dashboard.expenses.period_labels.quarter'),
                                    'year' => __('messages.dashboard.expenses.period_labels.year'),
                                    'custom' => __('messages.dashboard.expenses.period_labels.custom'),
                                ])
                                ->default(fn() => $this->timeframe)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $now = Carbon::now();
                                    
                                    if ($state === 'week') {
                                        $set('startDate', $now->copy()->startOfWeek()->format('Y-m-d'));
                                        $set('endDate', $now->copy()->endOfWeek()->format('Y-m-d'));
                                    } elseif ($state === 'month') {
                                        $set('startDate', $now->copy()->startOfMonth()->format('Y-m-d'));
                                        $set('endDate', $now->copy()->endOfMonth()->format('Y-m-d'));
                                    } elseif ($state === 'quarter') {
                                        $set('startDate', $now->copy()->startOfQuarter()->format('Y-m-d'));
                                        $set('endDate', $now->copy()->endOfQuarter()->format('Y-m-d'));
                                    } elseif ($state === 'year') {
                                        $set('startDate', $now->copy()->startOfYear()->format('Y-m-d'));
                                        $set('endDate', $now->copy()->endOfYear()->format('Y-m-d'));
                                    }
                                }),
                            
                            DatePicker::make('startDate')
                                ->label(__('expenses-dashboard.actions.filter.start_date.label'))
                                ->default(fn() => $this->startDate)
                                ->visible(fn (callable $get) => $get('timeframe') === 'custom'),
                                
                            DatePicker::make('endDate')
                                ->label(__('expenses-dashboard.actions.filter.end_date.label'))
                                ->default(fn() => $this->endDate)
                                ->visible(fn (callable $get) => $get('timeframe') === 'custom')
                                ->afterOrEqual('startDate'),
                            
                            Select::make('category')
                                ->label(__('expenses-dashboard.actions.filter.category.label'))
                                ->options(function() {
                                    $categories = Transaction::where('user_id', auth()->id())
                                        ->where('type', 'expense')
                                        ->select('category')
                                        ->distinct()
                                        ->pluck('category', 'category')
                                        ->toArray();
                                        
                                    return ['all' => __('messages.dashboard.expenses.filter.all_categories')] + $categories;
                                })
                                ->default(fn() => $this->category),
                        ])
                ])
                ->action(function (array $data) {
                    $oldTimeframe = $this->timeframe;
                    $oldCategory = $this->category;
                    $oldStartDate = $this->startDate;
                    $oldEndDate = $this->endDate;
                    $this->timeframe = $data['timeframe'] ?? 'month';
                    
                    if ($this->timeframe === 'custom') {
                        $this->startDate = $data['startDate'] ?? $this->startDate;
                        $this->endDate = $data['endDate'] ?? $this->endDate;
                    } else {
                        $this->setDateRangeFromTimeframe();
                    }
                    
                    $this->category = $data['category'] ?? 'all';
                    
                    if (
                        $oldTimeframe !== $this->timeframe ||
                        $oldCategory !== $this->category ||
                        $oldStartDate !== $this->startDate ||
                        $oldEndDate !== $this->endDate
                    ) {
                        Notification::make()
                            ->title(__('expenses-dashboard.notifications.filters_applied'))
                            ->success()
                            ->send();
                            
                        $this->dispatch('refreshCharts');
                    }
                })
                ->color('secondary')
                ->extraAttributes([
                    'class' => 'filter-button',
                    'x-data' => "{ 
                        isFiltered: " . (($this->timeframe !== 'month' || $this->category !== 'all') ? 'true' : 'false') . " 
                    }",
                    'x-bind:class' => "isFiltered ? 'filter-active' : ''"
                ])
                ->modalWidth('md')
                ->modalHeading(__('expenses-dashboard.actions.filter.modal_heading'))
                ->extraModalFooterActions(fn(Action $action) => [
                    Action::make('resetFilters')
                        ->label(__('expenses-dashboard.actions.filter.reset.label'))
                        ->color('gray')
                        ->action(function () use ($action) {
                            $this->resetFilters();
                            $action->cancel();
                        }),
                ]),
        ];
    }
    
    public function getExpensesData()
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
            
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }
        
        return $query->orderBy('date', 'desc')->get();
    }
    
    public function getTotalExpenses()
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
            
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }
        
        return $query->sum('amount') ?: 0;
    }
    
    public function getAverageExpensePerDay()
    {
        $total = $this->getTotalExpenses();
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $days = max(1, $startDate->diffInDays($endDate) + 1);
        
        return $total / $days;
    }
    
    public function getCategoryBreakdown()
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
            
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }
        
        $results = $query->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $totalAll = $query->sum('amount') ?: 0;
        
        $data = $results->map(function ($item) use ($totalAll) {
                $percentage = $totalAll > 0 ? round(($item->total / $totalAll) * 100, 1) : 0;
                return [
                    'category' => $item->category,
                    'total' => $item->total,
                    'percentage' => $percentage
                ];
            })
            ->sortByDesc('total')
            ->values();
            
        return $data;
    }
    
    public function getMonthlyTrend()
    {
        $year = Carbon::parse($this->startDate)->year;
        $months = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = Carbon::createFromDate($year, $i, 1)->startOfMonth();
            $monthEnd = Carbon::createFromDate($year, $i, 1)->endOfMonth();
            
            $query = Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereBetween('date', [$monthStart, $monthEnd]);
                
            if ($this->category !== 'all') {
                $query->where('category', $this->category);
            }
            
            $total = $query->sum('amount') ?: 0;
            
            $months[] = [
                'month' => $monthStart->format('M'),
                'total' => round($total, 2)
            ];
        }
        
        return $months;
    }
    
    public function getDailyTrend()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $days = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayDate = $date->format('Y-m-d');
            
            $query = Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereDate('date', $dayDate);
                
            if ($this->category !== 'all') {
                $query->where('category', $this->category);
            }
            
            $total = $query->sum('amount') ?: 0;
            
            $days[] = [
                'date' => $date->format('d M'),
                'total' => round($total, 2)
            ];
        }
        
        return $days;
    }
    
    public function getLargestExpenses($limit = 5)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
            
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }
        
        return $query->orderBy('amount', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function getUnhealthyExpenses()
    {
        return Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->where('category', 'unhealthy_habits')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->sum('amount') ?: 0;
    }
    
    public function getRecentTransactions($limit = 10)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
            
        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }
        
        return $query->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function expenseInfolists(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('date')
                    ->label(__('expenses-dashboard.infolist.date.label'))
                    ->date(),
                TextEntry::make('amount')
                    ->label(__('expenses-dashboard.infolist.amount.label'))
                    ->money('EUR'),
                TextEntry::make('category')
                    ->label(__('expenses-dashboard.infolist.category.label'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel' => 'success',
                        'unhealthy_habits' => 'danger',
                        'shopping', 'entertainment', 'other_expense' => 'warning',
                        default => 'gray',
                    }),
                TextEntry::make('description')
                    ->label(__('expenses-dashboard.infolist.description.label')),
            ])
            ->columns(4);
    }
    
    public function getPeriodLabel()
    {
        if ($this->timeframe === 'month') {
            return Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->format('F Y');
        } elseif ($this->timeframe === 'quarter') {
            $startMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfQuarter();
            $endMonth = $startMonth->copy()->endOfQuarter();
            return $startMonth->format('M') . ' - ' . $endMonth->format('M Y');
        } elseif ($this->timeframe === 'year') {
            return $this->currentYear;
        } elseif ($this->timeframe === 'week') {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            return $start->format('M d') . ' - ' . $end->format('M d, Y');
        } elseif ($this->timeframe === 'custom') {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            return $start->format('M d') . ' - ' . $end->format('M d, Y');
        }
        
        return '';
    }
}