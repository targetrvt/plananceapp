<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
    
    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
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
        
        // Set dates based on selected timeframe
        $now = Carbon::now();
        
        if ($this->timeframe === 'week') {
            $this->startDate = $now->copy()->startOfWeek()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d');
        } elseif ($this->timeframe === 'month') {
            $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
        } elseif ($this->timeframe === 'quarter') {
            $this->startDate = $now->copy()->startOfQuarter()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfQuarter()->format('Y-m-d');
        } elseif ($this->timeframe === 'year') {
            $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
        }
    }
    
    protected function getHeaderActions(): array
    {
        return [
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
                    $now = Carbon::now();
                    if ($this->timeframe === 'week') {
                        $this->startDate = $now->copy()->startOfWeek()->format('Y-m-d');
                        $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d');
                    } elseif ($this->timeframe === 'month') {
                        $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                        $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                    } elseif ($this->timeframe === 'quarter') {
                        $this->startDate = $now->copy()->startOfQuarter()->format('Y-m-d');
                        $this->endDate = $now->copy()->endOfQuarter()->format('Y-m-d');
                    } elseif ($this->timeframe === 'year') {
                        $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
                        $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
                    } else {
                        // For custom timeframe, use the provided dates
                        $this->startDate = $data['startDate'] ?? $this->startDate;
                        $this->endDate = $data['endDate'] ?? $this->endDate;
                    }
                    
                    $this->category = $data['category'] ?? 'all';
                }),
                
            Action::make('addExpense')
                ->label('Add Expense')
                ->url(fn (): string => url('/app/transactions/create'))
                ->color('success')
                ->icon('heroicon-m-plus-circle'),
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
}