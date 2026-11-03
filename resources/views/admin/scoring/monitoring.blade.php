<x-layouts.app>
    <x-slot name="header">
        Monitoring Penilaian Dewan Juri
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Header Bar (Preline UI / Flowbite style) -->
        <div class="bg-white rounded-2xl p-5 md:p-6 border border-slate-200/80 shadow-xs">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <label for="festival" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        Periode Festival
                    </label>
                    <div class="relative">
                        <select name="festival" id="festival" 
                                class="w-full rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3.5 text-sm font-semibold text-slate-800 shadow-xs focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                            @foreach($festivals as $festival)
                                <option value="{{ $festival->id }}" {{ $selectedFestival == $festival->id ? 'selected' : '' }}>
                                    {{ $festival->name }} ({{ $festival->year }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="md:col-span-5">
                    <label for="subcategory" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        Subkategori Penilaian
                    </label>
                    <div class="relative">
                        <select name="subcategory" id="subcategory" 
                                class="w-full rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3.5 text-sm font-semibold text-slate-800 shadow-xs focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                            <option value="">-- Pilih Subkategori --</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ $selectedSubcategory == $subcategory->id ? 'selected' : '' }}>
                                    [{{ $subcategory->festivalCategory->name }}] {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-600/30 transition-all duration-150 active:scale-[0.96]">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Terapkan
                    </button>
                </div>
            </form>
        </div>

        @if($selectedSubcategory && $subcategories->find($selectedSubcategory))
            @php
                $activeCat = $subcategories->find($selectedSubcategory);
            @endphp
            <!-- Subcategory Status & Lifecycle Action Card (Preline UI Banner Style) -->
            <div class="bg-white rounded-2xl p-5 md:p-6 border border-slate-200/80 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $activeCat->festivalCategory->assessment_type->value === 'innovation' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ $activeCat->festivalCategory->name }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full text-xs font-semibold
                                {{ $activeCat->scoring_status->value === 'preparation' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 
                                  ($activeCat->scoring_status->value === 'open' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 
                                  'bg-blue-50 text-blue-700 border border-blue-200/60') }}">
                                <span class="size-1.5 rounded-full {{ $activeCat->scoring_status->value === 'open' ? 'bg-emerald-500 animate-pulse' : ($activeCat->scoring_status->value === 'preparation' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                                Status: {{ $activeCat->scoring_status->label() }}
                            </span>
                        </div>
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $activeCat->name }}</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Kuota Pemenang: <span class="font-bold text-slate-800">{{ $activeCat->winner_count }} Juara</span> • Roster Juri: <span class="font-bold text-slate-800">{{ $juries->count() }} Terdaftar</span> • Total Objek: <span class="font-bold text-slate-800">{{ $objects->count() }} Peserta</span>
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-2.5 shrink-0">
                        @if($activeCat->scoring_status->value === 'preparation')
                            <button type="button" 
                                    onclick="confirmOpenScoring('{{ $selectedSubcategory }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-emerald-600/30 transition-all duration-150 active:scale-[0.96]">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                Buka Penilaian & Kunci Roster
                            </button>
                        @elseif($activeCat->scoring_status->value === 'open')
                            <form method="POST" action="{{ route('admin.award-categories.close-scoring', $selectedSubcategory) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirmSubmit(event, 'Seluruh juri tidak dapat mengubah nilai lagi. Pastikan semua penilaian sudah dikunci sebelum menutup.', { title: 'Tutup Penilaian', confirmText: 'Ya, Tutup Penilaian' })"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-rose-600/30 transition-all duration-150 active:scale-[0.96]">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Tutup Penilaian (Selesai)
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.award-categories.rankings', $activeCat) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all duration-150 active:scale-[0.96]">
                            <svg class="size-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            Lihat Ranking & Pemenang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Monitoring Matrix Table (Flowbite / Preline UI High-Density Pattern) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-5 md:p-6 border-b border-slate-200/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Matriks Evaluasi Real-Time</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Status evaluasi per dewan juri untuk setiap objek inovasi / instansi.</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-amber-400"></span> Draft</span>
                        <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-blue-500"></span> Terkirim</span>
                        <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-purple-500"></span> Terkunci</span>
                        <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-rose-500"></span> Revisi</span>
                    </div>
                </div>
                
                @if($juries->count() > 0 && $objects->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-full">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="py-3.5 px-6 sticky left-0 bg-slate-50/95 backdrop-blur-sm z-20 shadow-[1px_0_0_0_#e2e8f0]">
                                        Dewan Juri
                                    </th>
                                    @foreach($objects as $object)
                                        <th class="py-3.5 px-4 text-center min-w-[130px] max-w-[180px]">
                                            <div class="truncate font-semibold text-slate-800" title="{{ $object->name }}">
                                                {{ $object->name }}
                                            </div>
                                            @if($assessmentType === 'innovation' && $object->institution)
                                                <div class="text-[10px] text-slate-400 truncate mt-0.5">
                                                    {{ $object->institution->name }}
                                                </div>
                                            @endif
                                        </th>
                                    @endforeach
                                    <th class="py-3.5 px-6 text-center">
                                        Progress Juri
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($juries as $jury)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-4 px-6 font-semibold text-slate-900 sticky left-0 bg-white shadow-[1px_0_0_0_#e2e8f0] z-10 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <div class="size-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                                    {{ substr($jury->user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-900">{{ $jury->user->name }}</div>
                                                    <div class="text-[10px] text-slate-400">Juri Independen</div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        @php
                                            $juryProgress = 0;
                                            $juryTotal = $objects->count();
                                        @endphp
                                        
                                        @foreach($objects as $object)
                                            @php
                                                $assessmentData = $matrix[$jury->id][$object->id] ?? null;
                                                $status = $assessmentData['status'] ?? 'not_started';
                                                
                                                if(in_array($status, ['submitted', 'locked'])) {
                                                    $juryProgress++;
                                                }
                                            @endphp
                                            
                                            <td class="py-3 px-3 text-center align-middle whitespace-nowrap">
                                                <div class="inline-flex flex-col items-center gap-1.5">
                                                    <!-- Status Badge -->
                                                    @if($status === 'not_started')
                                                        <span class="text-xs text-slate-300 font-medium">—</span>
                                                    @elseif($status === 'draft')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                            <span class="size-1.5 rounded-full bg-amber-500"></span>
                                                            Draft
                                                        </span>
                                                    @elseif($status === 'submitted')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                            <span class="size-1.5 rounded-full bg-blue-500"></span>
                                                            Terkirim
                                                        </span>
                                                    @elseif($status === 'locked')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                            <span class="size-1.5 rounded-full bg-purple-500"></span>
                                                            Terkunci
                                                        </span>
                                                    @elseif($status === 'revision_requested')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                            <span class="size-1.5 rounded-full bg-rose-500"></span>
                                                            Revisi
                                                        </span>
                                                    @endif
                                                    
                                                    <!-- Action Buttons -->
                                                    @if($status !== 'not_started')
                                                        <div class="flex items-center gap-1 mt-0.5">
                                                            @if($status === 'submitted')
                                                                <form method="POST" action="{{ route('admin.assessments.lock', [$assessmentType, $assessmentData['id'] ?? $object->id]) }}" class="inline">
                                                                    @csrf
                                                                    <button type="submit" 
                                                                            onclick="return confirmSubmit(event, 'Penilaian terkunci menjadi dasar perhitungan ranking dan tidak dapat diubah juri.', { title: 'Kunci Penilaian', confirmText: 'Ya, Kunci', danger: false })"
                                                                            title="Kunci Penilaian"
                                                                            class="text-[10px] font-semibold px-2 py-0.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-md border border-purple-200 transition-colors">
                                                                        Kunci
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            
                                                            @if(in_array($status, ['submitted', 'locked']))
                                                                <button type="button" 
                                                                        onclick="showRevisionModal('{{ $assessmentType }}', '{{ $assessmentData['id'] ?? $object->id }}', '{{ addslashes($jury->user->name) }}', '{{ addslashes($object->name) }}')"
                                                                        title="Minta Revisi Juri"
                                                                        class="text-[10px] font-semibold px-2 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-md border border-rose-200 transition-colors">
                                                                    Revisi
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                        
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            @php
                                                $pct = $juryTotal > 0 ? round(($juryProgress / $juryTotal) * 100) : 0;
                                                $allDone = $juryProgress == $juryTotal;
                                            @endphp
                                            <div class="inline-flex flex-col items-center gap-1">
                                                <span class="text-xs font-bold tabular-nums {{ $allDone ? 'text-emerald-600' : 'text-slate-800' }}">
                                                    {{ $juryProgress }}/{{ $juryTotal }} ({{ $pct }}%)
                                                </span>
                                                <div class="w-20 bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200/50">
                                                    <div class="h-full rounded-full transition-all duration-300 {{ $allDone ? 'bg-emerald-500' : 'bg-blue-600' }}"
                                                         style="width: {{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="size-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Belum Ada Data Penilaian</h4>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $juries->count() === 0 ? 'Belum ada dewan juri yang memilih subkategori ini.' : 'Belum ada objek peserta terdaftar.' }}
                        </p>
                    </div>
                @endif
            </div>
        @else
            <!-- Filter Prompt State -->
            <div class="bg-white rounded-2xl p-8 md:p-12 border border-slate-200/80 shadow-xs text-center max-w-lg mx-auto">
                <div class="size-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4 ring-8 ring-blue-50/50">
                    <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Pilih Subkategori Penilaian</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Pilih subkategori di atas untuk melihat matriks pengisian nilai juri, mengunci nilai, atau meminta revisi.
                </p>
            </div>
        @endif
    </div>

    <!-- Open Scoring Confirmation Modal (Flowbite Pattern with Backdrop Blur) -->
    <div id="openScoringModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4 transition-opacity">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 md:p-8 shadow-2xl border border-slate-200 transform transition-all scale-100">
            <div class="size-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 ring-8 ring-amber-50/50">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Konfirmasi Buka Penilaian</h3>
            <div class="mt-2.5 text-xs text-slate-600 leading-relaxed space-y-2">
                <p class="font-bold text-amber-800 bg-amber-50 p-2.5 rounded-xl border border-amber-200">
                    Perhatian: Tindakan pembukaan penilaian bersifat permanen.
                </p>
                <ul class="list-disc list-inside space-y-1 text-slate-500">
                    <li>Roster juri penilai dan objek peserta akan <strong>dikunci permanen</strong>.</li>
                    <li>Juri tidak dapat menambah atau membatalkan subkategori ini.</li>
                    <li>Status akan berubah menjadi <strong>Dibuka</strong> dan siap menerima input nilai.</li>
                </ul>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeOpenScoringModal()" 
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <form id="openScoringForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-emerald-600/30 transition-all duration-150 active:scale-[0.96]">
                        Ya, Buka Penilaian Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Request Revision Modal (Flowbite Pattern) -->
    <div id="revisionModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4 transition-opacity">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 md:p-8 shadow-2xl border border-slate-200 transform transition-all scale-100">
            <div class="size-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 ring-8 ring-rose-50/50">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Minta Revisi Penilaian</h3>
            <p class="text-xs text-slate-500 mt-1" id="revisionInfo"></p>
            
            <form id="revisionForm" method="POST" class="mt-4">
                @csrf
                <div>
                    <label for="revision_note" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        Catatan Revisi untuk Juri <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="revision_note" id="revision_note" rows="4" required
                              class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-3 text-xs text-slate-800 shadow-xs focus:border-rose-500 focus:bg-white focus:ring-4 focus:ring-rose-500/10 transition-all placeholder:text-slate-400"
                              placeholder="Tuliskan catatan detail mengapa penilaian perlu diperbaiki oleh dewan juri..."></textarea>
                </div>
                
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRevisionModal()" 
                            class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-rose-600/30 transition-all duration-150 active:scale-[0.96]">
                        Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmOpenScoring(subcategoryId) {
            const modal = document.getElementById('openScoringModal');
            const form = document.getElementById('openScoringForm');
            form.action = `{{ route('admin.award-categories.open-scoring', ['awardCategory' => '__ID__']) }}`.replace('__ID__', subcategoryId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeOpenScoringModal() {
            const modal = document.getElementById('openScoringModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function showRevisionModal(type, id, juryName, objectName) {
            const modal = document.getElementById('revisionModal');
            const form = document.getElementById('revisionForm');
            const info = document.getElementById('revisionInfo');
            
            form.action = `{{ route('admin.assessments.request-revision', ['type' => '__TYPE__', 'id' => '__ID__']) }}`
                .replace('__TYPE__', type)
                .replace('__ID__', id);
            
            info.textContent = `Juri: ${juryName} • Objek: ${objectName}`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRevisionModal() {
            const modal = document.getElementById('revisionModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('revision_note').value = '';
        }

        // Close modals when clicking backdrop
        document.getElementById('openScoringModal').addEventListener('click', function(e) {
            if (e.target === this) closeOpenScoringModal();
        });

        document.getElementById('revisionModal').addEventListener('click', function(e) {
            if (e.target === this) closeRevisionModal();
        });
    </script>
    @endpush
</x-layouts.app>
