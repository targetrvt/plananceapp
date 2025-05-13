<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planance - Financial Planning</title>
    <link rel="icon" href="favicon.ico" type="images/Planancelogomini.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/planance.css') }}" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <nav class="nav">
                <a href="/" class="logo">
                    Planance
                </a>
                <div class="nav-links">
                    <a href="#features">Features</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#testimonials">Testimonials</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="auth-buttons">
                    <a href="/app/login" class="btn btn-outline">Log In</a>
                    <a href="/app/register" class="btn btn-primary">Sign Up</a>
                </div>
                <div class="mobile-nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>
    
    <div class="mobile-nav">
        <div class="mobile-nav-links">
            <a href="#features">Features</a>
            <a href="#pricing">Pricing</a>
            <a href="#testimonials">Testimonials</a>
            <a href="#faq">FAQ</a>
        </div>
        <div class="mobile-auth-buttons">
            <a href="/app/login" class="btn btn-outline">Log In</a>
            <a href="/app/register" class="btn btn-primary">Sign Up</a>
        </div>
    </div>

    <section class="hero">
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
        <div class="container">
            <div class="hero" style="padding-top: 0;">
                <div class="hero-content">
                    <div class="hero-tag">Smart Finance Management</div>
                    <h1>Take Control of Your Financial Future</h1>
                    <p>Planance helps individuals and businesses manage their finances with intelligent budgeting, expense tracking, and goal planning—all in one elegant platform.</p>
                    <div class="hero-cta">
                        <a href="/app/register" class="btn btn-primary">Start Free Trial</a>
                        <a href="#features" class="btn btn-outline">Explore Features</a>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-item">
                            <div class="stat-value">0+</div>
                            <div class="stat-label">Active Users</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">0+</div>
                            <div class="stat-label">Managed Monthly</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">%+</div>
                            <div class="stat-label">Customer Satisfaction</div>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="images/PlananceDashboard.png" alt="Planance Dashboard">
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <div class="section-tag">Powerful Tools</div>
                <h2>Everything You Need to Master Your Finances</h2>
                <p>This intuitive platform is packed with powerful features designed to give you complete control of your financial journey.</p>
            </div>
            
            <div class="features-wrapper">
                <div class="features-left">
                    <h3>Discover How Planance Works for You</h3>
                    <p>Explore my comprehensive suite of financial tools designed to help you budget, track, and grow your finances with confidence.</p>
                    
                    <div class="features-tabs">
                        <div class="feature-tab active">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 6v6l4 2"></path>
                                </svg>
                                Real-Time Tracking
                            </h4>
                            <p>Monitor your finances as they happen with instant updates and alerts.</p>
                        </div>
                        <div class="feature-tab">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                    <line x1="4" y1="22" x2="4" y2="15"></line>
                                </svg>
                                Smart Budgeting
                            </h4>
                            <p>Create customized budgets that adapt to your spending patterns.</p>
                        </div>
                        <div class="feature-tab">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                                Intelligent Insights
                            </h4>
                            <p>Get personalized recommendations and insights based on your financial behavior.</p>
                        </div>
                        <div class="feature-tab">
                            <h4>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Multi-User Access
                            </h4>
                            <p>Share financial management with family or team members with custom permissions.</p>
                        </div>
                    </div>
                </div>
                
                <div class="features-right">
                    <div class="feature-showcase">
                        <img src="/api/placeholder/600/400" alt="Planance Feature Showcase">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="features-grid">
        <div class="container">
            <div class="section-title">
                <div class="section-tag">Core Features</div>
                <h2>Smart Tools for Better Financial Management</h2>
                <p>Discover all the powerful features that make Planance the ultimate financial planning platform.</p>
            </div>
            
            <div class="cards-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M16 12h-6"></path>
                            <path d="M12 8v8"></path>
                        </svg>
                    </div>
                    <h3>Budget Management</h3>
                    <p>Create and manage detailed budgets with customizable categories and automatic tracking to stay on top of your spending.</p>
                    <a href="#" class="feature-more">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <h3>Expense Tracking</h3>
                    <p>Effortlessly track your expenses with receipt scanning technology and automatic categorization of transactions.</p>
                    <a href="#" class="feature-more">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <h3>Financial Goals</h3>
                    <p>Set and track your financial goals with visual progress indicators and smart recommendations to help you achieve them faster.</p>
                    <a href="#" class="feature-more">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="21" x2="9" y2="9"></line>
                        </svg>
                    </div>
                    <h3>Insightful Reports</h3>
                    <p>Access detailed financial reports and analytics to gain insights into your spending patterns and make informed decisions.</p>
                    <a href="#" class="feature-more">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h3>Secure & Private</h3>
                    <p>Your financial data is protected with bank-level security and encryption. We prioritize your privacy and data protection.</p>
                    <a href="#" class="feature-more">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>Team Collaboration</h3>
                    <p>Perfect for businesses and families - collaborate on shared financial planning with customizable access levels.</p>
                    <a href="#" class="feature-more">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <div class="section-tag">Testimonials</div>
                <h2>What Are Users Saying</h2>
                <p>Join individuals and businesses who have transformed their financial management with Planance.</p>
            </div>
            
            <div class="testimonials-wrapper">
                <div class="testimonials-left">
                    <div class="testimonials-title">
                        <h3>Trusted by Financial Decision-Makers</h3>
                        <p>Planance has helped individuals and businesses around the world take control of their finances and achieve their financial goals.</p>
                    </div>
                    

                </div>
                
                <div class="testimonials-right">
                    <div class="testimonials-grid">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"x"</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="/api/placeholder/50/50" alt="x">
                                <div class="author-info">
                                    <h4>x</h4>
                                    <p>x</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"x"</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="/api/placeholder/50/50" alt="x">
                                <div class="author-info">
                                    <h4>x</h4>
                                    <p>x</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"x"</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="/api/placeholder/50/50" alt="x">
                                <div class="author-info">
                                    <h4>x</h4>
                                    <p>x</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"x"</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="/api/placeholder/50/50" alt="x">
                                <div class="author-info">
                                    <h4>x</h4>
                                    <p>x</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-title">
                <div class="section-tag">Pricing</div>
                <h2>Simple, Transparent Pricing Plans</h2>
                <p>Choose the plan that fits your needs, whether you're an individual or a business.</p>
            </div>
            
            <div class="pricing-toggle">
                <span class="active">Monthly</span>
                <div class="pricing-toggle-pill"></div>
                <span>Yearly <span class="pricing-discount">Save 20%</span></span>
            </div>
            
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Personal</h3>
                    <p class="pricing-description">Perfect for individuals looking to manage personal finances.</p>
                    <div class="price">
                        <span class="price-currency">€</span>*<span class="price-period">/mo</span>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Unlimited Budget Categories
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Expense Tracking
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Financial Goal Setting
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Basic Reports
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Mobile App Access
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Email Support
                        </li>
                    </ul>
                    <a href="/app/register" class="btn btn-outline">Get Started</a>
                </div>
                
                <div class="pricing-card featured">
                    <h3>Premium</h3>
                    <p class="pricing-description">Advanced features for finance enthusiasts and professionals.</p>
                    <div class="price">
                        <span class="price-currency">€</span>**<span class="price-period">/mo</span>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Everything in Personal
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Receipt Scanning
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Advanced Analytics
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Custom Categories
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Export Data (CSV, PDF)
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Priority Support
                        </li>
                    </ul>
                    <a href="/app/register" class="btn btn-primary">Get Started</a>
                </div>
                
                <div class="pricing-card">
                    <h3>Business</h3>
                    <p class="pricing-description">Collaborative tools for teams and businesses of all sizes.</p>
                    <div class="price">
                        <span class="price-currency">€</span>***<span class="price-period">/mo</span>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Everything in Premium
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Multiple Users (up to 5)
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Team Collaboration
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Role-Based Permissions
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            API Access
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Dedicated Support
                        </li>
                    </ul>
                    <a href="/app/register" class="btn btn-outline">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <section class="app-showcase">
        <div class="container">
            <div class="app-wrapper">
                <div class="app-content">
                    <div class="section-tag">Mobile App</div>
                    <h1>COMING SOON</h1>
                    <h2>Take Planance Wherever You Go</h2>
                    <p>Mobile app will allow you to track expenses on the go, scan receipts instantly, and stay on top of your finances anywhere, anytime. Get notified when it's available!</p>
                    
                    <div class="app-buttons">
                        <a href="#" class="app-button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"></path>
                            </svg>
                            <div class="app-button-content">
                                <span>COMING SOON ON</span>
                                <span>App Store</span>
                            </div>
                        </a>
                        
                        <a href="#" class="app-button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="3 3 21 12 3 21 3 3"></polygon>
                            </svg>
                            <div class="app-button-content">
                                <span>COMING SOON ON</span>
                                <span>Google Play</span>
                            </div>
                        </a>
                    </div>
                </div>
                
                <div class="app-showcase-image">
                    <div class="app-circles">
                        <div class="circle circle-1"></div>
                        <div class="circle circle-2"></div>
                        <div class="circle circle-3"></div>
                    </div>
                    
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div class="screen-content">
                                <div class="app-header">
                                    <div class="app-header-logo">Planance</div>
                                </div>
                                
                                <div class="wireframe-elements">
                                    <div class="wireframe-box box-sm"></div>
                                    <div class="wireframe-box box-md"></div>
                                    <div class="wireframe-box box-lg"></div>
                                    <div class="wireframe-box box-sm"></div>
                                    <div class="wireframe-box box-md"></div>
                                </div>
                                
                                <div class="dev-indicator">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v4M12 18v4M4.93 4.93L7.76 7.76M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                                    </svg>
                                    <h3>Under Development</h3>
                                    <p>We're working hard to bring you the best experience</p>
                                    <div class="dev-progress">
                                        <div class="dev-progress-bar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq" id="faq">
        <div class="container">
            <div class="section-title">
                <div class="section-tag">FAQ</div>
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about Planance and how it can help you manage your finances.</p>
            </div>
            
            <div class="faq-wrapper">
                <div class="faq-item active">
                    <div class="faq-question">
                        How secure is my financial data with Planance?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        <p>At Planance, I take security seriously. I use bank-level encryption and security practices to ensure your data is always protected. I never share your personal information with third parties without your consent.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        Can I access Planance on multiple devices?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        <p>Yes! Planance is available on web browsers, iOS, and Android devices. Your data is automatically synced across all your devices, so you can manage your finances whenever and wherever it's convenient for you.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        Can I share access with my spouse or business partner?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        <p>Absolutely! Premium and Business plans allow for shared access with customizable permissions. You can control exactly what each user can view or modify, making it perfect for families, businesses, and financial advisors.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        Is there a free trial available?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, Planance offers a 14-day free trial on all Planance plans. No credit card is required to start your trial, and you can upgrade or cancel at any time.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        How do I get support if I have questions?
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        <p>Support team is available via email, live chat, and phone. Premium and Business users get priority support with faster response times. We also have an extensive knowledge base and video tutorials to help you get the most out of Planance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-shape-1"></div>
        <div class="cta-shape-2"></div>
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Take Control of Your Finances?</h2>
                <p>Join thousands of users who are already managing their finances smarter with Planance. Start your free 14-day trial today - no credit card required.</p>
                <a href="/app/register" class="btn">Start Free Trial</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-img">P</div>
                        Planance
                    </div>
                    <p>Smart financial planning for individuals and businesses. Take control of your finances with Planance's intuitive tools and powerful insights.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33c-3.28,0-5.37,1.93-5.37,5.48v1.48H6.16v4h2.64V23.5h5.7V11.46h3.53l.7-4Z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M23.32,6.44a.5.5,0,0,0-.2-.87l-.79-.19A.49.49,0,0,1,22,4.67L21.75,4a.5.5,0,0,0-.58-.35l-1.74.4a.5.5,0,0,1-.44-.08l-.58-.43a3,3,0,0,0-4,.46l-2.07,2.09a3,3,0,0,0-.89,2.11v.63a.49.49,0,0,1-.48.5C8.47,9.4,5.54,7.86,2.46,4.31a.5.5,0,0,0-.83.21A10.58,10.58,0,0,0,4.2,13.12a.5.5,0,0,1-.39.81H2.95a.5.5,0,0,0-.41.76l.07.1A7.62,7.62,0,0,0,7.36,18a.5.5,0,0,1,.1.82A12.49,12.49,0,0,1,.91,21a.5.5,0,0,0-.25.87,16,16,0,0,0,8.42,2.36c8.28,0,13.9-6.51,13.9-13.91,0-.2,0-.41,0-.62a9.41,9.41,0,0,0,2.26-2.42.5.5,0,0,0-.66-.73l-1.56.9a.5.5,0,0,1-.73-.16Z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M5,3.5A1.5,1.5,0,1,1,3.5,2,1.5,1.5,0,0,1,5,3.5ZM5,5H2V19H5Zm6.32,0H8.25V19h3.07V13c0-2.89,3.35-3.1,3.35,0v6h3.07V12.38c0-4.87-5.55-4.69-6.42-2.31V5Z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M17.34,5.46h0a1.2,1.2,0,1,0,1.2,1.2A1.2,1.2,0,0,0,17.34,5.46Zm4.6,2.42a7.59,7.59,0,0,0-.46-2.43,4.94,4.94,0,0,0-1.16-1.77,4.7,4.7,0,0,0-1.77-1.15,7.3,7.3,0,0,0-2.43-.47C15.06,2,14.72,2,12,2s-3.06,0-4.12.06a7.3,7.3,0,0,0-2.43.47A4.78,4.78,0,0,0,3.68,3.68,4.7,4.7,0,0,0,2.53,5.45a7.3,7.3,0,0,0-.47,2.43C2,8.94,2,9.28,2,12s0,3.06.06,4.12a7.3,7.3,0,0,0,.47,2.43,4.7,4.7,0,0,0,1.15,1.77,4.78,4.78,0,0,0,1.77,1.15,7.3,7.3,0,0,0,2.43.47C8.94,22,9.28,22,12,22s3.06,0,4.12-.06a7.3,7.3,0,0,0,2.43-.47,4.7,4.7,0,0,0,1.77-1.15,4.85,4.85,0,0,0,1.16-1.77,7.59,7.59,0,0,0,.46-2.43c0-1.06.06-1.4.06-4.12S22,8.94,21.94,7.88ZM20.14,16a5.61,5.61,0,0,1-.34,1.86,3.06,3.06,0,0,1-.75,1.15,3.19,3.19,0,0,1-1.15.75,5.61,5.61,0,0,1-1.86.34c-1,.05-1.37.06-4,.06s-3,0-4-.06A5.73,5.73,0,0,1,6.1,19.8,3.27,3.27,0,0,1,5,19.05a3,3,0,0,1-.74-1.15A5.54,5.54,0,0,1,3.86,16c0-1-.06-1.37-.06-4s0-3,.06-4A5.54,5.54,0,0,1,4.21,6.1,3,3,0,0,1,5,5,3.14,3.14,0,0,1,6.1,4.2,5.73,5.73,0,0,1,8,3.86c1,0,1.37-.06,4-.06s3,0,4,.06a5.61,5.61,0,0,1,1.86.34A3.06,3.06,0,0,1,19.05,5,3.06,3.06,0,0,1,19.8,6.1,5.61,5.61,0,0,1,20.14,8c.05,1,.06,1.37.06,4S20.19,15,20.14,16ZM12,6.87A5.13,5.13,0,1,0,17.14,12,5.12,5.12,0,0,0,12,6.87Zm0,8.46A3.33,3.33,0,1,1,15.33,12,3.33,3.33,0,0,1,12,15.33Z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                About Us
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Careers
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Blog
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Press
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Products</h4>
                    <ul>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Personal Finance
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Business Solutions
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Financial Education
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                API Documentation
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Support</h4>
                    <ul>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Help Center
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Contact Us
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                                Security
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="newsletter">
                    <h4>Subscribe to Planance Newsletter</h4>
                    <p>Get the latest updates, news, and financial tips directly to your inbox.</p>
                    <form class="newsletter-form">
                        <input type="email" class="newsletter-input" placeholder="Your email address">
                        <button type="submit" class="newsletter-button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </form>
                    <p class="newsletter-note">We respect your privacy. Unsubscribe at any time.</p>
                </div>
            </div>
            
            <div class="copyright">
                &copy; 2025 Planance. All rights reserved.
            </div>
        </div>
    </footer>
<script src="{{ asset('js/planance.js') }}"></script>
</body>
</html>
