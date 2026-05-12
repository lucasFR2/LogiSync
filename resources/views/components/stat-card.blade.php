@props([
    'label',
    'value',
    'icon',
    'bg' => 'var(--bg-hover)',
    'color' => 'var(--accent)',
    'badge' => null,
    'badgeType' => 'info',
    'badgeIcon' => null,
    'style' => ''
])

<div class="stat-card" style="{{ $style }}">
    <div class="stat-icon" style="background: {{ $bg }}; color: {{ $color }};">
        <i class="fa-solid {{ $icon }}"></i>
    </div>
    <div class="stat-label">{{ $label }}</div>
    <div class="stat-value" style="{{ isset($valueStyle) ? $valueStyle : '' }}">{{ $value }}</div>
    
    @if($badge)
        <div class="badge badge-{{ $badgeType }}" style="width: fit-content; margin-top: 0.5rem;">
            @if($badgeIcon) <i class="fa-solid {{ $badgeIcon }}"></i> @endif
            {{ $badge }}
        </div>
    @endif
    
    {{ $slot }}
</div>
