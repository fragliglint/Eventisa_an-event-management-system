document.addEventListener('DOMContentLoaded', function() {
    // Banner Slider Functionality
    class BannerSlider {
        constructor() {
            this.sliderWrapper = document.querySelector('.eventisa-banner-slider .slider-wrapper');
            this.slides = Array.from(document.querySelectorAll('.eventisa-banner-slider .slide'));
            this.dots = Array.from(document.querySelectorAll('.slider-dots .dot'));
            this.prevBtn = document.querySelector('.eventisa-banner-slider .slider-prev');
            this.nextBtn = document.querySelector('.eventisa-banner-slider .slider-next');
            this.currentSlide = 0;
            this.slideCount = this.slides.length;
            this.autoplayInterval = null;
            this.autoplayDelay = 5000; // 5 seconds
            this.isAnimating = false;

            this.init();
        }

        init() {
            // Add event listeners
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.prevSlide());
            }
            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.nextSlide());
            }

            // Dot navigation
            this.dots.forEach((dot, i) => {
                dot.addEventListener('click', () => this.goToSlide(i));
            });

            // Keyboard support
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.prevSlide();
                if (e.key === 'ArrowRight') this.nextSlide();
            });

            // Auto-play with pause on hover
            const sliderEl = document.querySelector('.eventisa-banner-slider');
            if (sliderEl) {
                sliderEl.addEventListener('mouseenter', () => this.stopAutoplay());
                sliderEl.addEventListener('mouseleave', () => this.startAutoplay());
            }

            // Touch swipe support
            this.addSwipeSupport();

            this.startAutoplay();
            this.updateSlider();
        }

        startAutoplay() {
            this.stopAutoplay();
            if (this.slideCount > 1) {
                this.autoplayInterval = setInterval(() => {
                    this.nextSlide();
                }, this.autoplayDelay);
            }
        }

        stopAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            }
        }

        nextSlide() {
            if (this.isAnimating) return;
            this.isAnimating = true;
            const nextIndex = (this.currentSlide + 1) % this.slideCount;
            this.goToSlide(nextIndex);
        }

        prevSlide() {
            if (this.isAnimating) return;
            this.isAnimating = true;
            const prevIndex = (this.currentSlide - 1 + this.slideCount) % this.slideCount;
            this.goToSlide(prevIndex);
        }

        goToSlide(index) {
            if (this.isAnimating || index === this.currentSlide) return;
            
            this.isAnimating = true;

            // Update current slide
            const currentSlide = this.slides[this.currentSlide];
            const nextSlide = this.slides[index];

            // Remove active classes
            currentSlide.classList.remove('active');
            this.dots[this.currentSlide].classList.remove('active');

            // Add active classes
            nextSlide.classList.add('active');
            this.dots[index].classList.add('active');

            this.currentSlide = index;
            
            // Reset animation flag
            setTimeout(() => {
                this.isAnimating = false;
            }, 1000); // Match CSS transition duration

            // Restart autoplay
            this.startAutoplay();
        }

        updateSlider() {
            this.slides.forEach((slide, index) => {
                slide.classList.toggle('active', index === this.currentSlide);
            });

            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentSlide);
            });
        }

        addSwipeSupport() {
            const slider = this.sliderWrapper;
            let startX = 0;
            let currentX = 0;
            let isSwiping = false;

            slider.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isSwiping = true;
                this.stopAutoplay();
            });

            slider.addEventListener('touchmove', (e) => {
                if (!isSwiping) return;
                currentX = e.touches[0].clientX;
            });

            slider.addEventListener('touchend', () => {
                if (!isSwiping) return;
                
                const diff = startX - currentX;
                const swipeThreshold = 50;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        this.nextSlide();
                    } else {
                        this.prevSlide();
                    }
                }
                
                isSwiping = false;
                this.startAutoplay();
            });
        }
    }

    // Initialize banner slider
    if (document.querySelector('.eventisa-banner-slider')) {
        new BannerSlider();
    }

    // Activity Horizontal Scroll Logic
    const activityList = document.querySelector('.activity-list');
    const activityPrevBtn = document.querySelector('.activity-prev-btn');
    const activityNextBtn = document.querySelector('.activity-next-btn');
    const scrollAmount = 220;

    if (activityList) {
        const updateActivityArrows = () => {
            if (activityList.scrollWidth > activityList.clientWidth + 10) {
                if (activityPrevBtn) activityPrevBtn.style.display = 'flex';
                if (activityNextBtn) activityNextBtn.style.display = 'flex';
            } else {
                if (activityPrevBtn) activityPrevBtn.style.display = 'none';
                if (activityNextBtn) activityNextBtn.style.display = 'none';
            }
        };

        if (activityPrevBtn) {
            activityPrevBtn.addEventListener('click', () => {
                activityList.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }

        if (activityNextBtn) {
            activityNextBtn.addEventListener('click', () => {
                activityList.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        window.addEventListener('resize', updateActivityArrows);
        window.addEventListener('load', updateActivityArrows);
        updateActivityArrows();
    }

    // Scroll Reveal Animation
    const scrollElements = document.querySelectorAll('.scroll-reveal');

    const elementInView = (el, fraction = 1.25) => {
        const top = el.getBoundingClientRect().top;
        return top <= (window.innerHeight || document.documentElement.clientHeight) / fraction;
    };

    const displayScrollElement = (element) => {
        element.classList.add('visible');
    };

    const handleScrollAnimation = () => {
        scrollElements.forEach((el) => {
            if (elementInView(el, 1.25)) {
                displayScrollElement(el);
            }
        });
    };

    // Throttle scroll events for performance
    let ticking = false;
    const throttledScrollHandler = () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                handleScrollAnimation();
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', throttledScrollHandler);
    window.addEventListener('resize', throttledScrollHandler);
    
    // Initial check
    handleScrollAnimation();

    // Cart functionality
    const cartBtn = document.querySelector('.btn-cart');
    const cartCount = document.querySelector('.cart-count');
    
    if (cartBtn && cartCount) {
        let cartItems = 0;
        
        // Simulate adding items to cart (for demo)
        cartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            cartItems++;
            cartCount.textContent = cartItems;
            
            // Add animation
            cartCount.style.transform = 'scale(1.3)';
            setTimeout(() => {
                cartCount.style.transform = 'scale(1)';
            }, 300);
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar scroll effect
    const navbar = document.querySelector('.eventisa-navbar');
    let lastScrollY = window.scrollY;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.backdropFilter = 'blur(10px)';
        } else {
            navbar.style.background = '#fff';
            navbar.style.backdropFilter = 'none';
        }

        lastScrollY = window.scrollY;
    });

    // Image lazy loading for better performance
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));

    // Add loading states for better UX
    const links = document.querySelectorAll('a[href]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            if (link.getAttribute('href').startsWith('http') || 
                link.getAttribute('href').includes('.php')) {
                // Add loading indicator for page transitions
                document.body.style.cursor = 'wait';
                setTimeout(() => {
                    document.body.style.cursor = 'default';
                }, 1000);
            }
        });
    });

    // Enhanced hover effects for cards
    const cards = document.querySelectorAll('.event-card, .offering-card, .activity-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // Mobile menu toggle (for future implementation)
    const createMobileMenu = () => {
        const navMenu = document.querySelector('.nav-menu');
        const navActions = document.querySelector('.nav-actions');
        
        if (window.innerWidth <= 992) {
            // You can add mobile menu toggle functionality here
            // This is a placeholder for future mobile menu implementation
        }
    };

    window.addEventListener('resize', createMobileMenu);
    createMobileMenu();

    // Add subtle animations to interactive elements
    const interactiveElements = document.querySelectorAll('button, a, .btn-sign-in, .btn-explore');
    interactiveElements.forEach(el => {
        el.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        el.addEventListener('mouseup', function() {
            this.style.transform = '';
        });
        
        el.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });

    console.log('Eventisa - All animations and interactions loaded successfully!');
});

// Additional utility functions
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

// Export for potential module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { debounce };
}