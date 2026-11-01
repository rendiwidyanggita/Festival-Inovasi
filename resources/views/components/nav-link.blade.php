@props(['active' => false, 'icon' => null, 'title' => ''])

@php
$classes = $active
    ? 'flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold bg-blue-600 text-white shadow-md shadow-blue-600/25 transition-all duration-200'
    : 'flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800/80 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} title="{{ $title }}">
    @if($icon)
        <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
            {!! $icon !!}
        </span>
    @endif
    <span class="sidebar-text truncate transition-opacity duration-200">
        {{ $slot }}
    </span>
</a>
