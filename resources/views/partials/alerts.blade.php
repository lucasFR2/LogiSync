@if(session('success'))
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <div style="font-weight:700; margin-bottom:0.35rem;">Corrija os erros abaixo:</div>
            <ul style="margin:0; padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
