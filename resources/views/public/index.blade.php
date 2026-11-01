<!DOCTYPE html>
<html lang="id" class="scroll-smooth bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Festival Inovasi') }} - Portal Penilaian & Apresiasi Inovasi Daerah</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-slate-900 selection:bg-blue-600 selection:text-white">

    <!-- Top Civic Navigation -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/90 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">
                                Festival Inovasi
                            </span>
                            @if($activeFestival)
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-200">
                                    {{ $activeFestival->year }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 font-medium hidden sm:block">Portal Resmi Penilaian & Rekapitulasi</p>
                    </div>
                </a>

                <!-- Navigation Actions -->
                <div class="flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <a href="{{ auth()->user()->isSuperadmin() ? route('admin.dashboard') : route('jury.dashboard') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-500 shadow-md shadow-blue-600/30 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Dashboard {{ auth()->user()->isSuperadmin() ? 'Admin' : 'Juri' }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Masuk Portal</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-16 pb-24 lg:pt-24 lg:pb-32 bg-gradient-to-b from-slate-50 to-white">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-3xl mx-auto">
                <!-- Civic Pill -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-xs font-semibold text-slate-700 shadow-sm mb-6">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                    <span>Platform Penilaian Akuntabel & Objektif</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-blue-400">{{ $activeFestival ? $activeFestival->name : 'Festival Inovasi' }}</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-tight text-balance">
                    Mendorong Terobosan Pelayanan, <br>
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 bg-clip-text text-transparent">
                        Mengapresiasi Karya Terbaik
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="mt-6 text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto text-pretty">
                    Ajang apresiasi resmi bagi Aparatur Sipil Negara dan Lembaga Daerah yang menghadirkan inovasi solutif, berdampak nyata, dan berkelanjutan bagi masyarakat.
                </p>

                <!-- Actions -->
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <a href="#kategori" 
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-sm bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/40 transition-all duration-200 active:scale-[0.96]">
                        <span>Jelajahi Subkategori</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </a>
                    @if($activeFestival)
                        <a href="{{ route('public.festival', $activeFestival) }}" 
                           class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-sm bg-white text-slate-900 border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all duration-200 active:scale-[0.96]">
                            <span>Lihat Detail Festival ({{ $activeFestival->year }})</span>
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Live KPI Counter Strip -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 p-6 rounded-2xl bg-white border-2 border-slate-200 shadow-lg">
                <div class="p-4 text-center border-r border-slate-200 last:border-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight tabular-nums">
                        {{ $stats['total_innovations'] ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-600 mt-1">Inovasi Terdaftar</div>
                </div>
                <div class="p-4 text-center border-r border-slate-800/80 last:border-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-blue-600 tracking-tight tabular-nums">
                        {{ $stats['total_institutions'] ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-600 mt-1">Instansi / Unit</div>
                </div>
                <div class="p-4 text-center border-r border-slate-800/80 last:border-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-indigo-600 tracking-tight tabular-nums">
                        {{ $stats['total_subcategories'] ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-600 mt-1">Subkategori Lomba</div>
                </div>
                <div class="p-4 text-center">
                    <div class="text-3xl sm:text-4xl font-extrabold text-emerald-600 tracking-tight tabular-nums">
                        {{ $stats['total_juries'] ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-600 mt-1">Dewan Juri Terdaftar</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Active Festival Spotlight & Categories -->
    <section id="kategori" class="py-20 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Klasifikasi Penghargaan</span>
                    <h2 class="text-3xl font-extrabold text-white mt-2">
                    <h2 class="text-3xl font-extrabold text-slate-900 mt-2 text-balance">
                    </h2>
                    <p class="text-slate-400 text-sm mt-2 max-w-xl">
                    <p class="text-slate-600 text-sm mt-2 max-w-xl text-pretty">
                    </p>
                </div>
                @if($activeFestival)
                    <div class="mt-4 md:mt-0 flex items-center gap-3">
                        <span class="text-xs text-slate-600">Status Periode:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $activeFestival->status->value === 'ongoing' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                              ($activeFestival->status->value === 'preparation' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 
                              'bg-blue-50 text-blue-700 border border-blue-200') }}">
                            {{ $activeFestival->status->label() }}
                        </span>
                    </div>
                @endif
            </div>

            @if($activeFestival && $activeFestival->festivalCategories->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($activeFestival->festivalCategories as $category)
                        <div class="rounded-2xl bg-white border-2 border-slate-200 p-6 sm:p-8 hover:border-blue-300 hover:shadow-lg transition-all">
                            <div class="flex items-center justify-between pb-6 border-b border-slate-200">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium 
                                        {{ $category->assessment_type->value === 'innovation' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-cyan-50 text-cyan-700 border border-cyan-200' }}">
                                        Tipe Penilaian: {{ $category->assessment_type->label() }}
                                    </span>
                                    <h3 class="text-xl font-bold text-slate-900 mt-2">{{ $category->name }}</h3>
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $category->awardCategories->count() }} Subkategori
                                </span>
                            </div>

                            <!-- Subcategories List -->
                            <div class="mt-6 space-y-3">
                                @foreach($category->awardCategories as $sub)
                                    <a href="{{ route('public.category', [$activeFestival, $sub]) }}" 
                                       class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border-2 border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-150 group active:scale-[0.98]">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">
                                                {{ $sub->name }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-slate-500 font-mono tabular-nums">
                                                {{ $category->assessment_type->value === 'innovation' ? $sub->innovations()->count() . ' Inovasi' : $sub->institutions()->count() . ' Instansi' }}
                                            </span>
                                            <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center rounded-2xl bg-white border-2 border-slate-200 text-slate-600">
                    <p>Kategori belum dikonfigurasi pada festival aktif.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Governance & Credibility Pillars -->
    <section class="py-20 border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Tata Kelola Terpercaya</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-2 text-balance">
                    Prinsip Penilaian Objektif & Terbuka
                </h2>
                <p class="text-slate-600 text-sm mt-3 text-pretty">
                    Setiap tahapan dirancang dengan kontrol integritas ketat untuk memastikan hasil dapat dipertanggungjawabkan kepada publik.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1 -->
                <div class="p-8 rounded-2xl bg-white border-2 border-slate-200 hover:border-blue-300 hover:shadow-lg transition-all relative">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Penilaian Multikriteria Berbobot</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Nilai dihitung otomatis menggunakan formula rata-rata tertimbang ($\sum \text{skor} \times \text{bobot}$) dengan skala baku 6 sampai 10.
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="p-8 rounded-2xl bg-white border-2 border-slate-200 hover:border-blue-300 hover:shadow-lg transition-all relative">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Penguncian Roster Permanen</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Saat penilaian subkategori dibuka, seluruh peserta dan dewan juri penilai dikunci secara permanen guna mencegah manipulasi peserta.
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="p-8 rounded-2xl bg-white border-2 border-slate-200 hover:border-blue-300 hover:shadow-lg transition-all relative">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Audit Trail Terpadu & Sah</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Setiap perubahan nilai, pembukaan penilaian, penetapan pemenang, dan penerbitan laporan PDF tercatat dalam jejak audit digital.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Civic Footer -->
    <footer class="bg-slate-50 border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                <div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight">Festival Inovasi</span>
                    <p class="text-xs text-slate-600 mt-1">
                        Diselenggarakan oleh {{ $activeFestival->settings->organizer_name ?? 'Panitia Festival Inovasi Daerah' }}
                    </p>
                </div>
                <div class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} Festival Inovasi Daerah. Seluruh hak cipta dilindungi undang-undang.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
