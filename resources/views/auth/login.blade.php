<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LogiSync WMS</title>
    <!-- Tailwind CSS via CDN para estilização imediata -->
    <script>tailwindConfig = { darkMode: 'class' };</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-gray-100 dark:bg-slate-950 font-sans antialiased transition-colors duration-300">

    <!-- Botão de tema no canto superior direito -->
    <div class="fixed top-4 right-4 z-50">
        <button
            onclick="toggleTheme()"
            data-theme-toggle
            id="login-theme-toggle"
            class="p-2.5 rounded-lg bg-white dark:bg-slate-800 shadow-md border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all duration-200 text-gray-600 dark:text-gray-300"
            title="Alternar tema"
            aria-label="Alternar entre tema claro e escuro"
        >
            <i class="fa-solid fa-moon text-base"></i>
        </button>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        <!-- Logo Centralizada -->
        <div class="mb-8">
            <a href="/">
                <!-- Espaço para sua Logo -->
                <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync" class="w-56 h-auto">
            </a>
        </div>

        <!-- Card de Login -->
        <div class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-slate-900 shadow-xl rounded-lg border-t-4 border-blue-600 dark:border-blue-500">
            
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white">Login</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Acesse o sistema de gerenciamento de armazém</p>
            </div>

            <!-- Alerta de Erros -->
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Campo de Email -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                    <input type="email" name="email" placeholder="exemplo@logisync.com" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Campo de Senha -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Senha</label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Lembrar-me e Links Auxiliares -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-slate-600 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2">Lembrar-me</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Esqueceu a senha?</a>
                    @endif
                </div>

                <!-- Botão de Ação -->
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-lg transform active:scale-95 transition-all">
                    Entrar no Sistema
                </button>
            </form>

            <!-- Rodapé do Card -->
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Não tem uma conta? 
                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">Criar conta agora</a>
                </p>
            </div>
        </div>

        <!-- Footer da Página -->
        <p class="mt-6 text-xs text-gray-400 dark:text-gray-500">© {{ date('Y') }} LogiSync WMS. Todos os direitos reservados.</p>
    </div>

</body>
</html>