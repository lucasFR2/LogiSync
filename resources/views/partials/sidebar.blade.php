@php
    $currentRoute = request()->route()->getName();
    $user = auth()->user();
    
    $allNavItems = [
        ['route' => 'dashboard',           'icon' => 'fa-chart-pie',               'label' => 'Dashboard'],
        ['route' => 'products.index',      'icon' => 'fa-cubes',                   'label' => 'Produtos',      'permission' => 'products.view'],
        ['route' => 'categories.index',    'icon' => 'fa-tags',                    'label' => 'Categorias',    'permission' => 'categories.manage'],
        ['route' => 'inventory.index',     'icon' => 'fa-arrow-right-to-bracket',  'label' => 'Entradas',      'permission' => 'inventory.view'],
        ['route' => 'manifestations.index','icon' => 'fa-file-invoice',            'label' => 'Manifestação',  'permission' => 'manifests.manage'],
        ['route' => 'suppliers.index',     'icon' => 'fa-address-book',            'label' => 'Fornecedores',  'permission' => 'suppliers.manage'],
        ['route' => 'customers.index',     'icon' => 'fa-users',                   'label' => 'Clientes',      'permission' => 'customers.manage'],
        ['route' => 'employees.index',     'icon' => 'fa-users-gear',              'label' => 'Gestão de Funcionários', 'permission' => 'users.manage'],
        ['route' => 'logs.index',          'icon' => 'fa-list-check',              'label' => 'Logs do Sistema',       'permission' => 'logs.view'],
        ['route' => 'roles.index',         'icon' => 'fa-id-card-clip',            'label' => 'Cargos e Funções',      'permission' => 'roles.manage'],
    ];

    $navItems = array_filter($allNavItems, function($item) use ($user) {
        if (!isset($item['permission'])) return true;
        return $user->hasPermission($item['permission']);
    });
@endphp

<aside class="sidebar" id="sidebar">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:.75rem;text-decoration:none;">
            <div style="background:var(--accent); color:var(--accent-fg); width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:800; font-family:'Outfit';">LS</div>
            <span style="font-family:'Outfit'; font-weight:800; font-size:1.25rem; color:var(--text-primary); letter-spacing:-0.03em;">LogiSync</span>
        </a>
    </div>

    {{-- Navigation --}}
    <div class="sidebar-section-label">Menu Principal</div>
    <nav class="sidebar-nav">
        @foreach($navItems as $item)
            @php
                $isActive = str_starts_with($currentRoute, explode('.', $item['route'])[0]);
            @endphp
            <a href="{{ route($item['route']) }}"
               class="nav-item {{ $isActive ? 'active' : '' }}">
                <i class="fa-solid {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="min-width:0;">
                <div class="sidebar-user-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary w-full" style="width:100%; padding:0.5rem; justify-content:center;">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sair</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebar-overlay"
     onclick="document.getElementById('sidebar').classList.remove('open'); this.classList.add('hidden');"
     class="hidden"
     style="position:fixed;inset:0;background:rgba(2,6,23,0.3);z-index:99;backdrop-filter:blur(4px);">
</div>
