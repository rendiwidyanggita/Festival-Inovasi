<x-layouts.app>
    <x-slot name="header">
        Dashboard Superadmin
    </x-slot>

    @if(session('selected_festival_id') && $currentFestival)
        <div class="space-y-6">
            <!-- Executive Banner (Preline UI / Flowbite style) -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 via-white to-indigo-50/40 p-6 md:p-8 text-slate-900 shadow-sm border border-blue-100/80">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100/80 text-blue-700 border border-blue-200/80">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $currentFestival->status->value === 'active' ? 'bg-emerald-400' : 'bg-amber-400' }} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $currentFestival->status->value === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                </span>
                                Periode Aktif: {{ $currentFestival->year }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentFestival->status->value === 'active' ? 'bg-emerald-100/80 text-emerald-700 border border-emerald-200/80' : ($currentFestival->status->value === 'preparation' ? 'bg-amber-100/80 text-amber-700 border border-amber-200/80' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                {{ $currentFestival->status->label() }}
                            </span>
                            @if($currentFestival->hasPublishedResults())
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-700 border border-emerald-200/80">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Hasil Diumumkan
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 text-balance">
                            {{ $currentFestival->name }}
                        </h1>
                        <p class="mt-2 text-sm text-slate-600 max-w-2xl text-pretty leading-relaxed">
                            Pusat komando tata kelola Festival Inovasi. Pantau kemajuan pengisian nilai oleh dewan juri, verifikasi kepatuhan multikriteria, dan tetapkan pemenang secara transparan.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 self-start md:self-auto shrink-0">
                        <a href="{{ route('admin.select-festival.index') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-xs transition-all duration-150 active:scale-[0.96]">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Ganti Periode
                        </a>
                        <a href="{{ route('public.festival', $currentFestival) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-600/20 transition-all duration-150 active:scale-[0.96]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Halaman Publik
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Metric Cards (Preline UI / DaisyUI Stats Component Style) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                <!-- Card 1: Inovasi ASN -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-200 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Inovasi Terdaftar</span>
                        <div class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center ring-4 ring-blue-50/50 group-hover:scale-105 transition-transform duration-200">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums">
                            {{ $stats['innovations_count'] ?? $currentFestival->innovations()->count() }}
                        </span>
                        <span class="text-xs font-medium text-slate-500">Proposal</span>
                    </div>
                    <div class="mt-3 flex items-center gap-1.5 text-xs text-blue-600 font-medium">
                        <a href="{{ route('admin.innovations.index') }}" class="inline-flex items-center gap-1 hover:underline">
                            Kelola Inovasi
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Instansi Peserta -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Instansi Peserta</span>
                        <div class="size-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center ring-4 ring-emerald-50/50 group-hover:scale-105 transition-transform duration-200">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums">
                            {{ $stats['institutions_count'] ?? $currentFestival->institutions()->count() }}
                        </span>
                        <span class="text-xs font-medium text-slate-500">Unit Kerja</span>
                    </div>
                    <div class="mt-3 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                        <a href="{{ route('admin.institutions.index') }}" class="inline-flex items-center gap-1 hover:underline">
                            Kelola Instansi
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Card 3: Subkategori Penghargaan -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-200 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Subkategori Penghargaan</span>
                        <div class="size-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center ring-4 ring-amber-50/50 group-hover:scale-105 transition-transform duration-200">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums">
                            {{ $stats['subcategories_count'] ?? $currentFestival->awardCategories()->count() }}
                        </span>
                        <span class="text-xs font-medium text-slate-500">dalam {{ $stats['categories_count'] ?? $currentFestival->festivalCategories()->count() }} Klaster</span>
                    </div>
                    <div class="mt-3 flex items-center gap-1.5 text-xs text-amber-600 font-medium">
                        <a href="{{ route('admin.festival-categories.index') }}" class="inline-flex items-center gap-1 hover:underline">
                            Daftar Subkategori
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Card 4: Dewan Juri Aktif -->
                <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-purple-200 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dewan Juri Bertugas</span>
                        <div class="size-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center ring-4 ring-purple-50/50 group-hover:scale-105 transition-transform duration-200">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums">
                            {{ $stats['active_juries_count'] ?? $currentFestival->festivalJuries()->where('status', 'active')->count() }}
                        </span>
                        <span class="text-xs font-medium text-slate-500">Penilai SK</span>
                    </div>
                    <div class="mt-3 flex items-center gap-1.5 text-xs text-purple-600 font-medium">
                        <a href="{{ route('admin.festival-juries.index') }}" class="inline-flex items-center gap-1 hover:underline">
                            Penugasan Juri
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Preline UI Stepper / Lifecycle Progression -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Alur Tata Kelola Festival</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau transisi tahapan dari pembentukan kriteria hingga pengumuman pemenang resmi.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        Standar PRD v0.1
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                    <!-- Step 1: Persiapan & Roster -->
                    <div class="relative p-4 rounded-xl border bg-slate-50/70 border-slate-200">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="size-7 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                                ✓
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Tahap 1</span>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900">Persiapan & Roster Juri</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Pendaftaran instansi, inovasi, bobot kriteria, dan penugasan dewan juri.</p>
                    </div>

                    <!-- Step 2: Scoring Terbuka -->
                    @php
                        $openCatsCount = $currentFestival->awardCategories()->where('scoring_status', 'open')->count();
                    @endphp
                    <div class="relative p-4 rounded-xl border {{ $openCatsCount > 0 ? 'bg-blue-50/60 border-blue-200 ring-2 ring-blue-500/10' : 'bg-slate-50/70 border-slate-200' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="size-7 rounded-lg {{ $openCatsCount > 0 ? 'bg-blue-600 text-white animate-pulse' : 'bg-slate-200 text-slate-600' }} font-bold text-xs flex items-center justify-center shadow-xs">
                                2
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider {{ $openCatsCount > 0 ? 'text-blue-700' : 'text-slate-600' }}">Tahap 2</span>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900">Penilaian Berbobot</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            {{ $openCatsCount > 0 ? $openCatsCount . ' subkategori sedang dibuka penilaiannya.' : 'Buka penilaian saat roster juri siap dikunci permanen.' }}
                        </p>
                    </div>

                    <!-- Step 3: Monitoring & Kunci Nilai -->
                    @php
                        $lockedCatsCount = $currentFestival->awardCategories()->where('scoring_status', 'completed')->count();
                    @endphp
                    <div class="relative p-4 rounded-xl border {{ $lockedCatsCount > 0 ? 'bg-purple-50/60 border-purple-200' : 'bg-slate-50/70 border-slate-200' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="size-7 rounded-lg {{ $lockedCatsCount > 0 ? 'bg-purple-600 text-white' : 'bg-slate-200 text-slate-600' }} font-bold text-xs flex items-center justify-center shadow-xs">
                                3
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider {{ $lockedCatsCount > 0 ? 'text-purple-700' : 'text-slate-600' }}">Tahap 3</span>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900">Review & Kunci Nilai</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Verifikasi skor juri di matriks monitoring, revisi catatan, atau kunci permanen.</p>
                    </div>

                    <!-- Step 4: Penetapan & Publikasi -->
                    <div class="relative p-4 rounded-xl border {{ $currentFestival->hasPublishedResults() ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50/70 border-slate-200' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="size-7 rounded-lg {{ $currentFestival->hasPublishedResults() ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-bold text-xs flex items-center justify-center shadow-xs">
                                4
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider {{ $currentFestival->hasPublishedResults() ? 'text-emerald-700' : 'text-slate-600' }}">Tahap 4</span>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900">Pemenang & Laporan PDF</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Penetapan juara sesuai ranking, berita acara PDF, dan publikasi terbuka.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Action Hub (Flowbite Cards with Micro-Interactions) -->
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-3.5">Modul Operasional Utama</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Action 1: Monitoring Penilaian -->
                    <a href="{{ route('admin.scoring.monitoring') }}" 
                       class="group relative bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-purple-300 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="size-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-200 shadow-xs">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-purple-600 transition-colors">Monitoring Penilaian</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Pantau matriks penilaian real-time tiap juri, kunci berkas, atau minta revisi.</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-purple-600">
                            Buka Monitoring
                            <svg class="size-3.5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>

                    <!-- Action 2: Penetapan Pemenang -->
                    <a href="{{ route('admin.winners.index') }}" 
                       class="group relative bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-amber-300 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="size-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-200 shadow-xs">
                                <svg class="size-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-amber-600 transition-colors">Penetapan Juara</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Tinjau ranking on-the-fly terhitung otomatis dari asesmen locked, dan assign juara.</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-amber-600">
                            Kelola Pemenang
                            <svg class="size-3.5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>

                    <!-- Action 3: Laporan Hasil PDF -->
                    <a href="{{ route('admin.reports.index') }}" 
                       class="group relative bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-rose-300 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="size-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all duration-200 shadow-xs">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-rose-600 transition-colors">Laporan & Berita Acara</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Cetak dokumen PDF resmi penetapan pemenang berbobot dengan tanda tangan dewan juri.</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-rose-600">
                            Cetak Laporan PDF
                            <svg class="size-3.5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>

                    <!-- Action 4: Kelola Festival & Kriteria -->
                    <a href="{{ route('admin.festivals.index') }}" 
                       class="group relative bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-blue-300 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="size-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-200 shadow-xs">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">Konfigurasi Master Data</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Atur tahun festival, status siklus, bobot persentase 4 kriteria, dan kuota juara.</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs font-semibold text-blue-600">
                            Konfigurasi
                            <svg class="size-3.5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Subcategory Status Table Preview (Preline UI Table style) -->
            @php
                $recentSubcategories = $currentFestival->awardCategories()
                    ->with(['festivalCategory', 'juryParticipations'])
                    ->orderBy('sort_order')
                    ->take(8)
                    ->get();
            @endphp
            @if($recentSubcategories->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 md:p-6 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ringkasan Status Subkategori</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Status pembukaan scoring dan kuota dewan juri per subkategori penghargaan.</p>
                        </div>
                        <a href="{{ route('admin.festival-categories.index') }}" 
                           class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                            Lihat Semua Subkategori
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="py-3.5 px-6">Klaster</th>
                                    <th class="py-3.5 px-6">Subkategori Penghargaan</th>
                                    <th class="py-3.5 px-6">Dewan Juri</th>
                                    <th class="py-3.5 px-6">Status Scoring</th>
                                    <th class="py-3.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($recentSubcategories as $cat)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-4 px-6 font-medium text-slate-600 text-xs">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $cat->festivalCategory->assessment_type->value === 'innovation' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                                {{ $cat->festivalCategory->name }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-900">
                                            {{ $cat->name }}
                                        </td>
                                        <td class="py-4 px-6 text-slate-600 text-xs tabular-nums">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="size-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                {{ $cat->juryParticipations->count() }} Juri Terdaftar
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold
                                                {{ $cat->scoring_status->value === 'preparation' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 
                                                  ($cat->scoring_status->value === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 
                                                  'bg-blue-50 text-blue-700 border border-blue-200/60') }}">
                                                <span class="size-1.5 rounded-full {{ $cat->scoring_status->value === 'open' ? 'bg-emerald-500 animate-pulse' : ($cat->scoring_status->value === 'preparation' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                                                {{ $cat->scoring_status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="{{ route('admin.scoring.monitoring', ['festival' => $currentFestival->id, 'subcategory' => $cat->id]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition-colors">
                                                Pantau
                                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- No Festival Selected State (Flowbite Card Style) -->
        <div class="max-w-xl mx-auto my-12 bg-white rounded-3xl p-8 md:p-10 border border-slate-200/80 shadow-lg text-center">
            <div class="size-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-5 ring-8 ring-blue-50/50">
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Belum Ada Festival Terpilih</h2>
            <p class="text-sm text-slate-500 mt-2 text-pretty leading-relaxed">
                Pilih periode festival aktif yang sedang berjalan untuk mulai mengelola kriteria, memantau dewan juri, dan menetapkan pemenang.
            </p>
            <div class="mt-6 flex justify-center">
                <a href="{{ route('admin.select-festival.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-600/30 transition-all duration-150 active:scale-[0.96]">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Pilih Periode Festival Sekarang
                </a>
            </div>
        </div>
    @endif
</x-layouts.app>
