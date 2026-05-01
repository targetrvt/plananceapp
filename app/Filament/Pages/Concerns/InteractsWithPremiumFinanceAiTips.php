<?php

namespace App\Filament\Pages\Concerns;

use App\Exceptions\AiAccessDeniedException;
use App\Services\Finance\DashboardFinanceAiTipsService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

trait InteractsWithPremiumFinanceAiTips
{
    public ?string $premiumFinanceAiTipsContent = null;

    public bool $premiumFinanceAiTipsLoading = false;

    protected function resetPremiumFinanceAiTips(): void
    {
        $this->premiumFinanceAiTipsContent = null;
    }

    public function canShowPremiumFinanceAiTips(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'premium'
            && (bool) auth()->user()?->hasPremiumSubscription();
    }

    /** Static tips list is omitted on the premium panel; AI replaces them for subscribed users. */
    public function shouldShowDefaultFinanceTips(): bool
    {
        return Filament::getCurrentPanel()?->getId() !== 'premium';
    }

    /**
     * Premium AI tips/buttons after subscription & explicit AI permission.
     */
    public function canGeneratePremiumFinanceAiTips(): bool
    {
        return $this->canShowPremiumFinanceAiTips()
            && (bool) auth()->user()?->hasAiAccess();
    }

    /** Show contact email notice (Premium but AI not opted in). */
    public function showsPremiumAiAccessGateNotice(): bool
    {
        return $this->canShowPremiumFinanceAiTips()
            && auth()->check()
            && ! auth()->user()->hasAiAccess();
    }

    public function premiumAiAccessGateEmail(): string
    {
        return (string) config('planance.contact_ai_email');
    }

    public function generatePremiumIncomeAiTips(): void
    {
        $this->runPremiumFinanceAiTips(
            app(DashboardFinanceAiTipsService::class),
            'income_tips',
            $this->category,
            'all'
        );
    }

    public function generatePremiumSavingsAiTips(): void
    {
        $this->runPremiumFinanceAiTips(
            app(DashboardFinanceAiTipsService::class),
            'savings_tips',
            'all',
            $this->category
        );
    }

    /**
     * @param  'income_tips'|'savings_tips'  $variant
     */
    private function runPremiumFinanceAiTips(
        DashboardFinanceAiTipsService $service,
        string $variant,
        string $incomeCategoryFilter,
        string $expenseCategoryFilter,
    ): void {
        if (! $this->canShowPremiumFinanceAiTips()) {
            abort(403);
        }

        if (! $this->canGeneratePremiumFinanceAiTips()) {
            return;
        }

        $this->premiumFinanceAiTipsLoading = true;
        $this->premiumFinanceAiTipsContent = null;

        try {
            // Active Filament/UI language (session switches), aligns with translations.
            $locale = app()->getLocale();
            [$tipsUiTitle, $tipsUiScopeDescription] = $variant === 'income_tips'
                ? [
                    __('income-dashboard.ai_tips.card_title', [], $locale),
                    __('income-dashboard.ai_tips.card_description', [], $locale),
                ]
                : [
                    __('expenses-dashboard.ai_tips.card_title', [], $locale),
                    __('expenses-dashboard.ai_tips.card_description', [], $locale),
                ];

            $this->premiumFinanceAiTipsContent = $service->generate(
                (int) auth()->id(),
                $variant,
                (string) $this->startDate,
                (string) $this->endDate,
                (string) $this->timeframe,
                $incomeCategoryFilter,
                $expenseCategoryFilter,
                $locale,
                $tipsUiTitle,
                $tipsUiScopeDescription,
            );
        } catch (AiAccessDeniedException $e) {
            Notification::make()
                ->title(__('messages.ai_access.denied_title'))
                ->body($e->getMessage())
                ->warning()
                ->send();

            $this->premiumFinanceAiTipsContent = null;
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('messages.finance_ai_tips.error_title'))
                ->body(__('messages.finance_ai_tips.error_body'))
                ->danger()
                ->send();

            $this->premiumFinanceAiTipsContent = null;
        } finally {
            $this->premiumFinanceAiTipsLoading = false;
        }
    }
}
