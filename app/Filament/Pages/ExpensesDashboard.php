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
    protected static ?string $navigationLabel = 'Expenses Dashboard';
    protected static ?string $title = 'Expenses Dashboard';
    protected static ?string $slug = 'expenses-dashboard';
    protected static ?string $navigationGroup = 'Overview';
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.pages.expenses-dashboard';
    
    public $timeframe = 'month';
    public $startDate;
    public $endDate;
    public $category = 'all';
    public $currentMonth;
    public $currentYear;
    
    protected $listeners = ['refreshDashboard' => '$refresh'];
    
    public function mount()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        
        $this->setDateRangeFromTimeframe();
    }
    
    /**
     * Set date range based on selected timeframe
     */
    protected function setDateRangeFromTimeframe()
    {
        // Create a Carbon instance for the current year/month
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
    }
    
    /**
     * Navigate to previous period based on current timeframe
     */
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
            return;
        }
        
        $this->setDateRangeFromTimeframe();
    }
    
    /**
     * Navigate to next period based on current timeframe
     */
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
            return;
        }
        
        $this->setDateRangeFromTimeframe();
    }
    
    /**
     * Update the timeframe and refresh the data
     *
     * @param string $timeframe
     * @return void
     */
    public function updateTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
        $this->setDateRangeFromTimeframe();
    }
    
    /**
     * Reset to current period
     */
    public function resetToCurrentPeriod()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->setDateRangeFromTimeframe();
    }
    
    /**
     * Get form schema for adding a new expense
     */
    public function getExpenseFormSchema(): array
    {
        return [
            Section::make('New Expense')
                ->schema([
                    Hidden::make('type')
                        ->default('expense'),
                    
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->prefix('EUR')
                        ->placeholder('0.00'),
                    
                    DatePicker::make('date')
                        ->required()
                        ->default(now()),
                    
                    Select::make('category')
                        ->options([
                            'food' => 'Food & Dining',
                            'shopping' => 'Shopping',
                            'entertainment' => 'Entertainment',
                            'transportation' => 'Transportation',
                            'housing' => 'Housing',
                            'utilities' => 'Utilities',
                            'health' => 'Health',
                            'education' => 'Education',
                            'travel' => 'Travel',
                            'unhealthy_habits' => 'Unhealthy Habits',
                            'other_expense' => 'Other Expense',
                        ])
                        ->searchable()
                        ->required(),
                    
                    TextInput::make('description')
                        ->maxLength(255)
                        ->placeholder('Expense description')
                ])
        ];
    }
    
    /**
     * Define the quick expense form action
     */
    protected function getQuickExpenseAction(): Action
    {
        return Action::make('quickAddExpense')
            ->label('Quick Add')
            ->color('success')
            ->icon('heroicon-m-plus-circle')
            ->form($this->getExpenseFormSchema())
            ->modalWidth('md')
            ->modalHeading('Add New Expense')
            ->modalDescription('Quickly add a new expense to your records.')
            ->modalSubmitActionLabel('Save Expense')
            ->action(function (array $data) {
                // Add user_id to data
                $data['user_id'] = auth()->id();
                
                // Create the transaction
                $transaction = Transaction::create($data);
                
                // Update user balance if needed
                $this->updateUserBalance($data['amount']);
                
                Notification::make()
                    ->title('Expense added successfully')
                    ->success()
                    ->send();
                
                // Refresh the dashboard data
                $this->dispatch('refreshDashboard');
            });
    }
    
    /**
     * Update user balance after adding an expense
     */
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
                    ->label('Add Expense')
                    ->url(fn (): string => url('/app/transactions/create'))
                    ->color('primary')
                    ->icon('heroicon-m-plus-circle'),
            ])->label('Add Expense')
              ->color('success')
              ->icon('heroicon-m-plus-circle'),
                
            Action::make('filter')
                ->label('Filter')
                ->icon('heroicon-m-funnel')
                ->iconPosition(IconPosition::After)
                ->form([
                    Section::make()
                        ->schema([
                            Select::make('timeframe')
                                ->label('Timeframe')
                                ->options([
                                    'week' => 'This Week',
                                    'month' => 'This Month',
                                    'quarter' => 'This Quarter',
                                    'year' => 'This Year',
                                    'custom' => 'Custom Range',
                                ])
                                ->default($this->timeframe)
                                ->live()
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
                                ->label('Start Date')
                                ->default($this->startDate)
                                ->visible(fn ($get) => $get('timeframe') === 'custom'),
                                
                            DatePicker::make('endDate')
                                ->label('End Date')
                                ->default($this->endDate)
                                ->visible(fn ($get) => $get('timeframe') === 'custom')
                                ->afterOrEqual('startDate'),
                                
                            Select::make('category')
                                ->label('Category')
                                ->options(function() {
                                    $categories = Transaction::where('user_id', auth()->id())
                                        ->where('type', 'expense')
                                        ->select('category')
                                        ->distinct()
                                        ->pluck('category', 'category')
                                        ->toArray();
                                        
                                    return ['all' => 'All Categories'] + $categories;
                                })
                                ->default($this->category),
                        ])
                ])
                ->action(function (array $data) {
                    $this->timeframe = $data['timeframe'] ?? 'month';
                    
                    // Set dates based on timeframe if not custom
                    if ($this->timeframe === 'custom') {
                        // For custom timeframe, use the provided dates
                        $this->startDate = $data['startDate'] ?? $this->startDate;
                        $this->endDate = $data['endDate'] ?? $this->endDate;
                    } else {
                        // Otherwise set the date range based on the timeframe
                        $this->setDateRangeFromTimeframe();
                    }
                    
                    $this->category = $data['category'] ?? 'all';
                }),
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
        
        return $query->sum('amount');
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
        
        // First, get the data without ordering
        $results = $query->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
        
        // Get total for percentage calculation
        $totalAll = $query->sum('amount');
        
        // Map and transform the results, then sort manually
        $data = $results->map(function ($item) use ($totalAll) {
                $percentage = $totalAll > 0 ? round(($item->total / $totalAll) * 100, 1) : 0;
                return [
                    'category' => $item->category,
                    'total' => $item->total,
                    'percentage' => $percentage
                ];
            })
            ->sortByDesc('total')
            ->values(); // Convert to indexed array after sorting
            
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
            ->sum('amount');
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
                    ->label('Date')
                    ->date(),
                TextEntry::make('amount')
                    ->label('Amount')
                    ->money('EUR'),
                TextEntry::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel' => 'success',
                        'unhealthy_habits' => 'danger',
                        'shopping', 'entertainment', 'other_expense' => 'warning',
                        default => 'gray',
                    }),
                TextEntry::make('description')
                    ->label('Description'),
            ])
            ->columns(4);
    }
    
    // Get period label based on current timeframe
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