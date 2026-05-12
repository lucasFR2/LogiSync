@if(session('success'))
    <div class="alert badge-success" style="padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; background-color:rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--green);">
        <i class="fa-solid fa-circle-check"></i>
        <span style="font-weight:600;">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert badge-danger" style="padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem; background-color:rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--red);">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span style="font-weight:600;">{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert badge-danger" style="padding:1rem; border-radius:var(--r-md); margin-bottom:1.5rem; background-color:rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--red);">
        <div style="display:flex; align-items:center; gap:0.75rem; font-weight:600; margin-bottom:0.5rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Por favor, corrija os seguintes erros:</span>
        </div>
        <ul style="margin:0; padding-left:2.5rem; font-size:0.9rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
