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
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            justify-content: center;
            align-items: center;
        }

        /* Form Side */
        .login-form-side {
            flex: 1;
            background: var(--bg-base);
            display: flex;
            flex-direction: column;
            padding: 4rem 2rem;
            position: relative;
            overflow-y: auto;
            width: 100%;
        }

        .login-card {
            width: 100%;
            max-width: 850px;
            margin: auto;
            padding: 3.5rem;
            background: var(--glass-bg);
            border-radius: var(--r-2xl);
            box-shadow: var(--shadow-2xl);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .login-header {
            margin-bottom: 3rem;
        }

        .login-header h2 {
            font-size: 2.25rem;
            margin-bottom: 0.5rem;
            font-family: 'Outfit';
            font-weight: 800;
            color: var(--text-primary);
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 500;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .form-section-title {
            font-family: 'Outfit';
            font-size: 1.25rem;
            font-weight: 800;
            margin: 2rem 0 0.75rem;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 0.875rem;
            border-bottom: 2px solid var(--border-subtle);
            padding-bottom: 0.75rem;
        }

        .auth-footer {
            margin-top: 4rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .login-form-side { padding: 3rem 1.5rem; }
            .login-card { padding: 2.5rem 1.5rem; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Form Section -->
        <div class="login-form-side anim-entrance" style="animation-delay: 0.2s;">
            
            <div style="position:absolute; top:2rem; left:2rem; z-index:100;">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary" style="padding:0.625rem 1.25rem; font-size:0.875rem; border-radius:12px; box-shadow: var(--shadow-sm);">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Voltar à Listagem
                </a>
            </div>

            <div style="position:absolute; top:2rem; right:2rem; z-index:100;">
                <button class="icon-btn" data-theme-toggle title="Mudar Tema" style="width:42px; height:42px; border-radius:12px; background: var(--bg-surface); border: 1px solid var(--border);">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>

            <div class="login-card">
                <div class="login-header">
                    <div style="width:56px; height:56px; background: linear-gradient(135deg, var(--accent), #4f46e5); color:white; border-radius:16px; display:flex; align-items:center; justify-content:center; font-weight:900; font-family:'Outfit'; font-size:1.75rem; margin-bottom:1.75rem; box-shadow: 0 8px 16px -4px var(--accent-glow);">LS</div>
                    <h2>Cadastro de Funcionário</h2>
                    <p>Insira as informações do novo colaborador para habilitar o acesso.</p>
                </div>

                @if(session('success'))
                    <div class="badge badge-success" style="width:100%; padding:1rem; margin-bottom:2rem; font-size:0.9rem; justify-content:center; border-radius:12px;">
                        <i class="fa-solid fa-circle-check mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="badge badge-danger" style="width:100%; padding:1rem; margin-bottom:2rem; font-size:0.9rem; flex-direction:column; align-items:center; border-radius:12px;">
                        <div style="display:flex; align-items:center; gap:0.5rem; font-weight:800; margin-bottom:0.5rem;">
                            <i class="fa-solid fa-circle-exclamation"></i> Ops! Algo deu errado
                        </div>
                        <div style="font-size:0.8rem; opacity:0.9;">
                            @foreach($errors->all() as $error)
                                <div>• {{ $error }}</div>
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
                            <select name="role_id" required class="form-select">
                                <option value="" disabled selected>Selecione o cargo</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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

                        <div class="form-group">
                            <label class="form-label">Data de Admissão <span style="color:var(--red);">*</span></label>
                            <input type="date" name="admission_date" value="{{ old('admission_date') }}" required class="form-input">
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
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complement" value="{{ old('complement') }}" class="form-input" placeholder="Apto, Bloco, etc.">
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

                    <button type="submit" class="btn btn-primary" style="padding:1.125rem; margin-top:2rem; font-size:1.1rem; width:100%; border-radius:14px; background: linear-gradient(135deg, var(--accent), #4f46e5); border:none; box-shadow: 0 10px 20px -5px var(--accent-glow);">
                        <i class="fa-solid fa-user-plus mr-2"></i>
                        Finalizar Cadastro de Funcionário
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
