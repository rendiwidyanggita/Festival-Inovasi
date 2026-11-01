<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Festival Inovasi</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-blue-800">Festival Inovasi</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Selamat Datang di Festival Inovasi
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Platform penilaian dan apresiasi inovasi terbaik Indonesia
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Active Festivals -->
        @if($festivals->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Festival Aktif</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($festivals as $festival)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $festival->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $festival->status->label() }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $festival->year }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $festival->name }}</h3>
                            <p class="text-gray-600 mb-4">
                                {{ $festival->festivalCategories()->count() }} kategori • 
                                {{ $festival->awardCategories()->count() }} subkategori
                            </p>
                            <a href="{{ route('public.festival', $festival) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Lihat Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-12 bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada festival aktif</h3>
                <p class="mt-2 text-gray-600">Festival akan ditampilkan di halaman ini setelah dibuat.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-gray-600">
                &copy; {{ date('Y') }} Festival Inovasi. Hak cipta dilindungi.
            </p>
        </div>
    </footer>
</body>
</html>
