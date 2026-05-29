<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LogiSync WMS - Sistema de Gerenciamento de Armazém">
    <title>@yield('title', 'LogiSync WMS') - LogiSync</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Charts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Design System --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        zinc: {
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/logisync.css') }}?v=2.1.1">

    {{-- Theme (must run before render) --}}
    <script src="{{ asset('js/theme.js') }}"></script>

    @stack('styles')
</head>
<body>
    <div class="app-shell">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main area --}}
        <div style="flex:1; display:flex; flex-direction:column; min-width:0;">
            @include('partials.topbar')

            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://unpkg.com/imask"></script>
    <script>
        window.initMasks = function(container = document) {
            const maskMap = {
                'phone': { mask: [ { mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' } ] },
                'cpf': { mask: '000.000.000-00' },
                'cnpj': { mask: '00.999.999/0000-00' },
                'cep': { mask: '00000-000' },
                'date': { mask: Date, pattern: 'DD/MM/YYYY', blocks: { DD: { mask: IMask.MaskedRange, from: 1, to: 31 }, MM: { mask: IMask.MaskedRange, from: 1, to: 12 }, YYYY: { mask: IMask.MaskedRange, from: 1900, to: 9999 } }, format: function (date) { let day = date.getDate(); let month = date.getMonth() + 1; let year = date.getFullYear(); if (day < 10) day = '0' + day; if (month < 10) month = '0' + month; return [day, month, year].join('/'); }, parse: function (str) { let yearMonthDay = str.split('/'); return new Date(yearMonthDay[2], yearMonthDay[1] - 1, yearMonthDay[0]); } },
                'currency': { mask: 'R$ num', blocks: { num: { mask: Number, thousandsSeparator: '.', padFractionalZeros: true, fractionalZerosCount: 2, radix: ',', mapToRadix: ['.'] } } },
                'percentage': { mask: 'num%', blocks: { num: { mask: Number, scale: 2, thousandsSeparator: '.', padFractionalZeros: true, fractionalZerosCount: 2, radix: ',', mapToRadix: ['.'], min: 0, max: 100 } } }
            };

            container.querySelectorAll('[data-mask]').forEach(el => {
                if (el.mask) el.mask.destroy(); // Avoid duplicates
                el.mask = IMask(el, maskMap[el.getAttribute('data-mask')]);
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.initMasks();

            // Password toggle logic
            document.querySelectorAll('.password-toggle').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
