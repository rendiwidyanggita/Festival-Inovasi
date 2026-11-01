<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $header ?? config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-white text-xl font-bold">Festival Inovasi</span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-4">
                            @if(auth()->user()->isSuperadmin())
                                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                    Dashboard
                                </x-nav-link>
                                <x-nav-link :href="route('admin.festivals.index')" :active="request()->routeIs('admin.festivals.*')">
                                    Festival
                                </x-nav-link>
                                <x-nav-link :href="route('admin.festival-categories.index')" :active="request()->routeIs('admin.festival-categories.*') || request()->routeIs('admin.award-categories.*')">
                                    Kategori
                                </x-nav-link>
                                <x-nav-link :href="route('admin.institutions.index')" :active="request()->routeIs('admin.institutions.*')">
                                    Instansi
                                </x-nav-link>
                                <x-nav-link :href="route('admin.innovations.index')" :active="request()->routeIs('admin.innovations.*')">
                                    Inovasi
                                </x-nav-link>
                                <x-nav-link :href="route('admin.juries.index')" :active="request()->routeIs('admin.juries.*') || request()->routeIs('admin.festival-juries.*')">
                                    Juri
                                </x-nav-link>
                                <x-nav-link :href="route('admin.assessment-criteria.index')" :active="request()->routeIs('admin.assessment-criteria.*')">
                                    Kriteria
                                </x-nav-link>
                            @elseif(auth()->user()->isJury())
                                <x-nav-link :href="route('jury.dashboard')" :active="request()->routeIs('jury.dashboard')">
                                    Dashboard
                                </x-nav-link>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-200 text-sm">
                                {{ auth()->user()->name }} 
                                <span class="text-gray-400">({{ auth()->user()->role->label() }})</span>
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
                        {{ session('warning') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
