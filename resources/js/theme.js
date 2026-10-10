const storageKey = 'vesperia-theme';
const themeToggle = document.querySelector('#theme-toggle');
const themeLabel = document.querySelector('#theme-label');

function applyTheme(theme) {
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

    if (themeToggle) {
        themeToggle.checked = isDark;
        themeToggle.setAttribute('aria-checked', String(isDark));
    }

    if (themeLabel) {
        themeLabel.textContent = isDark ? 'Mode gelap aktif' : 'Mode terang aktif';
    }
}

let savedTheme = 'light';

try {
    const preference = window.localStorage.getItem(storageKey);
    if (preference === 'dark' || preference === 'light') {
        savedTheme = preference;
    }
} catch (error) {
    console.warn('Preferensi tema tidak bisa dibaca dari penyimpanan browser.', error);
}

applyTheme(savedTheme);

themeToggle?.addEventListener('change', () => {
    const theme = themeToggle.checked ? 'dark' : 'light';

    try {
        window.localStorage.setItem(storageKey, theme);
    } catch (error) {
        console.warn('Preferensi tema tidak bisa disimpan di browser.', error);
    }

    applyTheme(theme);
});
