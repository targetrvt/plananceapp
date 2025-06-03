<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\MonthlySubscription;
use Filament\Support\Enums\IconPosition;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;

class MonthlySubscriptionsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Subscriptions Dashboard';
    protected static ?string $title = 'Subscriptions Dashboard';
    protected static ?string $slug = 'subscriptions-dashboard';
    protected static ?string $navigationGroup = 'Overview';
    protected static ?int $navigationSort = 3;
    
    protected static string $view = 'filament.pages.monthly-subscriptions-dashboard';
    
    public function getTotalMonthlyAmount()
    {
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();
            
        return $subscriptions->sum(function($subscription) {
            $divisor = match($subscription->billing_cycle) {
                'monthly' => 1,
                'quarterly' => 3,
                'biannual' => 6,
                'annual' => 12,
                default => 1,
            };
            
            return $subscription->amount / $divisor;
        });
    }
    
    public function getTotalAnnualAmount()
    {
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();
            
        return $subscriptions->sum(function($subscription) {
            $multiplier = match($subscription->billing_cycle) {
                'monthly' => 12,
                'quarterly' => 4,
                'biannual' => 2,
                'annual' => 1,
                default => 12,
            };
            
            return $subscription->amount * $multiplier;
        });
    }
    
    public function getSubscriptionCount()
    {
        return MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->count();
    }
    
    public function getUpcomingPayments()
    {
        $thirtyDaysFromNow = Carbon::now()->addDays(30);
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->whereBetween('billing_date', [Carbon::now(), $thirtyDaysFromNow])
            ->orderBy('billing_date')
            ->get();
        
        $subscriptions->each(function($subscription) {
            $subscription->days_left = (int) Carbon::now()->diffInDays($subscription->billing_date);
            return $subscription;
        });
        
        return $subscriptions;
    }
    
    public function getCategoryBreakdown()
    {
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();
        
        if ($subscriptions->isEmpty()) {
            return collect();
        }
            
        return $subscriptions
            ->groupBy('category')
            ->map(function ($group) use ($subscriptions) {
                $totalMonthly = $this->getTotalMonthlyAmount();
                
                $monthlyCost = $group->sum(function($subscription) {
                    $divisor = match($subscription->billing_cycle) {
                        'monthly' => 1,
                        'quarterly' => 3,
                        'biannual' => 6,
                        'annual' => 12,
                        default => 1,
                    };
                    
                    return $subscription->amount / $divisor;
                });
                
                $percentage = $totalMonthly > 0 ? (int)(($monthlyCost / $totalMonthly) * 100) : 0;
                
                return [
                    'count' => $group->count(),
                    'total' => $monthlyCost,
                    'color' => $this->getCategoryColor($group->first()->category),
                    'percentage' => $percentage,
                ];
            });
    }
    
    public function getSubscriptionsSortedByMonthlyCost()
    {
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get(['id', 'name', 'amount', 'category', 'billing_cycle']);
        
        $subscriptionsWithMonthlyCost = $subscriptions->map(function($subscription) {
            $divisor = match($subscription->billing_cycle) {
                'monthly' => 1,
                'quarterly' => 3,
                'biannual' => 6,
                'annual' => 12,
                default => 1,
            };
            
            $monthlyCost = $subscription->amount / $divisor;
            
            return [
                'name' => $subscription->name,
                'category' => $subscription->category,
                'monthly_cost' => $monthlyCost
            ];
        });
        
        return $subscriptionsWithMonthlyCost
            ->sortByDesc('monthly_cost')
            ->take(10)
            ->values();
    }

    public function getAllActiveSubscriptions()
    {
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get(['id', 'name', 'amount', 'billing_date', 'billing_cycle', 'category']);
            
        $subscriptions->each(function($subscription) {
            $subscription->days_left = (int) Carbon::now()->diffInDays($subscription->billing_date);
            return $subscription;
        });
        
        return $subscriptions;
    }

    public function getCategoryColors()
    {
        return [
            'streaming' => '#ef4444', 
            'software' => '#3b82f6', 
            'cloud' => '#6366f1', 
            'membership' => '#10b981', 
            'utilities' => '#f59e0b', 
            'phone' => '#ec4899', 
            'education' => '#8b5cf6', 
            'health' => '#22c55e', 
            'gaming' => '#a855f7', 
            'news' => '#14b8a6', 
            'other' => '#6b7280'
        ];
    }
    
    protected function getCategoryColor($category)
    {
        return match($category) {
            'streaming' => '#ef4444',
            'software' => '#3b82f6',
            'cloud' => '#6366f1',
            'membership' => '#10b981',
            'utilities' => '#f59e0b',
            'phone' => '#ec4899',
            'education' => '#8b5cf6',
            'health' => '#22c55e',
            'gaming' => '#a855f7',
            'news' => '#14b8a6',
            default => '#6b7280',
        };
    }
    
    public function getByBillingCycle()
    {
        $subscriptions = MonthlySubscription::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();
            
        return $subscriptions
            ->groupBy('billing_cycle')
            ->map(function ($group) {
                $monthlyEquivalent = $group->sum(function($subscription) {
                    $divisor = match($subscription->billing_cycle) {
                        'monthly' => 1,
                        'quarterly' => 3,
                        'biannual' => 6,
                        'annual' => 12,
                        default => 1,
                    };
                    
                    return $subscription->amount / $divisor;
                });
                
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                    'monthly_equivalent' => $monthlyEquivalent,
                ];
            });
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_subscription')
                ->label('Add Subscription')
                ->url(route('filament.app.resources.monthly-subscriptions.create'))
                ->icon('heroicon-o-plus')
                ->color('primary'),
                
            Action::make('view_all')
                ->label('Manage Subscriptions')
                ->url(route('filament.app.resources.monthly-subscriptions.index'))
                ->icon('heroicon-o-rectangle-stack')
                ->color('gray'),
        ];
    }

    protected function getStylesheets(): array
    {
        return [
            'subscription-dashboard' => asset('css/subscription-dashboard.css'),
        ];
    }

    protected function getScripts(): array
    {
        return [
            'chart-js' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            'chartjs-plugin-datalabels' => 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0',
            'moment-js' => 'https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js',
            'subscription-dashboard' => asset('js/subscription-dashboard.js'),
        ];
    }
}