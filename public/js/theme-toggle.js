/**
 * LogiSync - Theme Toggle
 * Gerencia a alternância entre tema claro e escuro.
 * Persiste a preferência no localStorage e respeita
 * a preferência do sistema operacional como padrão.
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'logisync_theme';
    const DARK_CLASS  = 'dark';

    /**
     * Retorna o tema salvo ou detecta a preferência do SO.
     * @returns {'dark'|'light'}
     */
    function getStoredTheme() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'dark' || saved === 'light') return saved;
        // Fallback: preferência do sistema operacional
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /**
     * Aplica o tema no elemento <html> e atualiza todos
     * os botões de toggle presentes na página.
     * @param {'dark'|'light'} theme
     */
    function applyTheme(theme) {
        const html = document.documentElement;

        if (theme === 'dark') {
            html.classList.add(DARK_CLASS);
        } else {
            html.classList.remove(DARK_CLASS);
        }

        // Atualiza ícone e tooltip de todos os botões de toggle
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            const icon = btn.querySelector('i');
            if (!icon) return;

            if (theme === 'dark') {
                // Modo escuro ativo → botão deve oferecer "ir para claro"
                icon.className = 'fa-solid fa-sun';
                btn.title = 'Mudar para tema claro';
            } else {
                // Modo claro ativo → botão deve oferecer "ir para escuro"
                icon.className = 'fa-solid fa-moon';
                btn.title = 'Mudar para tema escuro';
            }
        });
    }

    /**
     * Alterna entre tema claro e escuro e persiste a escolha.
     * Chamada pelo atributo onclick="toggleTheme()" nas views.
     */
    window.toggleTheme = function () {
        const current  = document.documentElement.classList.contains(DARK_CLASS) ? 'dark' : 'light';
        const next     = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem(STORAGE_KEY, next);
        applyTheme(next);
    };

    // ─── Inicialização ──────────────────────────────────────────────────────────
    // Aplica o tema assim que o script é carregado (antes do DOMContentLoaded)
    // para evitar o "flash" de tema incorreto.
    applyTheme(getStoredTheme());

    // Reaplica o ícone dos botões após o DOM estar pronto
    document.addEventListener('DOMContentLoaded', function () {
        const current = document.documentElement.classList.contains(DARK_CLASS) ? 'dark' : 'light';
        applyTheme(current);
    });

    // Escuta mudanças na preferência do SO (somente se o usuário não tiver
    // escolhido manualmente um tema)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
})();
