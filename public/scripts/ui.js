// Simple UI scripts for the site
document.addEventListener('DOMContentLoaded', function () {
    // Testimonials carousel
    const quotes = document.querySelectorAll('.testimonials-inner blockquote');
    if (quotes.length > 1) {
        let idx = 0;
        quotes.forEach((q, i) => { q.style.opacity = i === 0 ? '1' : '0'; q.style.transition = 'opacity 600ms ease'; });
        setInterval(() => {
            quotes[idx].style.opacity = '0';
            idx = (idx + 1) % quotes.length;
            quotes[idx].style.opacity = '1';
        }, 4000);
    }

    // Mobile popup menu toggle
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileOverlay = document.querySelector('.mobile-menu-overlay');
    const mobileClose = document.querySelector('.mobile-menu-close');

    function openMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.add('open');
        mobileOverlay.classList.add('open');
        mobileMenu.setAttribute('aria-hidden', 'false');
        mobileToggle.setAttribute('aria-expanded', 'true');
        // focus first link
        const firstLink = mobileMenu.querySelector('a');
        if (firstLink) firstLink.focus();
    }

    function closeMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.remove('open');
        mobileOverlay.classList.remove('open');
        mobileMenu.setAttribute('aria-hidden', 'true');
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileToggle.focus();
    }

    if (mobileToggle && mobileMenu && mobileOverlay) {
        mobileToggle.addEventListener('click', function () {
            const open = mobileMenu.classList.contains('open');
            if (open) closeMobileMenu(); else openMobileMenu();
        });

        mobileClose && mobileClose.addEventListener('click', closeMobileMenu);
        mobileOverlay.addEventListener('click', closeMobileMenu);

        document.addEventListener('keydown', function (ev) {
            if (ev.key === 'Escape' && mobileMenu.classList.contains('open')) {
                closeMobileMenu();
            }
        });
    }
});