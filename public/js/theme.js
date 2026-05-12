/**
 * LogiSync Theme Manager
 * Manages Light/Dark mode with localStorage persistence and no FOUC.
 * Apply data-theme on <html> BEFORE DOM renders — include this in <head>.
 */
(function () {
  const STORAGE_KEY = 'logisync_theme';
  const html = document.documentElement;

  function getTheme() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) return stored;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function setTheme(theme) {
    html.setAttribute('data-theme', theme);
    if (theme === 'dark') {
      html.classList.add('dark');
    } else {
      html.classList.remove('dark');
    }
    localStorage.setItem(STORAGE_KEY, theme);
    updateToggleIcons(theme);
  }

  function updateToggleIcons(theme) {
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      const icon = btn.querySelector('i');
      if (!icon) return;
      if (theme === 'dark') {
        icon.className = 'fa-solid fa-sun';
        btn.title = 'Mudar para tema claro';
      } else {
        icon.className = 'fa-solid fa-moon';
        btn.title = 'Mudar para tema escuro';
      }
    });
  }

  function toggleTheme() {
    const current = html.getAttribute('data-theme') || 'dark';
    setTheme(current === 'dark' ? 'light' : 'dark');
  }

  // Apply immediately to avoid flash
  setTheme(getTheme());

  // Expose global toggle function
  window.toggleTheme = toggleTheme;

  // Re-apply icons when DOM is ready
  document.addEventListener('DOMContentLoaded', function () {
    updateToggleIcons(html.getAttribute('data-theme'));
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      btn.addEventListener('click', toggleTheme);
    });
  });
})();
