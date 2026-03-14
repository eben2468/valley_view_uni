/**
 * Header Auto-hide on Scroll
 * Hides header when scrolling down, shows when scrolling up
 */
(function () {
    'use strict';

    let lastScrollTop = 0;
    let scrollThreshold = 10; // Minimum scroll distance to trigger
    let headerSection = null;
    let topBar = null;
    let logoBar = null;
    let ticking = false;

    // Initialize on DOM ready
    function init() {
        // Get header elements
        topBar = document.querySelector('.ed-top');
        logoBar = document.querySelector('.top-logo');

        // Create a wrapper section if elements exist
        if (topBar || logoBar) {
            // Find the parent section
            headerSection = topBar ? topBar.closest('section') : logoBar.closest('section');

            if (headerSection) {
                // Add transition and positioning classes
                headerSection.style.position = 'fixed';
                headerSection.style.top = '0';
                headerSection.style.left = '0';
                headerSection.style.right = '0';
                headerSection.style.zIndex = '9999';
                headerSection.style.transition = 'transform 0.3s ease-in-out';
                headerSection.style.transform = 'translateY(0)';
                headerSection.style.backgroundColor = '#fff';
                headerSection.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';

                // Add padding to body to prevent content jump
                updateBodyPadding();

                // Listen to scroll events
                window.addEventListener('scroll', onScroll, { passive: true });
                window.addEventListener('resize', updateBodyPadding);
            }
        }
    }

    // Update body padding based on header height
    function updateBodyPadding() {
        if (headerSection) {
            const headerHeight = headerSection.offsetHeight;
            document.body.style.paddingTop = headerHeight + 'px';
        }
    }

    // Handle scroll events with throttling
    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }

    // Main scroll handler
    function handleScroll() {
        ticking = false;

        const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Show header only when at the very top of the page
        if (currentScrollTop <= 0) {
            showHeader();
        } else {
            // Hide header for any scroll position below top
            hideHeader();
        }

        lastScrollTop = currentScrollTop;
    }

    // Hide header
    function hideHeader() {
        if (headerSection) {
            const headerHeight = headerSection.offsetHeight;
            headerSection.style.transform = `translateY(-${headerHeight}px)`;
            headerSection.classList.add('header-hidden');
        }
    }

    // Show header
    function showHeader() {
        if (headerSection) {
            headerSection.style.transform = 'translateY(0)';
            headerSection.classList.remove('header-hidden');
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
