<x-filament-panels::page>
    @php
        $goalSummaries = $this->getGoalSummaries();
        $totalTarget = $this->getTotalTargetAmount();
        $totalSaved = $this->getTotalSavedAmount();
        $totalRemaining = $this->getTotalRemainingToTargets();
        $overdueCount = $this->getOverdueGoalsCount();
        $overallPct = $this->getOverallSavedProgressPercent();
        $overallBarW = $totalTarget > 0 ? min(100, max(0, $overallPct)) : 0;
    @endphp

    <link rel="stylesheet" href="{{ asset('css/planance-financial-goals-dashboard.css') }}">

    <div class="goals-dashboard space-y-0">
        <div class="goals-dashboard__summary-card">
            <header class="goals-dashboard__intro">
                <p class="goals-dashboard__eyebrow">{{ __('financial-goals-dashboard.sections.eyebrow') }}</p>
                <p class="goals-dashboard__lede">{{ __('financial-goals-dashboard.sections.lede') }}</p>
            </header>

            <hr class="goals-dashboard__summary-divider" />

            <section class="gd-ribbon" aria-label="{{ __('financial-goals-dashboard.title') }}">
                <div class="gd-ribbon__track">
                    <span class="gd-ribbon__track-label">{{ __('financial-goals-dashboard.ribbon.overall_label') }}</span>
                    <div class="gd-ribbon__track-bar" role="presentation">
                        <span class="gd-ribbon__track-fill" style="width: {{ $overallBarW }}%;"></span>
                    </div>
                    <div class="gd-ribbon__track-meta">
                        {{ number_format($overallPct, 1) }}%
                        <span class="text-gray-400 dark:text-gray-500 font-normal">·</span>
                        EUR {{ number_format($totalSaved, 2) }}
                        <span class="text-gray-400 dark:text-gray-500 font-normal">/</span>
                        EUR {{ number_format($totalTarget, 2) }}
                    </div>
                </div>
                <ul class="gd-ribbon__metrics">
                    <li>
                        <span class="gd-ribbon__metric-label">{{ __('financial-goals-dashboard.stats.total_target') }}</span>
                        <span class="gd-ribbon__metric-value">EUR {{ number_format($totalTarget, 2) }}</span>
                    </li>
                    <li>
                        <span class="gd-ribbon__metric-label">{{ __('financial-goals-dashboard.stats.total_saved') }}</span>
                        <span class="gd-ribbon__metric-value">EUR {{ number_format($totalSaved, 2) }}</span>
                    </li>
                    <li>
                        <span class="gd-ribbon__metric-label">{{ __('financial-goals-dashboard.stats.remaining_to_goals') }}</span>
                        <span class="gd-ribbon__metric-value">EUR {{ number_format($totalRemaining, 2) }}</span>
                    </li>
                    <li>
                        <span class="gd-ribbon__metric-label">{{ __('financial-goals-dashboard.stats.overdue_goals') }}</span>
                        <span class="gd-ribbon__metric-value {{ $overdueCount > 0 ? 'gd-ribbon__metric-value--alert' : 'gd-ribbon__metric-value--ok' }}">{{ $overdueCount }}</span>
                    </li>
                </ul>
            </section>
        </div>

        <div class="goals-dashboard__list-head">
            <h2 class="goals-dashboard__list-title">{{ __('financial-goals-dashboard.sections.goals_overview') }}</h2>
            <span class="goals-dashboard__list-count">{{ __('financial-goals-dashboard.sections.active_count', ['count' => $goalSummaries->count()]) }}</span>
        </div>

        @if($goalSummaries->isNotEmpty())
            <div class="goals-dashboard__grid">
                @foreach($goalSummaries as $goal)
                    @php
                        $p = (float) $goal['progress'];
                        $labelPct = min($p, 999);
                        $progressWidth = $goal['is_complete']
                            ? 100
                            : ($p > 0 ? max(2, min(100, $p)) : 0);
                        if ($goal['is_complete']) {
                            $progressColor = 'linear-gradient(90deg, #3b82f6, #60a5fa)';
                            $badgeColor = '#2563eb';
                            $statusLabel = __('financial-goals-dashboard.status.reached');
                        } elseif ($goal['is_overdue']) {
                            $progressColor = 'linear-gradient(90deg, #f43f5e, #fb7185)';
                            $badgeColor = '#e11d48';
                            $statusLabel = __('financial-goals-dashboard.status.overdue');
                        } elseif ($p >= 90) {
                            $progressColor = 'linear-gradient(90deg, #f59e0b, #fbbf24)';
                            $badgeColor = '#d97706';
                            $statusLabel = __('financial-goals-dashboard.status.almost_there');
                        } else {
                            $progressColor = 'linear-gradient(90deg, #7c3aed, #c084fc)';
                            $badgeColor = '#7c3aed';
                            $statusLabel = __('financial-goals-dashboard.status.in_progress');
                        }
                        $tileModifier = $goal['is_complete']
                            ? 'goal-tile--complete'
                            : ($goal['is_overdue'] ? 'goal-tile--over' : ($p >= 90 ? 'goal-tile--warning' : ''));
                    @endphp

                    <article class="goal-tile {{ $tileModifier }}">
                        <span class="goal-tile__accent" aria-hidden="true"></span>
                        <div class="goal-tile__head">
                            <span class="goal-tile__name">{{ $goal['name'] }}</span>
                            <span class="goal-tile__badge" style="background-color: {{ $badgeColor }}; border-color: {{ $badgeColor }};">{{ $statusLabel }}</span>
                        </div>
                        <div class="goal-tile__date">
                            <x-heroicon-o-calendar-days class="h-3.5 w-3.5 shrink-0 opacity-80" />
                            @if(filled($goal['target_date']))
                                {{ \Carbon\Carbon::parse($goal['target_date'])->format('M d, Y') }}
                            @else
                                —
                            @endif
                        </div>
                        <dl class="goal-tile__amounts">
                            <div>
                                <dt>{{ __('financial-goals-dashboard.table.target') }}</dt>
                                <dd>EUR {{ number_format($goal['target_amount'], 2) }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('financial-goals-dashboard.table.saved') }}</dt>
                                <dd>EUR {{ number_format($goal['current_amount'], 2) }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('financial-goals-dashboard.table.remaining') }}</dt>
                                <dd>EUR {{ number_format($goal['remaining'], 2) }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('financial-goals-dashboard.table.days_to_deadline') }}</dt>
                                <dd @class(['goal-tile__days-value', 'is-alert' => $goal['days_remaining'] < 0 && ! $goal['is_complete']])>
                                    @if($goal['is_complete'])
                                        {{ __('financial-goals-dashboard.table.days_when_complete') }}
                                    @elseif(! filled($goal['target_date']))
                                        —
                                    @elseif($goal['days_remaining'] < 0)
                                        {{ trans_choice('financial-goals-dashboard.table.days_over_labels', abs($goal['days_remaining']), ['count' => abs($goal['days_remaining'])]) }}
                                    @else
                                        {{ trans_choice('financial-goals-dashboard.table.days_until_labels', $goal['days_remaining'], ['count' => $goal['days_remaining']]) }}
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        <div class="goal-tile__progress-wrap">
                            <div class="goal-tile__progress-labels">
                                <span>{{ __('financial-goals-dashboard.table.progress') }}</span>
                                <span>{{ number_format($labelPct, 1) }}%</span>
                            </div>
                            <div class="goal-tile__track" role="presentation">
                                <span class="goal-tile__fill" style="width: {{ $progressWidth }}%; background: {{ $progressColor }};"></span>
                            </div>
                        </div>
                        <div class="goal-tile__footer">
                            <x-filament::button
                                tag="a"
                                size="sm"
                                color="gray"
                                href="{{ \App\Filament\Resources\FinancialGoalResource::getUrl('edit', ['record' => $goal['id']]) }}"
                            >
                                {{ __('financial-goals-dashboard.actions.edit_goal.label') }}
                            </x-filament::button>
                            <div class="goal-tile__footer-actions">
                                <x-filament::button
                                    type="button"
                                    size="sm"
                                    color="success"
                                    wire:click="mountAction('quickAddToGoal', { goalId: {{ (int) $goal['id'] }} })"
                                >
                                    {{ __('financial-goals-dashboard.quick_add.trigger_label') }}
                                </x-filament::button>
                                @if((float) ($goal['current_amount'] ?? 0) > 0)
                                    <x-filament::button
                                        type="button"
                                        size="sm"
                                        color="warning"
                                        wire:click="mountAction('quickWithdrawFromGoal', { goalId: {{ (int) $goal['id'] }} })"
                                    >
                                        {{ __('financial-goals-dashboard.withdraw.trigger_label') }}
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="goals-dashboard__empty">
                <h3>{{ __('financial-goals-dashboard.empty.title') }}</h3>
                <p>{{ __('financial-goals-dashboard.empty.description') }}</p>
                <a href="{{ \App\Filament\Resources\FinancialGoalResource::getUrl('create') }}">
                    {{ __('financial-goals-dashboard.actions.create_goal.label') }}
                </a>
            </div>
        @endif
    </div>
</x-filament-panels::page>
