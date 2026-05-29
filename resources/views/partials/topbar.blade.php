<header class="topbar">
    <div class="topbar-left">
        {{-- Mobile menu toggle --}}
        <button class="icon-btn" id="mobile-menu-btn" style="margin-right:1rem;display:none;"
                onclick="document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sidebar-overlay').classList.toggle('hidden');">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        <div>
            <h1 style="margin:0; font-family:'Outfit';">@yield('page-title', 'Dashboard')</h1>
            <p style="margin:0; font-size:0.875rem; color:var(--text-muted); font-weight:500;">@yield('page-subtitle', 'Bem-vindo ao LogiSync WMS')</p>
        </div>
    </div>

    <div class="topbar-right">
        {{-- Actions --}}
        <div class="flex" style="gap:0.75rem;">
            <button class="icon-btn" title="Notificações">
                <i class="fa-solid fa-bell"></i>
            </button>
            
            <button class="icon-btn" data-theme-toggle title="Alternar tema">
                <i class="fa-solid fa-circle-half-stroke"></i>
            </button>
        </div>

        <div style="width:1px; height:24px; background:var(--border); margin:0 0.5rem;"></div>

        {{-- User avatar --}}
        <div class="flex" style="gap:0.75rem; cursor:pointer;" title="{{ auth()->user()->name }}">
            <div class="sidebar-user-avatar" style="width:42px; height:42px; border-radius:12px; box-shadow:var(--shadow-sm);">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="display:none;" class="d-md-block">
                <div style="font-size:0.875rem; font-weight:700; color:var(--text-primary);">{{ auth()->user()->name }}</div>
                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">{{ auth()->user()->role->name ?? auth()->user()->role->NAME ?? '' }}</div>
            </div>
        </div>
    </div>
</header>

<style>
@media (max-width: 768px) {
    #mobile-menu-btn { display:flex !important; }
}
@media (min-width: 992px) {
    .d-md-block { display:block !important; }
}
</style>
