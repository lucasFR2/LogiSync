@extends('layouts.app')

@section('title', 'Acesso Negado')
@section('page-title', '403')
@section('page-subtitle', 'Restrição de Acesso')

@section('content')
<div class="anim-entrance flex flex-col items-center justify-center text-center" style="min-height: 70vh; padding: 2rem;">
    <div class="card max-w-2xl w-full overflow-visible" style="position: relative;">
        <div class="card-body flex flex-col items-center gap-6 py-12">
            {{-- Icon Hexagon with Glow --}}
            <div style="position: relative; margin-bottom: 1rem;">
                <div style="width: 120px; height: 120px; background: var(--red-bg); color: var(--red); border-radius: 30px; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; position: relative; z-index: 2; border: 1px solid rgba(239, 68, 68, 0.2); box-shadow: 0 10px 30px -10px rgba(239, 68, 68, 0.3);">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                {{-- Decorative circles --}}
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 140px; height: 140px; border: 2px dashed var(--red); opacity: 0.1; border-radius: 50%; animation: spin 20s linear infinite;"></div>
            </div>
            
            <div class="flex flex-col gap-2">
                <h2 style="font-family: 'Outfit'; font-size: 2.5rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.04em;">Acesso Restrito</h2>
                <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: var(--text-muted); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.2em;">
                    <span style="width: 8px; height: 8px; background: var(--red); border-radius: 50%;"></span>
                    Erro 403 • Forbidden
                </div>
            </div>
            
            <p style="color: var(--text-secondary); font-size: 1.125rem; line-height: 1.7; max-width: 520px; font-weight: 500;">
                Desculpe o transtorno, mas sua conta não possui as <span style="color: var(--text-primary); font-weight: 700;">permissões eletivas</span> necessárias para acessar este módulo do LogiSync.
            </p>

            <div style="display: flex; flex-direction: column; gap: 1rem; width: 100%; max-width: 320px; margin-top: 1rem;">
                <a href="{{ route('dashboard') }}" class="btn" style="background: var(--accent); color: var(--accent-fg); display: flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 1rem; border-radius: var(--r-md); font-weight: 700; transition: all 0.3s var(--spring); box-shadow: 0 10px 20px -5px var(--accent-glow);">
                    <i class="fa-solid fa-house"></i>
                    <span>Ir para o Dashboard</span>
                </a>
                
                <button onclick="history.back()" class="btn" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 1rem; border-radius: var(--r-md); font-weight: 600; filter: none;">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Página Anterior</span>
                </button>
            </div>
        </div>

        {{-- Help Footer --}}
        <div class="card-footer" style="background: var(--bg-hover); border-top: 1px solid var(--border); padding: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-question" style="color: var(--blue);"></i>
            <span style="font-size: 0.875rem; color: var(--text-secondary);">
                Precisa de acesso? Informe ao seu <strong style="color: var(--text-primary);">gerente de operações</strong>.
            </span>
        </div>
    </div>

    {{-- Abstract background effect --}}
    <div style="position: absolute; width: 600px; height: 600px; border-radius: 50%; background: radial-gradient(circle, var(--red-bg) 0%, transparent 70%); top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: -1; opacity: 0.4;"></div>
</div>

<style>
    @keyframes spin { from { transform: translate(-50%, -50%) rotate(0deg); } to { transform: translate(-50%, -50%) rotate(360deg); } }
    .anim-entrance { animation: fadeInScale 0.7s var(--spring); }
    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.95) translateY(30px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>
@endsection
