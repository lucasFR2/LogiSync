<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - LogiSync WMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/logisync.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; padding:2rem 1rem; background: var(--bg-base); }
        .auth-page { width:100%; max-width:480px; }
        .auth-logo-wrap { text-align:center; margin-bottom:2rem; }
        .auth-logo-wrap img { height:40px; }
        .auth-logo-name { font-size:1.6rem; font-weight:800; color:var(--accent); letter-spacing:-.04em; margin-top:.5rem; }
        .auth-logo-tag { font-size:.78rem; color:var(--text-muted); }
        .auth-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--r-xl); padding:2rem; box-shadow:var(--shadow-lg); }
        .auth-card h2 { font-size:1.35rem; font-weight:700; margin-bottom:.25rem; }
        .auth-card p { font-size:.85rem; color:var(--text-secondary); margin-bottom:1.75rem; }
        .auth-form { display:flex; flex-direction:column; gap:.875rem; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
        .auth-footer { text-align:center; font-size:.78rem; color:var(--text-muted); margin-top:2rem; }
        .auth-link { color:var(--accent); text-decoration:none; font-weight:600; }
        .auth-link:hover { text-decoration:underline; }
        .theme-float { position:fixed; top:1rem; right:1rem; }
        @media(max-width:480px) { .form-row { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <button class="icon-btn theme-float" data-theme-toggle title="Alternar tema">
        <i class="fa-solid fa-moon"></i>
    </button>

    <div class="auth-page anim-fade-up">
        <div class="auth-logo-wrap">
            <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync">
            <div class="auth-logo-name">LogiSync</div>
            <div class="auth-logo-tag">Warehouse Management System</div>
        </div>

        <div class="auth-card">
            <h2>Criar conta</h2>
            <p>Preencha os dados para acessar o sistema WMS</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nome Completo</label>
                        <div class="input-icon-wrap">
                            <i class="input-icon fa-solid fa-user"></i>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Seu nome" required class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cargo</label>
                        <div class="input-icon-wrap">
                            <i class="input-icon fa-solid fa-id-badge"></i>
                            <input type="text" name="role" value="{{ old('role') }}"
                                   placeholder="Seu cargo" required class="form-input">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">E-mail Corporativo</label>
                    <div class="input-icon-wrap">
                        <i class="input-icon fa-solid fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="seu@email.com" required class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">CPF</label>
                    <div class="input-icon-wrap">
                        <i class="input-icon fa-solid fa-id-card"></i>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}"
                               placeholder="000.000.000-00" required inputmode="numeric"
                               autocomplete="off" maxlength="14" class="form-input">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Senha</label>
                        <div class="input-icon-wrap">
                            <i class="input-icon fa-solid fa-lock"></i>
                            <input type="password" name="password"
                                   placeholder="Mín. 8 caracteres" required class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar Senha</label>
                        <div class="input-icon-wrap">
                            <i class="input-icon fa-solid fa-lock"></i>
                            <input type="password" name="password_confirmation"
                                   placeholder="Repita" required class="form-input">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full" style="margin-top:.25rem;padding:.75rem;">
                    <i class="fa-solid fa-user-plus"></i>
                    Registrar Agora
                </button>
            </form>

            <p style="text-align:center;font-size:.8125rem;color:var(--text-secondary);margin-top:1.5rem;">
                Já tem conta?
                <a href="{{ route('login') }}" class="auth-link">Fazer login</a>
            </p>
        </div>

        <p class="auth-footer">© {{ date('Y') }} LogiSync WMS · Todos os direitos reservados.</p>
    </div>

    <script>
        (function () {
            function formatCpf(v) {
                const d = String(v||'').replace(/\D/g,'').slice(0,11);
                let o = d.slice(0,3);
                if(d.length>3) o+='.'+d.slice(3,6);
                if(d.length>6) o+='.'+d.slice(6,9);
                if(d.length>9) o+='-'+d.slice(9,11);
                return o;
            }
            const cpf = document.getElementById('cpf');
            if(cpf){
                cpf.addEventListener('input', function(){ this.value = formatCpf(this.value); });
                cpf.addEventListener('blur',  function(){ this.value = formatCpf(this.value); });
            }
        })();
    </script>
</body>
</html>
