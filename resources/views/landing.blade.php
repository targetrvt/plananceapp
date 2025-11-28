<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planance - {{ __('messages.landing.hero.title') }}</title>
    <link rel="icon" href="favicon.ico" type="images/Planancelogomini.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/planance.css') }}" rel="stylesheet">
</head>
<body class="dark-theme">
    <!-- Navigation -->
    <header class="dark-header">
        <div class="container">
            <nav class="nav-dark">
                <a href="/" class="logo-dark">
                    <img src="{{ asset('images/PlananceText.png') }}" alt="Planance" class="logo-text-image" style="height: 55px; width: auto; max-width: 340px;">
                </a>
                
                <div class="nav-center">
                    <div class="nav-links-dark">
                        <a href="#features">{{ __('messages.landing.nav.features') }}</a>
                        <a href="#pricing">{{ __('messages.landing.nav.pricing') }}</a>
                        <a href="#testimonials">{{ __('messages.landing.nav.testimonials') }}</a>
                        <a href="#faq">{{ __('messages.landing.nav.faq') }}</a>
                    </div>
                    
                    <div class="nav-actions">
                        <a href="/app/login" class="btn-dark-outline">{{ __('messages.landing.nav.login') }}</a>
                        <a href="/app/register" class="btn-dark-primary">{{ __('messages.landing.nav.signup') }}</a>
                    </div>
                </div>
                
                <!-- Language Switcher -->
                <div class="dark-language-switch {{ app()->getLocale() === 'lv' ? 'lv-active' : '' }}">
                    <a href="{{ route('lang.switch', 'en') }}" class="dark-lang-option {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                        <span class="flag-emoji">🇬🇧</span>
                    </a>
                    <a href="{{ route('lang.switch', 'lv') }}" class="dark-lang-option {{ app()->getLocale() === 'lv' ? 'active' : '' }}">
                        <span class="flag-emoji">🇱🇻</span>
                    </a>
                </div>
                
                <button class="mobile-menu-toggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu-dark">
        <div class="mobile-menu-content">
            <a href="#features">{{ __('messages.landing.nav.features') }}</a>
            <a href="#pricing">{{ __('messages.landing.nav.pricing') }}</a>
            <a href="#testimonials">{{ __('messages.landing.nav.testimonials') }}</a>
            <a href="#faq">{{ __('messages.landing.nav.faq') }}</a>
            
            <!-- Mobile Language Switcher -->
            <div class="mobile-dark-language-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="dark-lang-option {{ app()->getLocale() === 'en' ? 'active' : '' }}">
                    <span class="flag-emoji">🇬🇧</span> English
                </a>
                <a href="{{ route('lang.switch', 'lv') }}" class="dark-lang-option {{ app()->getLocale() === 'lv' ? 'active' : '' }}">
                    <span class="flag-emoji">🇱🇻</span> Latviešu
                </a>
            </div>
            
            <div class="mobile-menu-buttons">
                <a href="/app/login" class="btn-dark-outline">{{ __('messages.landing.nav.login') }}</a>
                <a href="/app/register" class="btn-dark-primary">{{ __('messages.landing.nav.signup') }}</a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-dark">
        <div class="hero-gradient"></div>
        <div class="hero-grid-pattern"></div>
        <div class="container">
            <div class="hero-content-dark">
                <div class="hero-badge">{{ __('messages.landing.hero.tag') }}</div>
                <h1 class="hero-title-dark">{{ __('messages.landing.hero.title') }}</h1>
                <p class="hero-description-dark">{{ __('messages.landing.hero.description') }}</p>
                
                <div class="hero-cta-dark">
                    <a href="/app/register" class="btn-hero-primary">
                        {{ __('messages.landing.hero.start_trial') }}
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.16663 10H15.8333M15.8333 10L9.99996 4.16669M15.8333 10L9.99996 15.8334" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="#features" class="btn-hero-secondary">{{ __('messages.landing.hero.explore_features') }}</a>
                </div>
            </div>
            
            <div class="hero-image-dark">
                <div class="image-glow"></div>
                <img src="images/PlananceDashboard.png" alt="Planance Dashboard" class="dashboard-preview">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-dark">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item-dark">
                    <div class="stat-value-dark">0+</div>
                    <div class="stat-label-dark">{{ __('messages.landing.hero.stats.active_users') }}</div>
                </div>
                <div class="stat-item-dark">
                    <div class="stat-value-dark">0+</div>
                    <div class="stat-label-dark">{{ __('messages.landing.hero.stats.managed_monthly') }}</div>
                </div>
                <div class="stat-item-dark">
                    <div class="stat-value-dark">%+</div>
                    <div class="stat-label-dark">{{ __('messages.landing.hero.stats.satisfaction') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Intro Section -->
    <section class="features-intro-dark" id="features">
        <div class="container">
            <div class="section-header-dark">
                <span class="section-tag-dark">{{ __('messages.landing.features.tag') }}</span>
                <h2>{{ __('messages.landing.features.title') }}</h2>
                <p>{{ __('messages.landing.features.description') }}</p>
            </div>
        </div>
    </section>

    <!-- Main Features Grid -->
    <section class="features-main-dark">
        <div class="container">
            <div class="features-grid-dark">
                <!-- Feature 1: Real-time -->
                <div class="feature-card-dark">
                    <div class="feature-icon-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                    </div>
                    <h3>{{ __('messages.landing.features.real_time.title') }}</h3>
                    <p>{{ __('messages.landing.features.real_time.description') }}</p>
                </div>

                <!-- Feature 2: Smart Budgeting -->
                <div class="feature-card-dark">
                    <div class="feature-icon-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                            <line x1="4" y1="22" x2="4" y2="15"></line>
                        </svg>
                    </div>
                    <h3>{{ __('messages.landing.features.smart_budgeting.title') }}</h3>
                    <p>{{ __('messages.landing.features.smart_budgeting.description') }}</p>
                </div>

                <!-- Feature 3: Intelligent Insights -->
                <div class="feature-card-dark">
                    <div class="feature-icon-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <h3>{{ __('messages.landing.features.intelligent_insights.title') }}</h3>
                    <p>{{ __('messages.landing.features.intelligent_insights.description') }}</p>
                </div>

                <!-- Feature 4: Multi-user -->
                <div class="feature-card-dark">
                    <div class="feature-icon-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>{{ __('messages.landing.features.multi_user.title') }}</h3>
                    <p>{{ __('messages.landing.features.multi_user.description') }}</p>
                </div>

                <!-- Feature 5: Budget Management -->
                <div class="feature-card-dark">
                    <div class="feature-icon-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M16 12h-6"></path>
                            <path d="M12 8v8"></path>
                        </svg>
                    </div>
                    <h3>{{ __('messages.landing.features.core_features.budget_management.title') }}</h3>
                    <p>{{ __('messages.landing.features.core_features.budget_management.description') }}</p>
                </div>

                <!-- Feature 6: Secure -->
                <div class="feature-card-dark">
                    <div class="feature-icon-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h3>{{ __('messages.landing.features.core_features.secure.title') }}</h3>
                    <p>{{ __('messages.landing.features.core_features.secure.description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-dark" id="pricing">
        <div class="container">
            <div class="section-header-dark">
                <span class="section-tag-dark">{{ __('messages.landing.pricing.tag') }}</span>
                <h2>{{ __('messages.landing.pricing.title') }}</h2>
                <p>{{ __('messages.landing.pricing.description') }}</p>
            </div>

            <div class="pricing-grid-dark">
                <!-- Personal Plan -->
                <div class="pricing-card-dark">
                    <div class="pricing-header-dark">
                        <h3>{{ __('messages.landing.pricing.personal.title') }}</h3>
                        <p>{{ __('messages.landing.pricing.personal.description') }}</p>
                    </div>
                    <div class="pricing-price-dark">
                        <span class="currency">€</span>
                        <span class="amount">*</span>
                        <span class="period">/mo</span>
                    </div>
                    <ul class="pricing-features-dark">
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.personal.features.unlimited_categories') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.personal.features.expense_tracking') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.personal.features.goal_setting') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.personal.features.basic_reports') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.personal.features.mobile_access') }}
                        </li>
                    </ul>
                    <a href="/app/register" class="btn-pricing-dark">{{ __('messages.landing.pricing.get_started') }}</a>
                </div>

                <!-- Premium Plan (Featured) -->
                <div class="pricing-card-dark featured">
                    <div class="popular-badge">Most Popular</div>
                    <div class="pricing-header-dark">
                        <h3>{{ __('messages.landing.pricing.premium.title') }}</h3>
                        <p>{{ __('messages.landing.pricing.premium.description') }}</p>
                    </div>
                    <div class="pricing-price-dark">
                        <span class="currency">€</span>
                        <span class="amount">**</span>
                        <span class="period">/mo</span>
                    </div>
                    <ul class="pricing-features-dark">
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.premium.features.everything_personal') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.premium.features.receipt_scanning') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.premium.features.advanced_analytics') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.premium.features.custom_categories') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.premium.features.priority_support') }}
                        </li>
                    </ul>
                    <a href="/app/register" class="btn-pricing-dark featured">{{ __('messages.landing.pricing.get_started') }}</a>
                </div>

                <!-- Business Plan -->
                <div class="pricing-card-dark">
                    <div class="pricing-header-dark">
                        <h3>{{ __('messages.landing.pricing.business.title') }}</h3>
                        <p>{{ __('messages.landing.pricing.business.description') }}</p>
                    </div>
                    <div class="pricing-price-dark">
                        <span class="currency">€</span>
                        <span class="amount">***</span>
                        <span class="period">/mo</span>
                    </div>
                    <ul class="pricing-features-dark">
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.business.features.everything_premium') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.business.features.multiple_users') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.business.features.team_collaboration') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.business.features.role_permissions') }}
                        </li>
                        <li>
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('messages.landing.pricing.business.features.dedicated_support') }}
                        </li>
                    </ul>
                    <a href="/app/register" class="btn-pricing-dark">{{ __('messages.landing.pricing.get_started') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-dark" id="testimonials">
        <div class="container">
            <div class="section-header-dark">
                <span class="section-tag-dark">{{ __('messages.landing.testimonials.tag') }}</span>
                <h2>{{ __('messages.landing.testimonials.title') }}</h2>
                <p>{{ __('messages.landing.testimonials.description') }}</p>
            </div>

            <div class="testimonials-grid-dark">
                <div class="testimonial-card-dark">
                    <p>"x"</p>
                    <div class="testimonial-author-dark">
                        <div class="author-avatar">x</div>
                        <div>
                            <h4>x</h4>
                            <span>x</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card-dark">
                    <p>"x"</p>
                    <div class="testimonial-author-dark">
                        <div class="author-avatar">x</div>
                        <div>
                            <h4>x</h4>
                            <span>x</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card-dark">
                    <p>"x"</p>
                    <div class="testimonial-author-dark">
                        <div class="author-avatar">x</div>
                        <div>
                            <h4>x</h4>
                            <span>x</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-dark" id="faq">
        <div class="container">
            <div class="section-header-dark">
                <span class="section-tag-dark">{{ __('messages.landing.faq.tag') }}</span>
                <h2>{{ __('messages.landing.faq.title') }}</h2>
                <p>{{ __('messages.landing.faq.description') }}</p>
            </div>

            <div class="faq-list-dark">
                <div class="faq-item-dark active">
                    <button class="faq-question-dark">
                        <span>{{ __('messages.landing.faq.security.question') }}</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer-dark">
                        <p>{{ __('messages.landing.faq.security.answer') }}</p>
                    </div>
                </div>

                <div class="faq-item-dark">
                    <button class="faq-question-dark">
                        <span>{{ __('messages.landing.faq.sharing.question') }}</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer-dark">
                        <p>{{ __('messages.landing.faq.sharing.answer') }}</p>
                    </div>
                </div>

                <div class="faq-item-dark">
                    <button class="faq-question-dark">
                        <span>{{ __('messages.landing.faq.trial.question') }}</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer-dark">
                        <p>{{ __('messages.landing.faq.trial.answer') }}</p>
                    </div>
                </div>

                <div class="faq-item-dark">
                    <button class="faq-question-dark">
                        <span>{{ __('messages.landing.faq.support.question') }}</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="faq-answer-dark">
                        <p>{{ __('messages.landing.faq.support.answer') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-dark">
        <div class="container">
            <div class="cta-content-dark">
                <h2>{{ __('messages.landing.cta.title') }}</h2>
                <p>{{ __('messages.landing.cta.description') }}</p>
                <a href="/app/register" class="btn-cta-primary">
                    {{ __('messages.landing.cta.start_trial') }}
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M4.16663 10H15.8333M15.8333 10L9.99996 4.16669M15.8333 10L9.99996 15.8334" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-dark">
        <div class="container">
            <div class="footer-content-dark">
                <div class="footer-brand">
                    <div class="footer-logo-dark">
                        <img src="{{ asset('images/PlananceText.png') }}" alt="Planance" class="logo-text-image">
                    </div>
                    <p>{{ __('messages.landing.footer.description') }}</p>
                    <div class="social-links-dark">
                        <a href="#" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33c-3.28,0-5.37,1.93-5.37,5.48v1.48H6.16v4h2.64V23.5h5.7V11.46h3.53l.7-4Z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.32,6.44a.5.5,0,0,0-.2-.87l-.79-.19A.49.49,0,0,1,22,4.67L21.75,4a.5.5,0,0,0-.58-.35l-1.74.4a.5.5,0,0,1-.44-.08l-.58-.43a3,3,0,0,0-4,.46l-2.07,2.09a3,3,0,0,0-.89,2.11v.63a.49.49,0,0,1-.48.5C8.47,9.4,5.54,7.86,2.46,4.31a.5.5,0,0,0-.83.21A10.58,10.58,0,0,0,4.2,13.12a.5.5,0,0,1-.39.81H2.95a.5.5,0,0,0-.41.76l.07.1A7.62,7.62,0,0,0,7.36,18a.5.5,0,0,1,.1.82A12.49,12.49,0,0,1,.91,21a.5.5,0,0,0-.25.87,16,16,0,0,0,8.42,2.36c8.28,0,13.9-6.51,13.9-13.91,0-.2,0-.41,0-.62a9.41,9.41,0,0,0,2.26-2.42.5.5,0,0,0-.66-.73l-1.56.9a.5.5,0,0,1-.73-.16Z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5,3.5A1.5,1.5,0,1,1,3.5,2,1.5,1.5,0,0,1,5,3.5ZM5,5H2V19H5Zm6.32,0H8.25V19h3.07V13c0-2.89,3.35-3.1,3.35,0v6h3.07V12.38c0-4.87-5.55-4.69-6.42-2.31V5Z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="footer-links-dark">
                    <h4>{{ __('messages.landing.footer.company') }}</h4>
                    <a href="#">{{ __('messages.landing.footer.links.about') }}</a>
                    <a href="#">{{ __('messages.landing.footer.links.careers') }}</a>
                    <a href="#">{{ __('messages.landing.footer.links.blog') }}</a>
                </div>

                <div class="footer-links-dark">
                    <h4>{{ __('messages.landing.footer.products') }}</h4>
                    <a href="#">{{ __('messages.landing.footer.links.personal_finance') }}</a>
                    <a href="#">{{ __('messages.landing.footer.links.business_solutions') }}</a>
                    <a href="#">{{ __('messages.landing.footer.links.api_docs') }}</a>
                </div>

                <div class="footer-links-dark">
                    <h4>{{ __('messages.landing.footer.support') }}</h4>
                    <a href="#">{{ __('messages.landing.footer.links.help_center') }}</a>
                    <a href="#">{{ __('messages.landing.footer.links.contact') }}</a>
                    <a href="#faq">{{ __('messages.landing.footer.links.faq') }}</a>
                </div>
            </div>

            <div class="footer-bottom-dark">
                <p>{{ __('messages.landing.footer.copyright') }}</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/planance.js') }}"></script>
</body>
</html>