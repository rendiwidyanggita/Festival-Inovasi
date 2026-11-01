<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $festival->name }} - Festival Inovasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition-colors">Festival Inovasi</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ auth()->user()->isSuperadmin() ? route('admin.dashboard') : route('jury.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-600 hover:text-red-600 font-medium transition-colors">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all">
                            Masuk Portal
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $festival->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $festival->year }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    {{ $festival->status->value === 'active' ? 'bg-green-100 text-green-800' : 
                      ($festival->status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ $festival->status->label() }}
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if($festival->hasPublishedResults())
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-green-800">
                    <strong>Hasil sudah diumumkan!</strong> 
                    Lihat peringkat dan pemenang pada setiap subkategori.
                </p>
            </div>
        @endif

        <!-- Festival Categories -->
        @if($festival->festivalCategories->count() > 0)
            <div class="space-y-8">
                @foreach($festival->festivalCategories as $category)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-900">{{ $category->name }}</h2>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $category->assessment_type->value === 'innovation' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $category->assessment_type->label() }}
                                </span>
                            </div>
                            
                            @if($category->awardCategories->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($category->awardCategories as $awardCategory)
                                        <a href="{{ route('public.category', [$festival, $awardCategory]) }}" 
                                           class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow transition-all">
                                            <h3 class="font-medium text-gray-900 mb-2">{{ $awardCategory->name }}</h3>
                                            <p class="text-sm text-gray-500">
                                                {{ $awardCategory->assessment_type->value === 'innovation' ? $awardCategory->innovations()->count() : $awardCategory->institutions()->count() }} peserta
                                            </p>
                                            @if($festival->hasPublishedResults())
                                                <p class="text-sm text-green-600 mt-1">
                                                    {{ $awardCategory->winner_count }} pemenang
                                                </p>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Belum ada subkategori.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kategori</h3>
                <p class="mt-1 text-sm text-gray-500">Kategori akan ditampilkan setelah ditambahkan.</p>
            </div>
        @endif
    </main>

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
