<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - LogiSync WMS</title>
    <!-- Tailwind CSS via CDN para estilização imediata -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        <!-- Logo Centralizada -->
        <div class="mb-8">
            <a href="/">
                <!-- Espaço para sua Logo -->
                <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync" class="w-56 h-auto">
            </a>
        </div>

        <!-- Card de Registro -->
        <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-xl rounded-lg border-t-4 border-blue-600">
            
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-extrabold text-gray-800">Criar Conta</h2>
                <p class="text-sm text-gray-500">Cadastre-se para gerenciar seu armazém</p>
            </div>

            <!-- Listagem de Erros -->
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="name" placeholder="Seu nome" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Campo E-mail -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail Corporativo</label>
                    <input type="email" name="email" placeholder="exemplo@logisync.com" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                 <!-- Campo Cargo -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cargo</label>
                    <input type="text" name="role" placeholder="Seu cargo atual" value="{{ old('role') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
                <!-- Campo CPF -->
                 <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CPF</label>
                    <input id="cpf" type="text" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required
                        inputmode="numeric" autocomplete="off" maxlength="14"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Campo Senha -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Senha</label>
                    <input type="password" name="password" placeholder="Mínimo 8 caracteres" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" placeholder="Repita a senha" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>

                <!-- Botão de Ação -->
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-lg transform active:scale-95 transition-all">
                    Registrar Agora
                </button>
            </form>

            <!-- Rodapé do Card -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-600">
                    Já possui uma conta? 
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 transition-colors">Fazer Login</a>
                </p>
            </div>
        </div>

        <!-- Footer da Página -->
        <p class="mt-6 text-xs text-gray-400">© {{ date('Y') }} LogiSync WMS. Todos os direitos reservados.</p>
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
