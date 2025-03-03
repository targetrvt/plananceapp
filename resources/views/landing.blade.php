<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planance - Smart Financial Planning Simplified</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5046e5;
            --primary-dark: #4238c2;
            --secondary: #36b4ff;
            --dark: #222639;
            --light: #f5f7fa;
            --success: #38c172;
            --danger: #e3342f;
            --warning: #ffad33;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            line-height: 1.6;
            background-color: var(--light);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .logo img {
            height: 40px;
            margin-right: 0.5rem;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .auth-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        /* Hero Section */
        .hero {
            padding: 4rem 0;
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .hero-content {
            flex: 1;
        }
        
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        
        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #555;
        }
        
        .highlight {
            color: var(--primary);
        }
        
        /* Features */
        .features {
            padding: 5rem 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: #555;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            padding: 2rem;
            background-color: var(--light);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background-color: rgba(80, 70, 229, 0.1);
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .feature-icon svg {
            width: 30px;
            height: 30px;
            color: var(--primary);
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .feature-card p {
            color: #555;
        }
        
        /* Testimonials */
        .testimonials {
            padding: 5rem 0;
            background-color: #f8fafc;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .testimonial-card {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .testimonial-content {
            font-style: italic;
            margin-bottom: 1.5rem;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 1rem;
        }
        
        .author-info h4 {
            font-weight: 600;
        }
        
        .author-info p {
            font-size: 0.9rem;
            color: #555;
        }
        
        /* Pricing */
        .pricing {
            padding: 5rem 0;
            background-color: white;
        }
        
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .pricing-card {
            background-color: var(--light);
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .pricing-card.featured {
            background-color: var(--primary);
            color: white;
            transform: scale(1.05);
        }
        
        .pricing-card.featured .price {
            color: white;
        }
        
        .pricing-card.featured .btn {
            background-color: white;
            color: var(--primary);
            border-color: white;
        }
        
        .pricing-card.featured .btn:hover {
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .pricing-card.featured:hover {
            transform: scale(1.05) translateY(-5px);
        }
        
        .pricing-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .price span {
            font-size: 1rem;
            font-weight: 400;
        }
        
        .pricing-features {
            list-style: none;
            margin-bottom: 2rem;
        }
        
        .pricing-features li {
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .pricing-card.featured .pricing-features li {
            border-bottom-color: rgba(255, 255, 255, 0.2);
        }
        
        /* CTA */
        .cta {
            padding: 5rem 0;
            background-color: var(--primary);
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 2rem;
            font-size: 1.1rem;
        }
        
        .cta .btn {
            background-color: white;
            color: var(--primary);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        .cta .btn:hover {
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 4rem 0 2rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .footer-logo {
            margin-bottom: 1rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }
        
        .footer-about p {
            margin-bottom: 1.5rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
        }
        
        .social-links svg {
            width: 20px;
            height: 20px;
            fill: white;
        }
        
        .footer-links h4 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }
            
            .nav {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .pricing-card.featured {
                transform: none;
            }
            
            .pricing-card.featured:hover {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="nav">
                <div class="logo">
                    <img src="/api/placeholder/40/40" alt="Planance Logo"> Planance
                </div>
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
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero">
                <div class="hero-content">
                    <h1>Smart <span class="highlight">Financial Planning</span> Made Simple</h1>
                    <p>Planance helps individuals and businesses take control of their finances with intuitive budgeting, expense tracking, and financial goal planning.</p>
                    <a href="/app/register" class="btn btn-primary">Start Free Trial</a>
                </div>
                <div class="hero-image">
                    <img src="/api/placeholder/500/400" alt="Planance Dashboard">
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p>Planance comes packed with all the tools you need to manage your finances effectively.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3>Budget Management</h3>
                    <p>Create and manage detailed budgets with customizable categories and automatic tracking to stay on top of your spending.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3>Expense Tracking</h3>
                    <p>Effortlessly track your expenses with receipt scanning technology and automatic categorization of transactions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <h3>Financial Goals</h3>
                    <p>Set and track your financial goals with visual progress indicators and smart recommendations to help you achieve them faster.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3>Insightful Reports</h3>
                    <p>Access detailed financial reports and analytics to gain insights into your spending patterns and make informed decisions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3>Secure & Private</h3>
                    <p>Your financial data is protected with bank-level security and encryption. We prioritize your privacy and data protection.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3>Team Collaboration</h3>
                    <p>Perfect for businesses and families - collaborate on shared financial planning with customizable access levels.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>What Our Users Say</h2>
                <p>Join thousands of satisfied individuals and businesses who have transformed their financial management with Planance.</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Planance has completely transformed how I manage my personal finances. The intuitive interface and budgeting tools helped me save €5,000 in just 6 months!"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="/api/placeholder/50/50" alt="Sarah K.">
                        <div class="author-info">
                            <h4>Sarah K.</h4>
                            <p>Marketing Professional</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"As a small business owner, keeping track of expenses was always a challenge. Planance made it simple with its receipt scanning feature and detailed reports."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="/api/placeholder/50/50" alt="Michael D.">
                        <div class="author-info">
                            <h4>Michael D.</h4>
                            <p>Small Business Owner</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The financial goal tracking feature helped me stay motivated while saving for my first home. Planance is now an essential part of my financial routine."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="/api/placeholder/50/50" alt="Emma T.">
                        <div class="author-info">
                            <h4>Emma T.</h4>
                            <p>Software Engineer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-title">
                <h2>Simple, Transparent Pricing</h2>
                <p>Choose the plan that fits your needs, whether you're an individual or a business.</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Personal</h3>
                    <div class="price">€5<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>Unlimited Budget Categories</li>
                        <li>Expense Tracking</li>
                        <li>Financial Goal Setting</li>
                        <li>Basic Reports</li>
                        <li>Mobile App Access</li>
                        <li>Email Support</li>
                    </ul>
                    <a href="/app/register" class="btn btn-outline">Get Started</a>
                </div>
                <div class="pricing-card featured">
                    <h3>Premium</h3>
                    <div class="price">€12<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>Everything in Personal</li>
                        <li>Receipt Scanning</li>
                        <li>Advanced Analytics</li>
                        <li>Custom Categories</li>
                        <li>Export Data (CSV, PDF)</li>
                        <li>Priority Support</li>
                    </ul>
                    <a href="/app/register" class="btn btn-primary">Get Started</a>
                </div>
                <div class="pricing-card">
                    <h3>Business</h3>
                    <div class="price">€25<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>Everything in Premium</li>
                        <li>Multiple Users (up to 5)</li>
                        <li>Team Collaboration</li>
                        <li>Role-Based Permissions</li>
                        <li>API Access</li>
                        <li>Dedicated Support</li>
                    </ul>
                    <a href="/app/register" class="btn btn-outline">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Ready to Take Control of Your Finances?</h2>
            <p>Join thousands of users who are already managing their finances smarter with Planance. Start your free 14-day trial today - no credit card required.</p>
            <a href="/app/register" class="btn">Start Free Trial</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">Planance</div>
                    <p>Smart financial planning for individuals and businesses. Take control of your finances with our intuitive tools and powerful insights.</p>
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
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Products</h4>
                    <ul>
                        <li><a href="#">Personal Finance</a></li>
                        <li><a href="#">Business Solutions</a></li>
                        <li><a href="#">Financial Education</a></li>
                        <li><a href="#">API Documentation</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Security</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                        <li><a href="#">GDPR Compliance</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; 2025 Planance. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- FAQ Section -->
    <section class="faq" id="faq" style="padding: 5rem 0; background-color: #f8fafc;">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about Planance and how it can help you manage your finances.</p>
            </div>
            <div style="max-width: 800px; margin: 0 auto;">
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">How secure is my financial data with Planance?</h3>
                    <p style="color: #555;">At Planance, we take security seriously. We use bank-level encryption and security practices to ensure your data is always protected. Our system is regularly audited by independent security experts, and we never share your personal information with third parties without your consent.</p>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">Can I access Planance on multiple devices?</h3>
                    <p style="color: #555;">Yes! Planance is available on web browsers, iOS, and Android devices. Your data is automatically synced across all your devices, so you can manage your finances whenever and wherever it's convenient for you.</p>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">Does Planance integrate with my bank accounts?</h3>
                    <p style="color: #555;">Yes, Planance can connect securely to over 10,000 financial institutions worldwide. This allows for automatic transaction import and categorization, saving you time and ensuring your financial data is always up-to-date.</p>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">Can I share access with my spouse or business partner?</h3>
                    <p style="color: #555;">Absolutely! Our Premium and Business plans allow for shared access with customizable permissions. You can control exactly what each user can view or modify, making it perfect for families, businesses, and financial advisors.</p>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">Is there a free trial available?</h3>
                    <p style="color: #555;">Yes, we offer a 14-day free trial on all our plans. No credit card is required to start your trial, and you can upgrade or cancel at any time.</p>
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--primary);">How do I get support if I have questions?</h3>
                    <p style="color: #555;">Our support team is available via email, live chat, and phone. Premium and Business users get priority support with faster response times. We also have an extensive knowledge base and video tutorials to help you get the most out of Planance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile App Section -->
    <section style="padding: 5rem 0; background-color: white;">
        <div class="container">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 3rem;">
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Take Planance With You Everywhere</h2>
                    <p style="font-size: 1.1rem; color: #555; margin-bottom: 2rem;">Our mobile app allows you to track expenses on the go, scan receipts instantly, and stay on top of your finances anywhere, anytime.</p>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" style="display: inline-block;">
                            <img src="/api/placeholder/150/50" alt="Download on App Store" style="height: 50px; width: auto;">
                        </a>
                        <a href="#" style="display: inline-block;">
                            <img src="/api/placeholder/150/50" alt="Get it on Google Play" style="height: 50px; width: auto;">
                        </a>
                    </div>
                </div>
                <div style="flex: 1; min-width: 300px; display: flex; justify-content: center;">
                    <img src="/api/placeholder/300/600" alt="Planance Mobile App" style="max-width: 100%; height: auto; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);">
                </div>
            </div>
        </div>
    </section>

</body>
</html>