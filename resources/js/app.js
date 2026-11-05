import './bootstrap';

const themeToggles = document.querySelectorAll('[data-theme-toggle]');

function syncThemeToggles() {
    const isDark = document.documentElement.classList.contains('dark');

    themeToggles.forEach((toggle) => {
        toggle.setAttribute('aria-pressed', String(isDark));
        toggle.textContent = isDark ? 'Mode terang' : 'Mode gelap';
    });
}

themeToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('amikom-theme', isDark ? 'dark' : 'light');
        syncThemeToggles();
    });
});

syncThemeToggles();
