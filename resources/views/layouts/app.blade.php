<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LogiSync WMS - Sistema de Gerenciamento de Armazém">
    <title>@yield('title', 'LogiSync WMS') - LogiSync</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Design System --}}
    <link rel="stylesheet" href="{{ asset('css/logisync.css') }}">

    {{-- Theme (must run before render) --}}
    <script src="{{ asset('js/theme.js') }}"></script>

    @stack('styles')
</head>
<body>
    <div class="app-shell">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main area --}}
        <div style="flex:1; display:flex; flex-direction:column; min-width:0;">
            @include('partials.topbar')

            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
