document.addEventListener('DOMContentLoaded', function() {
    const nav = document.querySelector('.nav');
    if (!document.querySelector('.mobile-nav-toggle')) {
        const mobileToggle = document.createElement('button');
        mobileToggle.className = 'mobile-nav-toggle';
        mobileToggle.setAttribute('aria-label', 'Toggle Menu');

        for (let i = 0; i < 3; i++) {
            const span = document.createElement('span');
            mobileToggle.appendChild(span);
        }
        nav.appendChild(mobileToggle);
    }
    
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mobileNav = document.querySelector('body > .mobile-nav'); // The standalone mobile nav
    const body = document.body;
    
    if (mobileNavToggle && mobileNav) {
        mobileNavToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileNav.classList.toggle('active');
            body.classList.toggle('nav-open');
        });
        
        const mobileLinks = mobileNav.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileNavToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                body.classList.remove('nav-open');
            });
        });
        
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                mobileNavToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                body.classList.remove('nav-open');
            }
        });
    }
    
    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (faqItems.length) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            if (question) {
                question.addEventListener('click', () => {
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            otherItem.classList.remove('active');
                        }
                    });
                    item.classList.toggle('active');
                });
            }
        });
    }
    
    // Pricing Toggle
    const pricingToggle = document.querySelector('.pricing-toggle-pill');
    const pricingLabels = document.querySelectorAll('.pricing-toggle span');
    
    if (pricingToggle && pricingLabels.length) {
        pricingToggle.addEventListener('click', () => {
            pricingToggle.classList.toggle('yearly');
            pricingLabels.forEach(label => label.classList.toggle('active'));
            
            // Update pricing period text
            const pricePeriods = document.querySelectorAll('.price-period');
            pricePeriods.forEach(period => {
                period.textContent = pricingToggle.classList.contains('yearly') ? '/year' : '/mo';
            });
        });
    }
    
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    if (anchorLinks.length) {
        anchorLinks.forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const headerHeight = document.querySelector('header').offsetHeight;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    const offsetPosition = targetPosition - headerHeight - 20;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
});