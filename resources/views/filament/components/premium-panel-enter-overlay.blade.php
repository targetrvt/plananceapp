@php
    $showPremiumEnterTransition =
        filament()->getId() === 'premium'
        && session()->pull(\App\Filament\PremiumPanelEntryTransition::SESSION_KEY, false);
@endphp
@if ($showPremiumEnterTransition)
<div
    id="planance-premium-panel-overlay"
    class="planance-premium-panel-overlay"
    aria-hidden="true"
    role="presentation"
>
    <div class="planance-premium-panel-overlay__backdrop"></div>
    <div class="planance-premium-panel-overlay__content">
        <div class="planance-premium-panel-overlay__glow"></div>
        <div class="planance-premium-panel-overlay__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="planance-premium-panel-overlay__sparkle-svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.575L16.5 21.75l-.394-1.175a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.175-.394a2.25 2.25 0 001.423-1.423L16.5 15.75l.394 1.175a2.25 2.25 0 001.423 1.423L19.5 18.75l-1.175.394a2.25 2.25 0 00-1.423 1.423z" />
            </svg>
        </div>
        <p class="planance-premium-panel-overlay__title">
            {{ __('messages.premium_panel_entry.title') }}
        </p>
        <p class="planance-premium-panel-overlay__subtitle">
            {{ __('messages.premium_panel_entry.subtitle') }}
        </p>
    </div>
</div>
<style>
.planance-premium-panel-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgb(255 255 255 / 0.95);
    pointer-events: all;
}

.planance-premium-panel-overlay__sparkle-svg {
    width: 3rem;
    height: 3rem;
}

.planance-premium-panel-overlay__backdrop {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        145deg,
        rgb(67 56 202 / 0.97),
        rgb(16 185 129 / 0.88),
        rgb(52 211 153 / 0.85)
    );
    animation: planancePremiumOverlayBackdrop 1.05s ease-out forwards;
}

.planance-premium-panel-overlay__content {
    position: relative;
    z-index: 1;
    text-align: center;
    padding: 1.75rem 2rem;
    max-width: 22rem;
    animation: planancePremiumOverlayContent 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

.planance-premium-panel-overlay__glow {
    position: absolute;
    inset: -40%;
    background: radial-gradient(circle at 50% 40%, rgb(255 255 255 / 0.2), transparent 55%);
    pointer-events: none;
    opacity: 0.85;
    animation: planancePremiumGlow 2.8s ease-in-out infinite alternate;
}

.planance-premium-panel-overlay__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    opacity: 0.95;
    filter: drop-shadow(0 4px 20px rgb(16 185 129 / 0.45));
}

.planance-premium-panel-overlay__title {
    font-size: 1.25rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    margin: 0;
}

.planance-premium-panel-overlay__subtitle {
    margin: 0.5rem 0 0;
    font-size: 0.875rem;
    opacity: 0.88;
}

.planance-premium-panel-overlay.planance-premium-panel-overlay--exit .planance-premium-panel-overlay__backdrop {
    animation: planancePremiumOverlayFadeOut 0.75s ease forwards;
}

.planance-premium-panel-overlay.planance-premium-panel-overlay--exit .planance-premium-panel-overlay__content {
    animation: planancePremiumOverlayContentOut 0.65s cubic-bezier(0.4, 0, 1, 1) forwards;
}

@keyframes planancePremiumOverlayBackdrop {
    from {
        opacity: 0;
        filter: blur(8px);
    }
    to {
        opacity: 1;
        filter: blur(0);
    }
}

@keyframes planancePremiumOverlayContent {
    from {
        opacity: 0;
        transform: translateY(16px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes planancePremiumGlow {
    from {
        opacity: 0.55;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1.06);
    }
}

@keyframes planancePremiumOverlayFadeOut {
    to {
        opacity: 0;
        filter: blur(4px);
    }
}

@keyframes planancePremiumOverlayContentOut {
    to {
        opacity: 0;
        transform: translateY(-12px) scale(0.98);
    }
}

@media (prefers-reduced-motion: reduce) {
    .planance-premium-panel-overlay,
    .planance-premium-panel-overlay__backdrop,
    .planance-premium-panel-overlay__content {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }

    .planance-premium-panel-overlay__glow {
        animation: none;
    }
}
</style>
<script>
(function () {
    const el = document.getElementById('planance-premium-panel-overlay');
    if (!el) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const removeOverlay = () => el.remove();

    if (reduceMotion) {
        removeOverlay();
        return;
    }

    const exitDelayMs = 950;
    const removeAfterExitMs = 820;

    const runExit = () => {
        el.classList.add('planance-premium-panel-overlay--exit');
        el.style.pointerEvents = 'none';
        window.setTimeout(removeOverlay, removeAfterExitMs);
    };

    window.setTimeout(runExit, exitDelayMs);
})();
</script>
@endif
