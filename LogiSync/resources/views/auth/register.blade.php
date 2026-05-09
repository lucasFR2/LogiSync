<!DOCTYPE html>
<html lang="pt-br" id="root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - LogiSync WMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Carregar tema ANTES de renderizar
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = savedTheme ? savedTheme === 'dark' : prefersDark;
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans antialiased">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <!-- Botão de Toggle de Tema -->
        <button id="themeToggle" onclick="toggleTheme()" class="absolute top-6 right-6 p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" title="Alternar tema">
            <i class="fa-solid fa-moon dark:hidden"></i>
            <i class="fa-solid fa-sun hidden dark:inline"></i>
        </button>

        <!-- Logo Centralizada -->
        <div class="mb-8">
            <a href="/">
                <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync" class="w-56 h-auto">
            </a>
        </div>

        <!-- Card de Registro -->
        <div class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-gray-800 shadow-xl rounded-lg border-t-4 border-blue-600 dark:border-blue-500">

            <div class="mb-6 text-center">
                <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white">Criar Conta</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Cadastre-se para gerenciar seu armazém</p>
            </div>

            <!-- Listagem de Erros -->
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-200 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Campo Nome -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                    <input type="text" name="name" placeholder="Seu nome" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Campo E-mail -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">E-mail Corporativo</label>
                    <input type="email" name="email" placeholder="exemplo@logisync.com" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                 <!-- Campo Cargo -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
                    <input type="text" name="role" placeholder="Seu cargo atual" value="{{ old('role') }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Campo CPF -->
                 <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">CPF</label>
                    <input id="cpf" type="text" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required
                        inputmode="numeric" autocomplete="off" maxlength="14"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Campo Senha -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Senha</label>
                    <input type="password" name="password" placeholder="Mínimo 8 caracteres" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" placeholder="Repita a senha" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Botão de Ação -->
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md shadow-lg transform active:scale-95 transition-all">
                    Registrar Agora
                </button>
            </form>

            <!-- Rodapé do Card -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Já possui uma conta?
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">Fazer Login</a>
                </p>
            </div>
        </div>

        <!-- Footer da Página -->
        <p class="mt-6 text-xs text-gray-400 dark:text-gray-600">© {{ date('Y') }} LogiSync WMS. Todos os direitos reservados.</p>
    </div>

<script>
    (function () {
        function formatCpf(value) {
            const digits = String(value || '').replace(/\D/g, '').slice(0, 11);

            const p1 = digits.slice(0, 3);
            const p2 = digits.slice(3, 6);
            const p3 = digits.slice(6, 9);
            const p4 = digits.slice(9, 11);

            let out = p1;
            if (p2) out += '.' + p2;
            if (p3) out += '.' + p3;
            if (p4) out += '-' + p4;
            return out;
        }

        function applyCpfMask(input) {
            if (!input) return;

            input.addEventListener('input', function () {
                const caretAtEnd = input.selectionStart === input.value.length;
                input.value = formatCpf(input.value);
                if (caretAtEnd) {
                    input.setSelectionRange(input.value.length, input.value.length);
                }
            });

            input.addEventListener('blur', function () {
                input.value = formatCpf(input.value);
            });

            input.value = formatCpf(input.value);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                applyCpfMask(document.getElementById('cpf'));
            });
        } else {
            applyCpfMask(document.getElementById('cpf'));
        }
    })();
</script>

</body>
</html>
