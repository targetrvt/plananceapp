document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuDark = document.querySelector('.mobile-menu-dark');
    const body = document.body;
    
    if (mobileMenuToggle && mobileMenuDark) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenuDark.classList.toggle('active');
            body.style.overflow = mobileMenuDark.classList.contains('active') ? 'hidden' : '';
            
            if (mobileMenuDark.classList.contains('active')) {
                const menuItems = mobileMenuDark.querySelectorAll('.mobile-menu-content > *');
                menuItems.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        item.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, index * 50);
                });
            }
        });
        
        const mobileLinks = mobileMenuDark.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenuToggle.classList.remove('active');
                mobileMenuDark.classList.remove('active');
                body.style.overflow = '';
            });
        });
        
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                mobileMenuToggle.classList.remove('active');
                mobileMenuDark.classList.remove('active');
                body.style.overflow = '';
            }
        });
    }
    
    const nav = document.querySelector('.nav');
    if (nav && !document.querySelector('.mobile-nav-toggle')) {
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
    const mobileNav = document.querySelector('body > .mobile-nav');
    
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
    
    const faqItems = document.querySelectorAll('.faq-item, .faq-item-dark');
    
    if (faqItems.length) {
        faqItems.forEach((item, index) => {
            const question = item.querySelector('.faq-question, .faq-question-dark');
            const answer = item.querySelector('.faq-answer, .faq-answer-dark');
            
            if (answer) {
                answer.style.maxHeight = '0';
                answer.style.overflow = 'hidden';
                answer.style.transition = 'max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease';
                answer.style.opacity = '0';
            }
            
            if (question) {
                question.style.transition = 'all 0.3s ease';
                
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            const otherAnswer = otherItem.querySelector('.faq-answer, .faq-answer-dark');
                            if (otherAnswer) {
                                otherAnswer.style.maxHeight = '0';
                                otherAnswer.style.opacity = '0';
                            }
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    item.classList.toggle('active');
                    
                    if (answer) {
                        if (!isActive) {
                            answer.style.maxHeight = answer.scrollHeight + 'px';
                            answer.style.opacity = '1';
                        } else {
                            answer.style.maxHeight = '0';
                            answer.style.opacity = '0';
                        }
                    }
                });
            }
        });
    }
    
    const pricingToggle = document.querySelector('.pricing-toggle-pill');
    const pricingLabels = document.querySelectorAll('.pricing-toggle span');
    
    if (pricingToggle && pricingLabels.length) {
        pricingToggle.addEventListener('click', () => {
            pricingToggle.classList.toggle('yearly');
            pricingLabels.forEach(label => label.classList.toggle('active'));
            
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
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    const header = document.querySelector('header, .dark-header');
                    const headerHeight = header ? header.offsetHeight : 80;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    const offsetPosition = targetPosition - headerHeight - 20;
                    
                    smoothScrollTo(offsetPosition, 800);
                }
            });
        });
    }
    
    function smoothScrollTo(target, duration) {
        const start = window.pageYOffset;
        const distance = target - start;
        let startTime = null;
        
        function easeInOutCubic(t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }
        
        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            const ease = easeInOutCubic(progress);
            
            window.scrollTo(0, start + distance * ease);
            
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }
        
        requestAnimationFrame(animation);
    }
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const featureCards = document.querySelectorAll('.feature-card-dark');
    featureCards.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px) scale(0.95)';
        el.style.transition = `opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.1}s, transform 0.6s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.1}s`;
        observer.observe(el);
    });
    
    const pricingCards = document.querySelectorAll('.pricing-card-dark');
    pricingCards.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(40px)';
        el.style.transition = `opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.15}s, transform 0.8s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.15}s`;
        observer.observe(el);
    });
    
    const testimonialCards = document.querySelectorAll('.testimonial-card-dark');
    testimonialCards.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(-30px)';
        el.style.transition = `opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.12}s, transform 0.7s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.12}s`;
        observer.observe(el);
    });
    
    const sectionHeaders = document.querySelectorAll('.section-header-dark');
    sectionHeaders.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });
    
    const heroContent = document.querySelector('.hero-content-dark');
    if (heroContent) {
        const heroElements = heroContent.querySelectorAll('*');
        heroElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = `opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1) ${0.2 + index * 0.1}s, transform 0.8s cubic-bezier(0.4, 0, 0.2, 1) ${0.2 + index * 0.1}s`;
        });
        
        setTimeout(() => {
            heroElements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }, 100);
    }
    
    const heroImage = document.querySelector('.hero-image-dark');
    if (heroImage) {
        heroImage.style.opacity = '0';
        heroImage.style.transform = 'translateY(20px)';
        heroImage.style.transition = 'opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.6s, transform 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.6s';
        
        setTimeout(() => {
            heroImage.style.opacity = '1';
            heroImage.style.transform = 'translateY(0)';
        }, 600);
        
        const imageGlow = heroImage.querySelector('.image-glow');
        if (imageGlow) {
            imageGlow.style.opacity = '0';
            imageGlow.style.transition = 'opacity 1s cubic-bezier(0.4, 0, 0.2, 1) 0.8s';
            setTimeout(() => {
                imageGlow.style.opacity = '1';
            }, 800);
        }
    }
    
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) translateX(0) scale(1) !important;
        }
    `;
    document.head.appendChild(style);
    
    const buttons = document.querySelectorAll('.btn-dark-primary, .btn-dark-outline, .btn-hero-primary, .btn-hero-secondary, .btn-pricing-dark, .btn-cta-primary');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            this.style.boxShadow = '0 10px 25px rgba(99, 102, 241, 0.3)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
        
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(rippleStyle);
    
    const statValues = document.querySelectorAll('.stat-value-dark');
    statValues.forEach(stat => {
        observer.observe(stat);
        
        const observerStat = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const text = target.textContent;
                    const number = parseInt(text.replace(/\D/g, ''));
                    
                    if (!isNaN(number) && number > 0) {
                        let current = 0;
                        const increment = number / 30;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= number) {
                                target.textContent = text;
                                clearInterval(timer);
                            } else {
                                target.textContent = Math.floor(current) + text.replace(/\d/g, '');
                            }
                        }, 30);
                    }
                    
                    observerStat.unobserve(target);
                }
            });
        }, { threshold: 0.5 });
        
        observerStat.observe(stat);
    });
    
    document.body.style.opacity = '0';
    window.addEventListener('load', function() {
        document.body.style.transition = 'opacity 0.5s ease-in';
        document.body.style.opacity = '1';
    });
});