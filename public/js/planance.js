document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    
    if (mobileNavToggle && mobileNav) {
        mobileNavToggle.addEventListener('click', () => {
            mobileNavToggle.classList.toggle('active');
            mobileNav.classList.toggle('active');
        });
    }
    
    // Feature Tabs
    const featureTabs = document.querySelectorAll('.feature-tab');
    const featureImage = document.getElementById('feature-image');
    
    if (featureTabs.length && featureImage) {
        featureTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                featureTabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Change feature image based on data-feature attribute
                const feature = tab.getAttribute('data-feature');
                if (feature) {
                    featureImage.src = `/images/features/${feature}.png`;
                    featureImage.alt = `${feature} Feature`;
                    
                    // Add fade-in animation
                    featureImage.classList.remove('fade-in');
                    void featureImage.offsetWidth; // Trigger reflow
                    featureImage.classList.add('fade-in');
                }
            });
        });
    }
    
    // Pricing Toggle
    const pricingToggle = document.querySelector('.pricing-toggle-pill');
    const pricingLabels = document.querySelectorAll('.pricing-toggle span');
    const priceValues = document.querySelectorAll('.price-value');
    
    if (pricingToggle && pricingLabels.length) {
        pricingToggle.addEventListener('click', () => {
            pricingToggle.classList.toggle('yearly');
            pricingLabels.forEach(label => label.classList.toggle('active'));
            
            // Update pricing values - multiply by 10 for yearly and apply 20% discount
            if (priceValues.length) {
                priceValues.forEach(price => {
                    const currentValue = parseInt(price.textContent);
                    if (pricingToggle.classList.contains('yearly')) {
                        // Switch to yearly pricing (monthly * 10 with 20% discount)
                        const yearlyPrice = Math.round(currentValue * 10 * 0.8);
                        price.textContent = yearlyPrice;
                    } else {
                        // Switch back to monthly pricing
                        const monthlyPrice = Math.round((currentValue / 0.8) / 10);
                        price.textContent = monthlyPrice;
                    }
                    
                    // Add animation
                    price.classList.remove('fade-in');
                    void price.offsetWidth; // Trigger reflow
                    price.classList.add('fade-in');
                });
                
                // Update pricing period text
                const pricePeriods = document.querySelectorAll('.price-period');
                pricePeriods.forEach(period => {
                    period.textContent = pricingToggle.classList.contains('yearly') ? '/year' : '/mo';
                });
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
                    // Close all other FAQ items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    // Toggle current item
                    item.classList.toggle('active');
                });
            }
        });
    }
    
    // Smooth Scroll for Anchor Links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    if (anchorLinks.length) {
        anchorLinks.forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    // Close mobile nav if open
                    if (mobileNav && mobileNav.classList.contains('active')) {
                        mobileNav.classList.remove('active');
                        if (mobileNavToggle) {
                            mobileNavToggle.classList.remove('active');
                        }
                    }
                    
                    // Scroll to target with offset for header
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
    
    // Hero image animation on scroll
    const heroImage = document.querySelector('.hero-image img');
    
    if (heroImage) {
        window.addEventListener('scroll', () => {
            const scrollPosition = window.scrollY;
            if (scrollPosition < 500) {
                const rotateY = -5 + (scrollPosition / 100);
                const rotateX = 5 - (scrollPosition / 100);
                
                heroImage.style.transform = `perspective(1000px) rotateY(${rotateY}deg) rotateX(${rotateX}deg)`;
            }
        });
    }
    
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('.newsletter-input');
            const email = emailInput.value.trim();
            
            // Simple email validation
            if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                // Show success message
                const successMessage = document.createElement('p');
                successMessage.textContent = 'Thank you for subscribing!';
                successMessage.style.color = '#10B981';
                successMessage.style.marginTop = '1rem';
                
                // Replace form with success message
                this.innerHTML = '';
                this.appendChild(successMessage);
            } else {
                // Show error message
                const errorMessage = document.createElement('p');
                errorMessage.textContent = 'Please enter a valid email address.';
                errorMessage.style.color = '#EF4444';
                errorMessage.style.fontSize = '0.875rem';
                errorMessage.style.marginTop = '0.5rem';
                
                // Remove any existing error messages
                const existingError = this.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                errorMessage.classList.add('error-message');
                this.appendChild(errorMessage);
                
                // Highlight input
                emailInput.style.borderColor = '#EF4444';
                emailInput.focus();
            }
        });
    }
    
    // Animate elements on scroll
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.feature-card, .testimonial-card, .pricing-card, .faq-item');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 100) {
                element.classList.add('fade-in');
            }
        });
    };
    
    // Initial call to animate elements already in viewport
    animateOnScroll();
    
    // Add scroll event listener
    window.addEventListener('scroll', animateOnScroll);
});