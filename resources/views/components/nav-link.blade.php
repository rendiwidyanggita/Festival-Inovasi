@props(['active' => false])

<a {{ $attributes->merge(['class' => $active 
    ? 'bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium' 
    : 'text-gray-300 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors'
]) }}>
    {{ $slot }}
</a>
