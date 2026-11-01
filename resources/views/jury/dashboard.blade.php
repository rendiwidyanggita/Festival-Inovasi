<x-layouts.app>
    <x-slot name="header">
        Portal Dewan Juri
    </x-slot>

    @if($festivals->count() > 0)
        @foreach($festivals as $festival)
            @php
                $participations = $festival->juryParticipations ?? collect();
                $totalSubcategories = $participations->count();
                $openSubcategories = $participations->filter(fn($p) => $p->awardCategory->scoring_status->value === 'open')->count();
                $completedSubcategories = $participations->filter(fn($p) => ($p->progress['percentage'] ?? 0) >= 100)->count();
                $inProgressSubcategories = $participations->filter(fn($p) => ($p->progress['percentage'] ?? 0) > 0 && ($p->progress['percentage'] ?? 0) < 100)->count();
            @endphp

            <div class="space-y-6 mb-10">
                <!-- Executive Jury Welcome Banner (Flowbite / Preline UI inspired) -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 md:p-8 text-white shadow-xl shadow-indigo-950/20 border border-indigo-900/40">
                    <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute left-1/3 -top-16 w-60 h-60 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 backdrop-blur-sm">
                                    <svg class="size-3.5 text-indigo-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    Dewan Juri Terverifikasi
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $festival->name }} ({{ $festival->year }})
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white text-balance">
                                Selamat Datang, {{ auth()->user()->name }}
                            </h1>
                            <p class="mt-2 text-sm text-slate-300 max-w-2xl text-pretty leading-relaxed">
                                Portal penilaian independen Festival Inovasi. Lakukan evaluasi objektif terhadap inovasi dan unit instansi peserta dengan rentang skor 6 hingga 10 sesuai rubrik kriteria berbobot.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($participations->count() > 0 && $festival->status->value === 'preparation')
                                <a href="{{ route('jury.participations.index', $festival) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/15 text-white border border-white/20 shadow-sm backdrop-blur-sm transition-all duration-150 active:scale-[0.96]">
                                    <svg class="size-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Ubah Pilihan Subkategori
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if($participations->count() > 0)
                    <!-- Metric Stats Bar (DaisyUI / Preline UI Stats Pattern) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Stat 1: Total Subkategori -->
                        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Subkategori Diikuti</span>
                                <div class="mt-1 text-2xl md:text-3xl font-extrabold text-slate-900 tabular-nums">
                                    {{ $totalSubcategories }}
                                </div>
                                <span class="text-[11px] text-slate-500">Penugasan resmi juri</span>
                            </div>
                            <div class="size-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                        </div>

                        <!-- Stat 2: Terbuka untuk Penilaian -->
                        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Siap Dinilai (Open)</span>
                                <div class="mt-1 text-2xl md:text-3xl font-extrabold text-emerald-600 tabular-nums">
                                    {{ $openSubcategories }}
                                </div>
                                <span class="text-[11px] text-emerald-600 font-medium">Bisa diinput sekarang</span>
                            </div>
                            <div class="size-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>

                        <!-- Stat 3: Sedang Dinilai -->
                        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dalam Proses</span>
                                <div class="mt-1 text-2xl md:text-3xl font-extrabold text-blue-600 tabular-nums">
                                    {{ $inProgressSubcategories }}
                                </div>
                                <span class="text-[11px] text-blue-600 font-medium">Draft tersimpan</span>
                            </div>
                            <div class="size-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>

                        <!-- Stat 4: Selesai 100% -->
                        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Penilaian Selesai</span>
                                <div class="mt-1 text-2xl md:text-3xl font-extrabold text-purple-600 tabular-nums">
                                    {{ $completedSubcategories }}
                                </div>
                                <span class="text-[11px] text-purple-600 font-medium">Tuntas dievaluasi</span>
                            </div>
                            <div class="size-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Subcategories Table (Preline UI Table with Progress & Action Chips) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div class="p-5 md:p-6 border-b border-slate-200/80 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Daftar Subkategori Penugasan</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Pilih subkategori untuk mulai atau melanjutkan proses penilaian kriteria.</p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                {{ $participations->count() }} Subkategori
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                        <th class="py-3.5 px-6">Klaster</th>
                                        <th class="py-3.5 px-6">Subkategori</th>
                                        <th class="py-3.5 px-6">Status Scoring</th>
                                        <th class="py-3.5 px-6">Progress Evaluasi</th>
                                        <th class="py-3.5 px-6 text-right">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($participations as $participation)
                                        @php
                                            $cat = $participation->awardCategory;
                                            $progressData = $participation->progress ?? ['assessed' => 0, 'total' => 0, 'percentage' => 0];
                                            $isComplete = $progressData['percentage'] >= 100;
                                        @endphp
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $cat->festivalCategory->assessment_type->value === 'innovation' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                                    {{ $cat->festivalCategory->name }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 font-semibold text-slate-900">
                                                {{ $cat->name }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold
                                                    {{ $cat->scoring_status->value === 'preparation' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 
                                                      ($cat->scoring_status->value === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 
                                                      'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                                    <span class="size-1.5 rounded-full {{ $cat->scoring_status->value === 'open' ? 'bg-emerald-500 animate-pulse' : ($cat->scoring_status->value === 'preparation' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                                    {{ $cat->scoring_status->label() }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3 min-w-[160px]">
                                                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                                        <div class="h-full rounded-full transition-all duration-500 {{ $isComplete ? 'bg-emerald-500' : ($progressData['percentage'] > 0 ? 'bg-blue-600' : 'bg-slate-300') }}"
                                                             style="width: {{ $progressData['percentage'] }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-bold tabular-nums {{ $isComplete ? 'text-emerald-600' : 'text-slate-700' }}">
                                                        {{ $progressData['assessed'] }}/{{ $progressData['total'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                                @if($cat->scoring_status->value === 'open')
                                                    <a href="{{ route('jury.subcategories.show', $cat) }}" 
                                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ $isComplete ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-600/30' }} transition-all duration-150 active:scale-[0.96]">
                                                        @if($isComplete)
                                                            <svg class="size-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                            Tinjau Nilai
                                                        @elseif($progressData['percentage'] > 0)
                                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            Lanjutkan
                                                        @else
                                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            Mulai Menilai
                                                        @endif
                                                    </a>
                                                @elseif($cat->scoring_status->value === 'preparation')
                                                    <span class="inline-flex items-center gap-1 text-xs text-amber-600 font-medium">
                                                        <svg class="size-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Menunggu Dibuka
                                                    </span>
                                                @else
                                                    <a href="{{ route('jury.subcategories.show', $cat) }}" 
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                                        Lihat Arsip
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Evaluation Rubric & Code of Ethics Notice (Flowbite Banner) -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 rounded-2xl p-5 flex items-start gap-4">
                        <div class="size-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-xs text-slate-600 leading-relaxed">
                            <h4 class="font-bold text-slate-900 mb-1">Panduan Standar Penilaian Sesuai SK Dewan Juri</h4>
                            <p>
                                1. Nilai wajib berupa bilangan bulat dengan rentang <strong>6 s/d 10</strong> untuk setiap kriteria.<br>
                                2. Gunakan tombol <em>Simpan Draft</em> apabila ingin mencatat nilai sementara tanpa mengirimkannya langsung.<br>
                                3. Tombol <em>Submit Penilaian</em> akan mengunci nilai dan menyalurkan skor ke sistem monitoring superadmin untuk ditinjau.
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Empty Subcategory State -->
                    <div class="bg-white rounded-2xl p-8 md:p-12 border border-slate-200/80 shadow-xs text-center max-w-lg mx-auto">
                        <div class="size-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4 ring-8 ring-indigo-50/50">
                            <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Belum Ada Subkategori Terpilih</h3>
                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                            Anda belum memilih subkategori penilaian yang akan Anda evaluasi pada festival ini.
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('jury.participations.index', $festival) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/30 transition-all duration-150 active:scale-[0.96]">
                                Pilih Subkategori Penilaian Sekarang
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <!-- No Festival Assigned State -->
        <div class="bg-white rounded-2xl p-8 md:p-12 border border-slate-200/80 shadow-xs text-center max-w-lg mx-auto my-12">
            <div class="size-16 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-4">
                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum Ada Penugasan Festival</h3>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Akun juri Anda belum didaftarkan pada festival yang aktif. Hubungi Superadmin untuk penerbitan SK Penugasan.
            </p>
        </div>
    @endif
</x-layouts.app>
