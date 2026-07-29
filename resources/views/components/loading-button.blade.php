@props([
    'type' => 'submit',
    'variant' => 'primary', // primary, secondary, danger, outline
    'icon' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2.5 px-6 py-3 text-base font-semibold rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed min-h-[48px] cursor-pointer';

$variantClasses = [
    'primary'   => 'bg-[#0f2c59] hover:bg-[#07132a] active:bg-[#050e1f] text-white border-transparent focus:ring-[#ffb703] shadow-sm hover:shadow dark:bg-sky-600 dark:hover:bg-sky-500',
    'gold'      => 'bg-[#ffb703] hover:bg-[#fb8500] active:bg-[#e07700] text-slate-900 font-bold border-transparent focus:ring-[#ffb703] shadow-sm hover:shadow',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 border-slate-200 focus:ring-slate-400 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:border-slate-700',
    'danger'    => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white border-transparent focus:ring-rose-500 shadow-sm hover:shadow',
    'outline'   => 'bg-transparent hover:bg-slate-50 text-slate-700 border-slate-300 focus:ring-[#ffb703] dark:text-slate-200 dark:border-slate-600 dark:hover:bg-slate-800',
][$variant] ?? 'bg-[#0f2c59] text-white';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]) }}>
    @if($icon)
        <i class="{{ $icon }} button-icon"></i>
    @endif
    <span class="button-text">{{ $slot }}</span>
</button>
