/**
 * CamsTube - Main JavaScript
 */

(function() {
    'use strict';

    /**
     * Thumbnail Preview Slideshow on Hover
     * Cycles through previewImages when hovering over a video card
     */
    function initThumbnailPreviews() {
        const cards = document.querySelectorAll('.video-card[data-previews]');

        cards.forEach(function(card) {
            let previews;
            try {
                previews = JSON.parse(card.dataset.previews);
            } catch(e) {
                return;
            }

            if (!previews || previews.length === 0) return;

            const img = card.querySelector('.thumb-img');
            if (!img) return;

            const originalSrc = img.src;
            let intervalId = null;
            let currentIndex = 0;

            card.addEventListener('mouseenter', function() {
                if (previews.length === 0) return;
                currentIndex = 0;
                intervalId = setInterval(function() {
                    currentIndex = (currentIndex + 1) % previews.length;
                    img.src = previews[currentIndex];
                }, 800);
            });

            card.addEventListener('mouseleave', function() {
                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                }
                img.src = originalSrc;
                currentIndex = 0;
            });
        });
    }

    /**
     * Lazy Loading for Images (fallback for browsers without native support)
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: '200px' });

            document.querySelectorAll('img[data-src]').forEach(function(img) {
                observer.observe(img);
            });
        }
    }

    /**
     * Smooth page transitions
     */
    function initSmoothLoading() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.3s ease';
        window.addEventListener('load', function() {
            document.body.style.opacity = '1';
        });
        // Fallback in case load already fired
        if (document.readyState === 'complete') {
            document.body.style.opacity = '1';
        }
    }

    /**
     * Mobile menu close on link click
     */
    function initMobileMenu() {
        var links = document.querySelectorAll('.nav-links .nav-link');
        var navLinks = document.querySelector('.nav-links');
        links.forEach(function(link) {
            link.addEventListener('click', function() {
                if (navLinks) navLinks.classList.remove('open');
            });
        });
    }

    /**
     * Search form validation
     */
    function initSearchForm() {
        var forms = document.querySelectorAll('.search-form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var input = form.querySelector('.search-input, input[name="q"]');
                if (input && input.value.trim() === '') {
                    e.preventDefault();
                    input.focus();
                    input.style.borderColor = '#e91e63';
                    setTimeout(function() {
                        input.style.borderColor = '';
                    }, 2000);
                }
            });
        });
    }

    // Initialize everything when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        initSmoothLoading();
        initThumbnailPreviews();
        initLazyLoading();
        initMobileMenu();
        initSearchForm();
    }

})();
