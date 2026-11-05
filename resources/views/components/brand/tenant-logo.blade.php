{{--
  Componente: x-brand.tenant-logo
  Mostra o logótipo registado pela empresa (tenant) e, na sua ausência ou em
  caso de erro ao carregar a imagem, o símbolo canónico ZBIZ+.

  Props:
    - src:      URL do logótipo da empresa (null => símbolo ZBIZ+)
    - name:     Nome da empresa (mostrado ao lado quando show-name = true e existe logo)
    - size:     'sm' (h-8) | 'md' (h-10, padrão) | 'lg' (h-12)
    - showName: bool — mostra o nome (empresa ou "ZBIZ+") ao lado do símbolo
    - subtitle: texto opcional por baixo do nome
--}}
@props([
    'src' => null,
    'name' => null,
    'size' => 'md',
    'showName' => false,
    'subtitle' => null,
])

@php
    $heights = ['sm' => 'h-8 w-8', 'md' => 'h-10 w-10', 'lg' => 'h-12 w-12'];
    $boxClass = $heights[$size] ?? $heights['md'];
    $hasLogo = filled($src);
    $displayName = $hasLogo ? ($name ?: 'ZBIZ+') : null;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 min-w-0 select-none']) }} data-brand="{{ $hasLogo ? 'tenant' : 'zbiz' }}">
    <span class="relative {{ $boxClass }} shrink-0 flex items-center justify-center rounded-xl overflow-hidden {{ $hasLogo ? 'bg-white border border-slate-200 dark:border-slate-700' : '' }}">
        @if($hasLogo)
            <img src="{{ $src }}"
                 alt="{{ $name ?: 'Logótipo da empresa' }}"
                 class="w-full h-full object-contain p-0.5"
                 data-brand-img
                 onerror="this.style.display='none'; this.parentElement.classList.remove('bg-white','border'); this.nextElementSibling.style.display='block';">
        @endif
        <svg viewBox="0 0 48 48" fill="none" class="w-full h-full" role="img" aria-label="ZBIZ+"
             data-brand-fallback
             @if($hasLogo) style="display:none" @endif>
            <rect width="48" height="48" rx="12" fill="#0f172a"/>
            <path d="M12 18H27L12 33H33" stroke="#10b981" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M36 10.5V19.5M31.5 15H40.5" stroke="#34d399" stroke-width="3.5" stroke-linecap="round"/>
        </svg>
    </span>

    @if($showName)
        <span class="min-w-0 flex flex-col leading-tight">
            @if($hasLogo)
                <span class="text-sm font-black tracking-tight text-slate-900 dark:text-white truncate font-heading">{{ $displayName }}</span>
            @else
                <span class="text-sm font-black tracking-tight text-slate-900 dark:text-white font-heading">ZBIZ<span class="text-emerald-500">+</span></span>
            @endif
            @if($subtitle)
                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 truncate">{{ $subtitle }}</span>
            @endif
        </span>
    @endif
</span>

