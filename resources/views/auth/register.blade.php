<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionário - LogiSync WMS</title>
    
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
            background: var(--bg-base);
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
        }

        /* Left Side: Brand & Visual */
        .login-visual {
            flex: 1.2;
            position: sticky;
            top: 0;
            height: 100vh;
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
            opacity: 0.3;
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
            font-size: 3.5rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: white;
            font-family: 'Outfit';
        }

        .login-visual p {
            font-size: 1.2rem;
            opacity: 0.7;
            line-height: 1.6;
        }

        /* Right Side: Form */
        .login-form-side {
            flex: 0.8;
            background: var(--bg-base);
            display: flex;
            flex-direction: column;
            padding: 4rem;
            position: relative;
            overflow-y: auto;
        }

        .login-card {
            width: 100%;
            max-width: 600px;
            margin: auto;
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
            font-family: 'Outfit';
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

        .form-section-title {
            font-family: 'Outfit';
            font-size: 1.1rem;
            font-weight: 700;
            margin: 1.5rem 0 0.5rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.5rem;
        }

        .auth-footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        @media (max-width: 1200px) {
            .login-visual { flex: 1; }
            .login-form-side { flex: 1; }
        }

        @media (max-width: 992px) {
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
                <h1>Junte-se à nossa equipe operacional.</h1>
                <p>Crie sua conta de funcionário para começar a gerenciar o fluxo logístico com eficiência e precisão.</p>
                
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
            
            <div style="position:absolute; top:2rem; left:2rem;">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary" style="padding:0.5rem 1rem; font-size:0.8rem;">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div style="position:absolute; top:2rem; right:2rem;">
                <button class="icon-btn" data-theme-toggle title="Mudar Tema">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>

            <div class="login-card">
                <div class="login-header">
                    <div style="width:48px; height:48px; background:var(--accent); color:var(--accent-fg); border-radius:14px; display:flex; align-items:center; justify-content:center; font-weight:800; font-family:'Outfit'; font-size:1.5rem; margin-bottom:1.5rem;">LS</div>
                    <h2>Cadastro de Funcionário</h2>
                    <p>Preencha os dados abaixo para criar um novo  acesso ao sistema.</p>
                </div>

                @if(session('success'))
                    <div class="alert badge-success" style="margin-bottom:1.5rem; font-size:0.875rem;">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert badge-danger" style="margin-bottom:1.5rem; font-size:0.875rem;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="auth-form" enctype="multipart/form-data">
                    @csrf

                    <div class="form-section-title">
                        <i class="fa-solid fa-id-card"></i> Dados Pessoais
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group" style="grid-column: 1/-1;">
                            <label class="form-label">Nome Completo <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Nome completo do funcionário">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Cargo <span style="color:var(--red);">*</span></label>
                            <select name="role" required class="form-select">
                                <option value="" disabled selected>Selecione o cargo</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">CPF <span style="color:var(--red);">*</span></label>
                            <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" required class="form-input" placeholder="000.000.000-00" maxlength="14">
                        </div>

                        <div class="form-group">
                            <label class="form-label">RG <span style="color:var(--red);">*</span></label>
                            <input type="text" name="rg" id="rg" value="{{ old('rg') }}" required class="form-input" placeholder="00.000.000-0" maxlength="12">
                        </div>

                        <div class="form-group" style="grid-column: 1/-1;">
                            <label class="form-label">E-mail Corporativo <span style="color:var(--red);">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="email@logisync.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Telefone / Celular <span style="color:var(--red);">*</span></label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required class="form-input" placeholder="(00) 00000-0000">
                        </div>
                    </div>

                    <div class="form-section-title">
                        <i class="fa-solid fa-map-location-dot"></i> Endereço
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">CEP <span style="color:var(--red);">*</span></label>
                            <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}" required class="form-input" placeholder="00000-000" maxlength="9">
                        </div>

                        <div class="form-group" style="grid-column: 1/-1;">
                            <label class="form-label">Logradouro <span style="color:var(--red);">*</span></label>
                            <input type="text" name="address" value="{{ old('address') }}" required class="form-input" placeholder="Rua, Avenida, etc.">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Número <span style="color:var(--red);">*</span></label>
                            <input type="text" name="number" value="{{ old('number') }}" required class="form-input" placeholder="123">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Bairro <span style="color:var(--red);">*</span></label>
                            <input type="text" name="neighborhood" value="{{ old('neighborhood') }}" required class="form-input" placeholder="Ex: Centro">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Cidade <span style="color:var(--red);">*</span></label>
                            <input type="text" name="city" value="{{ old('city') }}" required class="form-input" placeholder="Cidade">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Estado (UF) <span style="color:var(--red);">*</span></label>
                            <input type="text" name="state" value="{{ old('state') }}" required class="form-input" placeholder="SP" maxlength="2">
                        </div>
                    </div>

                    <div class="form-section-title">
                        <i class="fa-solid fa-file-pdf"></i> Documentação
                    </div>
                    
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Anexar Documentos (PDF, Imagem) <span style="color:var(--red);">*</span></label>
                        <div style="position:relative; background:var(--bg-base); border:2px dashed var(--border); border-radius:var(--r-md); padding:1.5rem; text-align:center; transition:all 0.3s;" id="drop-zone">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                            <div style="font-size:0.875rem; color:var(--text-secondary);">
                                Clique para selecionar ou arraste o arquivo aqui
                            </div>
                            <input type="file" name="documents[]" class="form-input" style="position:absolute; inset:0; opacity:0; cursor:pointer;" onchange="updateFileName(this)" multiple>
                            <div id="file-name" style="margin-top:0.5rem; font-size:0.8rem; font-weight:700; color:var(--accent);"></div>
                        </div>
                    </div>

                    <div class="form-section-title">
                        <i class="fa-solid fa-key"></i> Segurança
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Senha de Acesso <span style="color:var(--red);">*</span></label>
                            <input type="password" name="password" required class="form-input" placeholder="••••••••">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirmar Senha <span style="color:var(--red);">*</span></label>
                            <input type="password" name="password_confirmation" required class="form-input" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:1rem; margin-top:1.5rem; font-size:1rem;">
                        <i class="fa-solid fa-user-plus"></i>
                        Finalizar Cadastro
                    </button>
                </form>

                <div class="auth-footer">
                    &copy; {{ date('Y') }} LogiSync Global.
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('file-name');
            const dropZone = document.getElementById('drop-zone');
            if (input.files && input.files.length > 0) {
                const names = Array.from(input.files).map(f => f.name).join(' | ');
                fileNameDisplay.textContent = input.files.length + ' arquivo(s) selecionado(s): ' + names;
                dropZone.style.borderColor = 'var(--accent)';
                dropZone.style.background = 'var(--accent-subtle)';
            } else {
                fileNameDisplay.textContent = '';
                dropZone.style.borderColor = 'var(--border)';
                dropZone.style.background = 'var(--bg-base)';
            }
        }

        // Simple Input Masks
        document.getElementById('cpf').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4");
            else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3})$/, "$1.$2.$3");
            else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3})$/, "$1.$2");
            e.target.value = v;
        });

        document.getElementById('rg').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 9) v = v.slice(0, 9);
            if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{1})$/, "$1.$2.$3-$4");
            else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3})$/, "$1.$2.$3");
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3})$/, "$1.$2");
            e.target.value = v;
        });

        document.getElementById('zip_code').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 8) v = v.slice(0, 8);
            if (v.length > 5) v = v.replace(/^(\d{5})(\d{0,3})$/, "$1-$2");
            e.target.value = v;
        });

        document.getElementById('phone').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
            else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
            e.target.value = v;
        });
    </script>
</body>
</html>
