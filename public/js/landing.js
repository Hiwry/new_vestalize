/**
 * Vestalize Landing Page Scripts
 * Handles scroll animations and interactivity
 */

document.addEventListener('DOMContentLoaded', function () {
    // ============================================
    // Pricing Toggle (Checkbox-based)
    // ============================================
    const pricingToggle = document.getElementById('pricing-toggle-input');
    if (pricingToggle) {
        pricingToggle.addEventListener('change', function () {
            const isYearly = this.checked;

            // Atualizar labels
            const labelMensal = document.getElementById('label-mensal');
            const labelAnual = document.getElementById('label-anual');

            if (labelMensal && labelAnual) {
                labelMensal.classList.toggle('text-white', !isYearly);
                labelMensal.classList.toggle('text-muted', isYearly);
                labelAnual.classList.toggle('text-white', isYearly);
                labelAnual.classList.toggle('text-muted', !isYearly);
            }

            // Atualizar preços
            document.querySelectorAll('.price-monthly').forEach(el => {
                el.classList.toggle('hidden', isYearly);
            });
            document.querySelectorAll('.price-yearly').forEach(el => {
                el.classList.toggle('hidden', !isYearly);
            });
        });
    }

    // ============================================
    // Intersection Observer for Scroll Animations
    // ============================================
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                // Optionally unobserve after animation
                // observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with scroll-animate class
    document.querySelectorAll('.scroll-animate').forEach(el => {
        observer.observe(el);
    });

    // ============================================
    // Smooth Scroll for Anchor Links
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ============================================
    // Navbar Scroll Effect
    // ============================================
    const navbar = document.querySelector('.landing-navbar');
    if (navbar) {
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });
    }

    // ============================================
    // Animated Counter for Stats
    // ============================================
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);

        function updateCounter() {
            start += increment;
            if (start < target) {
                element.textContent = Math.floor(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        }

        updateCounter();
    }

    // Observe counter elements
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.dataset.target);
                if (target) {
                    animateCounter(entry.target, target);
                }
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-counter]').forEach(el => {
        counterObserver.observe(el);
    });

    // ============================================
    // Form Handling
    // ============================================
    const leadForm = document.getElementById('lead-form');
    if (leadForm) {
        leadForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enviando...
            `;

            // Simulate API call - replace with actual endpoint
            setTimeout(() => {
                // Show success message
                const formContainer = leadForm.closest('.form-container') || leadForm.parentElement;
                formContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Cadastro realizado!</h3>
                        <p class="text-muted">Em breve entraremos em contato.</p>
                    </div>
                `;
            }, 1500);
        });
    }

    // ============================================
    // Floating Badges Parallax Effect
    // ============================================
    const floatingBadges = document.querySelectorAll('.floating-badge');
    if (floatingBadges.length > 0 && window.innerWidth > 1024) {
        window.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;

            floatingBadges.forEach((badge, index) => {
                const factor = (index + 1) * 0.3;
                badge.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
            });
        });
    }

    // ============================================
    // word.style.animationDelay = `${index * 0.05}s`;
    // });

    // ============================================
    // Theme Switcher (Dark/Light)
    // ============================================
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    const body = document.body;
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');

    // DO NOT override theme if we are in the admin dashboard (avento-theme)
    // The admin area has its own dark-mode.js and state management
    if (html.classList.contains('avento-theme')) {
        console.log('Landing.js: Admin theme detected, skipping theme initialization');
        return;
    }

    function updateIcons(isLight) {
        if (!sunIcon || !moonIcon) return;
        if (isLight) {
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        } else {
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        }
    }

    // Check for saved theme preference (Unified with admin key 'dark')
    const savedTheme = localStorage.getItem('dark');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Legacy support for 'theme' key if 'dark' is missing
    let initialDark = prefersDark;
    if (savedTheme !== null) {
        initialDark = savedTheme === 'true' || savedTheme === 'dark';
    } else {
        const legacyTheme = localStorage.getItem('theme');
        if (legacyTheme !== null) {
            initialDark = legacyTheme === 'dark';
        }
    }

    if (initialDark) {
        html.classList.add('dark');
        body.classList.add('dark');
        updateIcons(false);
    } else {
        html.classList.remove('dark');
        body.classList.remove('dark');
        updateIcons(true);
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const isNowDark = html.classList.toggle('dark');
            body.classList.toggle('dark', isNowDark);
            localStorage.setItem('dark', isNowDark ? 'true' : 'false');
            updateIcons(!isNowDark);
        });
    }
});
