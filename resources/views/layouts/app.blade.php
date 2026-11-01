<x-layouts.app>
    @hasSection('title')
        <x-slot name="header">
            @yield('title')
        </x-slot>
    @endif

    @yield('content')
</x-layouts.app>
