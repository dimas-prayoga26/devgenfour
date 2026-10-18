const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('.main-nav');
const header = document.querySelector('.site-header');

function setMenuOpen(open) {
    menuButton.setAttribute('aria-expanded', String(open));
    menuButton.setAttribute('aria-label', open ? menuButton.dataset.closeLabel : menuButton.dataset.openLabel);
    navigation.classList.toggle('is-open', open);
}

menuButton.addEventListener('click', () => {
    setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
});

navigation.addEventListener('click', (event) => {
    if (event.target.closest('a')) setMenuOpen(false);
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
        setMenuOpen(false);
        menuButton.focus();
    }
});

document.addEventListener('click', (event) => {
    if (!header.contains(event.target)) setMenuOpen(false);
});

window.matchMedia('(min-width: 1024px)').addEventListener('change', () => setMenuOpen(false));

document.querySelectorAll('.language-switch a').forEach((link) => {
    link.addEventListener('click', () => {
        const destination = new URL(link.href);
        destination.hash = window.location.hash;
        link.href = destination.href;
    });
});

const sectionObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        document.querySelectorAll('.main-nav a').forEach((link) => {
            if (link.hash === `#${entry.target.id}`) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    });
}, { rootMargin: '-15% 0px -55% 0px' });

document.querySelectorAll('main section[id]').forEach((section) => sectionObserver.observe(section));

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const carousel = document.querySelector('.portfolio-carousel');

if (carousel) {
    const track = carousel.querySelector('.portfolio-track');
    const slides = [...track.querySelectorAll('.portfolio-slide')];
    const dots = [...carousel.querySelectorAll('[data-slide]')];
    const position = carousel.querySelector('.portfolio-position');
    let current = 0;
    let dragging = null;
    let scrollFrame = null;

    function updateSlide() {
        current = Math.max(0, Math.min(slides.length - 1, Math.round(track.scrollLeft / track.clientWidth)));
        dots.forEach((dot, index) => {
            if (index === current) dot.setAttribute('aria-current', 'true');
            else dot.removeAttribute('aria-current');
        });
        slides.forEach((slide, index) => slide.setAttribute('aria-hidden', String(index !== current)));
        position.textContent = `${String(current + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
    }

    function goToSlide(index, immediate = false) {
        current = (index + slides.length) % slides.length;
        track.scrollTo({ left: current * track.clientWidth, behavior: immediate || reducedMotion.matches ? 'instant' : 'smooth' });
    }

    carousel.querySelector('.portfolio-previous').addEventListener('click', () => goToSlide(current - 1));
    carousel.querySelector('.portfolio-next').addEventListener('click', () => goToSlide(current + 1));
    dots.forEach((dot) => dot.addEventListener('click', () => goToSlide(Number(dot.dataset.slide))));
    track.addEventListener('keydown', (event) => {
        if (event.target !== track || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
        event.preventDefault();
        if (event.key === 'Home') goToSlide(0);
        else if (event.key === 'End') goToSlide(slides.length - 1);
        else goToSlide(current + (event.key === 'ArrowRight' ? 1 : -1));
    });
    track.addEventListener('scroll', () => {
        if (scrollFrame !== null) return;
        scrollFrame = requestAnimationFrame(() => {
            updateSlide();
            scrollFrame = null;
        });
    }, { passive: true });
    track.addEventListener('pointerdown', (event) => {
        if (event.pointerType !== 'mouse' || event.button !== 0) return;
        dragging = { x: event.clientX, left: track.scrollLeft };
        track.classList.add('is-dragging');
        track.setPointerCapture(event.pointerId);
    });
    track.addEventListener('pointermove', (event) => {
        if (dragging) track.scrollLeft = dragging.left - (event.clientX - dragging.x);
    });
    function finishDrag() {
        if (!dragging) return;
        const index = Math.round(track.scrollLeft / track.clientWidth);
        dragging = null;
        track.classList.remove('is-dragging');
        goToSlide(index);
    }
    track.addEventListener('pointerup', finishDrag);
    track.addEventListener('pointercancel', finishDrag);
    track.addEventListener('lostpointercapture', finishDrag);
    new ResizeObserver(() => goToSlide(current, true)).observe(track);
    updateSlide();
}
