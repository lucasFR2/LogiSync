// Theme toggle helper for Tailwind 'class' dark mode
(function () {
	function applyTheme(theme) {
		const root = document.documentElement;
		if (theme === 'dark') {
			root.classList.add('dark');
		} else {
			root.classList.remove('dark');
		}
	}

	function getStoredTheme() {
		try {
			return localStorage.getItem('theme');
		} catch (e) {
			return null;
		}
	}

	function setStoredTheme(value) {
		try {
			localStorage.setItem('theme', value);
		} catch (e) {
			// ignore
		}
	}

	function init() {
		const stored = getStoredTheme();
		const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
		const theme = stored || (prefersDark ? 'dark' : 'light');
		applyTheme(theme);
	}

	function toggleTheme() {
		const root = document.documentElement;
		const isDark = root.classList.contains('dark');
		const next = isDark ? 'light' : 'dark';
		applyTheme(next);
		setStoredTheme(next);
	}

	window.toggleTheme = toggleTheme;
	document.addEventListener('DOMContentLoaded', init);
})();
