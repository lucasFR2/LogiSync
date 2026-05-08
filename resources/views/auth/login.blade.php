<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LogiSync WMS</title>
    <!-- Tailwind CSS via CDN para estilização imediata -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard-dark.css') }}">
</head>
<body class="bg-[#020617] font-sans antialiased">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        <!-- Logo Centralizada -->
        <div class="mb-8">
            <a href="/">
                <!-- Espaço para sua Logo -->
                <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync" class="w-56 h-auto">
            </a>
        </div>

        <!-- Card de Login -->
        <div class="w-full sm:max-w-md px-8 py-10 bg-[#0F172A] shadow-xl rounded-lg border border-[#1E293B]">
            
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-extrabold text-[#FFFFFF]">Login</h2>
                <p class="text-sm text-[#94A3B8]">Acesse o sistema de gerenciamento de armazém</p>
            </div>

            <!-- Alerta de Erros -->
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-900/20 border-l-4 border-red-600 text-red-400 text-sm rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Campo de Email -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#FFFFFF] mb-1">E-mail</label>
                    <input type="email" name="email" placeholder="exemplo@logisync.com" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-[#1E293B] bg-[#0F172A] text-[#FFFFFF] placeholder-[#94A3B8] rounded-md focus:ring-2 focus:ring-blue-500/50 focus:border-[#2563EB] outline-none transition-all">
                </div>

                <!-- Campo de Senha -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-[#FFFFFF] mb-1">Senha</label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full px-4 py-2 border border-[#1E293B] bg-[#0F172A] text-[#FFFFFF] placeholder-[#94A3B8] rounded-md focus:ring-2 focus:ring-blue-500/50 focus:border-[#2563EB] outline-none transition-all">
                </div>

                <!-- Lembrar-me e Links Auxiliares -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-[#94A3B8]">
                        <input type="checkbox" name="remember" class="rounded border-[#1E293B] bg-[#0F172A] text-[#2563EB] shadow-sm focus:ring-blue-500">
                        <span class="ml-2">Lembrar-me</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-[#2563EB] hover:text-blue-400 transition-colors">Esqueceu a senha?</a>
                    @endif
                </div>

                <!-- Botão de Ação -->
                <button type="submit" 
                    class="w-full bg-[#2563EB] hover:bg-blue-700 text-[#FFFFFF] font-bold py-2 px-4 rounded-md shadow-lg transform active:scale-95 transition-all">
                    Entrar no Sistema
                </button>
            </form>

            <!-- Rodapé do Card -->
            <div class="mt-8 pt-6 border-t border-[#1E293B] text-center">
                <p class="text-sm text-[#94A3B8]">
                    Não tem uma conta? 
                    <a href="{{ route('register') }}" class="font-bold text-[#2563EB] hover:text-blue-400 transition-colors">Criar conta agora</a>
                </p>
            </div>
        </div>

        <!-- Footer da Página -->
        <p class="mt-6 text-xs text-[#94A3B8]">© {{ date('Y') }} LogiSync WMS. Todos os direitos reservados.</p>
    </div>

</body>
</html>