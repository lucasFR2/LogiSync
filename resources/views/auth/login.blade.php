<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - LogiSync WMS</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/logisync.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: var(--bg-base);
        }

        .login-container {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Left Side: Brand & Visual */
        .login-visual {
            flex: 1.2;
            position: relative;
            background: #020617;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            overflow: hidden;
        }

        .login-visual img.bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
            filter: grayscale(0.5);
        }

        .login-visual-content {
            position: relative;
            z-index: 10;
            color: white;
            max-width: 600px;
        }

        .brand-pill {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: inline-block;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-visual h1 {
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: white;
        }

        .login-visual p {
            font-size: 1.25rem;
            opacity: 0.7;
            line-height: 1.6;
        }

        /* Right Side: Form */
        .login-form-side {
            flex: 0.8;
            background: var(--bg-base);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 3rem;
            background: var(--bg-surface);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.9375rem;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--accent);
        }

        .auth-footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        @media (max-width: 1024px) {
            .login-visual { display: none; }
            .login-form-side { flex: 1; padding: 2rem; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Visual Section -->
        <div class="login-visual anim-entrance">
            <img src="{{ asset('images/login-bg.jpg') }}" class="bg" alt="Warehouse">
            <div class="login-visual-content">
                <div class="brand-pill">LogiSync WMS v2.0</div>
                <h1>Sincronize sua logística de ponta a ponta.</h1>
                <p>A plataforma definitiva para gerenciamento de armazéns, otimizada para performance e escalabilidade.</p>
                
                <div style="display:flex; gap:2rem; margin-top:4rem; opacity:0.8;">
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <span style="font-size:1.5rem; font-weight:800; font-family:'Outfit';">99.9%</span>
                        <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">Uptime</span>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <span style="font-size:1.5rem; font-weight:800; font-family:'Outfit';">24/7</span>
                        <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">Monitoramento</span>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <span style="font-size:1.5rem; font-weight:800; font-family:'Outfit';">100%</span>
                        <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">Seguro</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="login-form-side anim-entrance" style="animation-delay: 0.2s;">
            
            <div style="position:absolute; top:2rem; right:2rem;">
                <button class="icon-btn" data-theme-toggle title="Mudar Tema">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>

            <div class="login-card">
                <div class="login-header">
                    <div style="width:48px; height:48px; background:var(--accent); color:var(--accent-fg); border-radius:14px; display:flex; align-items:center; justify-content:center; font-weight:800; font-family:'Outfit'; font-size:1.5rem; margin-bottom:1.5rem;">LS</div>
                    <h2>Bem-vindo</h2>
                    <p>Insira suas credenciais para acessar o painel administrativo.</p>
                </div>

                @if($errors->any())
                    <div class="alert badge-danger" style="margin-bottom:1.5rem; font-size:0.875rem;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">E-mail Corporativo</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nome@empresa.com" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Senha de Acesso</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="password-field" placeholder="••••••••" required class="form-input" style="padding-right:3.5rem;">
                            <button type="button" onclick="togglePassword()" class="password-toggle">
                                <i class="fa-solid fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; margin-top:0.5rem;">
                        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.875rem; color:var(--text-secondary);">
                            <input type="checkbox" name="remember" style="accent-color:var(--accent); width:16px; height:16px; border-radius:4px;">
                            Manter conectado
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size:0.875rem; color:var(--accent); text-decoration:none; font-weight:600;">Esqueceu a senha?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:1rem; margin-top:1rem; font-size:1rem;">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Acessar Sistema
                    </button>
                </form>

                <div class="auth-footer">
                    &copy; {{ date('Y') }} LogiSync Global. Todos os direitos reservados.
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById('password-field');
            const icon  = document.getElementById('eye-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }
    </script>
</body>
</html>