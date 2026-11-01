@extends('layouts.app')

@section('title', 'Peringkat - ' . $awardCategory->name)

@section('content')
<div class="space-y-6">
    <!-- Header Card (Preline UI / Flowbite style) -->
    <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('admin.winners.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Daftar Pemenang
                </a>
                <span class="text-xs text-slate-400">•</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $isInnovation ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                    {{ $awardCategory->festivalCategory->name }}
                </span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kalkulasi Peringkat: {{ $awardCategory->name }}</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Peringkat dihitung otomatis <em>on-the-fly</em> dari rata-rata nilai penilaian juri yang sudah berstatus terkunci (locked). Pilih peserta dan tentukan posisi juaranya.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                <svg class="size-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                </svg>
                Kuota: {{ $awardCategory->winner_count }} Juara
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200/80 rounded-2xl p-4 flex items-center gap-3 text-xs text-emerald-800">
            <svg class="size-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200/80 rounded-2xl p-4 text-xs text-rose-800">
            <div class="font-bold mb-1">Terjadi kesalahan validasi:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top 3 Podium Visual Cards (Preline UI Highlight) -->
    @if(count($rankings) >= 3)
        @php
            $r1 = $rankings[0] ?? null;
            $r2 = $rankings[1] ?? null;
            $r3 = $rankings[2] ?? null;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Rank 2: Silver -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden order-2 md:order-1 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        🥈 Peringkat 2
                    </span>
                    <span class="text-sm font-extrabold text-slate-800 tabular-nums">
                        {{ number_format($r2->average_score ?? 0, 2) }}
                    </span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 truncate" title="{{ $r2->name }}">{{ $r2->name }}</h4>
                    @if($isInnovation && !empty($r2->innovator_name))
                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $r2->innovator_name }} • {{ $r2->institution_name ?? '' }}</p>
                    @endif
                </div>
            </div>

            <!-- Rank 1: Gold (Elevated Center) -->
            <div class="bg-gradient-to-b from-amber-50/50 to-white rounded-2xl p-6 border-2 border-amber-300 shadow-md relative overflow-hidden order-1 md:order-2 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-400 text-amber-950 shadow-xs">
                        🥇 Peringkat 1 (Tertinggi)
                    </span>
                    <span class="text-base font-black text-amber-900 tabular-nums">
                        {{ number_format($r1->average_score ?? 0, 2) }}
                    </span>
                </div>
                <div>
                    <h4 class="text-base font-extrabold text-slate-900 truncate" title="{{ $r1->name }}">{{ $r1->name }}</h4>
                    @if($isInnovation && !empty($r1->innovator_name))
                        <p class="text-xs text-slate-600 truncate mt-0.5 font-medium">{{ $r1->innovator_name }} • {{ $r1->institution_name ?? '' }}</p>
                    @endif
                </div>
            </div>

            <!-- Rank 3: Bronze -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden order-3 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-800 border border-orange-200">
                        🥉 Peringkat 3
                    </span>
                    <span class="text-sm font-extrabold text-slate-800 tabular-nums">
                        {{ number_format($r3->average_score ?? 0, 2) }}
                    </span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 truncate" title="{{ $r3->name }}">{{ $r3->name }}</h4>
                    @if($isInnovation && !empty($r3->innovator_name))
                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $r3->innovator_name }} • {{ $r3->institution_name ?? '' }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Ranking Table & Assignment Form (Flowbite / Preline UI Table style) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 md:p-6 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tabel Penetapan Juara</h3>
                <p class="text-xs text-slate-500 mt-0.5">Centang peserta yang ditetapkan sebagai juara dan pilih posisi juaranya (1 s/d {{ $awardCategory->winner_count }}).</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="autoAssignBtn" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition-colors">
                    ⚡ Pasang Sesuai Ranking
                </button>
            </div>
        </div>

        <form action="{{ route('admin.award-categories.winners.store', $awardCategory) }}" method="POST" id="winnersForm">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 w-12 text-center">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 size-4">
                            </th>
                            <th class="py-3.5 px-4 text-center w-20">Rank</th>
                            <th class="py-3.5 px-6">{{ $isInnovation ? 'Nama Inovasi' : 'Nama Instansi' }}</th>
                            @if($isInnovation)
                                <th class="py-3.5 px-6">Inovator</th>
                                <th class="py-3.5 px-6">Instansi Pengusung</th>
                            @endif
                            <th class="py-3.5 px-6 text-right">Rata-rata Skor</th>
                            <th class="py-3.5 px-6 text-center w-48">Penetapan Posisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($rankings as $ranking)
                            @php
                                $existingWinner = $existingWinners->get($ranking->id);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors {{ $existingWinner ? 'bg-amber-50/30' : '' }}">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" name="winners[{{ $loop->index }}][selected]" 
                                           value="1" 
                                           class="winner-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 size-4"
                                           @if($existingWinner) checked @endif
                                           data-id="{{ $ranking->id }}"
                                           data-rank="{{ $ranking->rank }}"
                                           data-type="{{ $isInnovation ? 'innovation' : 'institution' }}">
                                    <input type="hidden" name="winners[{{ $loop->index }}][target_type]" 
                                           value="{{ $isInnovation ? 'innovation' : 'institution' }}">
                                    <input type="hidden" name="winners[{{ $loop->index }}][target_id]" 
                                           value="{{ $ranking->id }}">
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($ranking->rank === 1)
                                        <span class="inline-flex items-center justify-center size-7 rounded-lg bg-amber-400 text-amber-950 font-black text-xs shadow-xs">
                                            1
                                        </span>
                                    @elseif($ranking->rank === 2)
                                        <span class="inline-flex items-center justify-center size-7 rounded-lg bg-slate-200 text-slate-800 font-bold text-xs">
                                            2
                                        </span>
                                    @elseif($ranking->rank === 3)
                                        <span class="inline-flex items-center justify-center size-7 rounded-lg bg-orange-200 text-orange-900 font-bold text-xs">
                                            3
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center size-7 rounded-lg bg-slate-100 text-slate-600 font-semibold text-xs tabular-nums">
                                            {{ $ranking->rank }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-900">
                                    {{ $ranking->name }}
                                </td>
                                @if($isInnovation)
                                    <td class="py-4 px-6 text-slate-600 text-xs">
                                        {{ $ranking->innovator_name ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-600 text-xs">
                                        {{ $ranking->institution_name ?? '-' }}
                                    </td>
                                @endif
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 tabular-nums">
                                        {{ number_format($ranking->average_score, 2) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <select name="winners[{{ $loop->index }}][position]" 
                                            class="position-select block w-full rounded-xl border-slate-200 bg-slate-50/50 py-1.5 px-3 text-xs font-semibold text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 @if(!$existingWinner) hidden @endif"
                                            data-selected="{{ $existingWinner->position ?? '' }}">
                                        <option value="">-- Pilih Posisi --</option>
                                        @for($i = 1; $i <= $awardCategory->winner_count; $i++)
                                            <option value="{{ $i }}" @if($existingWinner && $existingWinner->position == $i) selected @endif>
                                                🏆 Juara {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isInnovation ? 7 : 5 }}" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada peserta terdaftar dalam subkategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-5 md:p-6 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="text-xs text-slate-500 leading-relaxed">
                    Pastikan jumlah pemenang yang dipilih tidak melebihi kuota <strong>{{ $awardCategory->winner_count }} pemenang</strong> dan setiap posisi juara bersifat unik.
                </div>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-blue-600/30 transition-all duration-150 active:scale-[0.96]">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Penetapan Pemenang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.winner-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = e.target.checked;
            togglePositionSelect(cb);
        });
    });

    document.querySelectorAll('.winner-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            togglePositionSelect(this);
        });
    });

    function togglePositionSelect(checkbox) {
        const row = checkbox.closest('tr');
        const positionSelect = row.querySelector('.position-select');
        
        if (checkbox.checked) {
            positionSelect.classList.remove('hidden');
        } else {
            positionSelect.classList.add('hidden');
            positionSelect.value = '';
        }
    }

    // Auto assign button helper
    const autoBtn = document.getElementById('autoAssignBtn');
    if (autoBtn) {
        autoBtn.addEventListener('click', function() {
            const maxWinners = {{ $awardCategory->winner_count }};
            const checkboxes = Array.from(document.querySelectorAll('.winner-checkbox'));
            
            checkboxes.forEach((cb, idx) => {
                const rank = parseInt(cb.dataset.rank, 10);
                const row = cb.closest('tr');
                const positionSelect = row.querySelector('.position-select');
                
                if (rank <= maxWinners) {
                    cb.checked = true;
                    positionSelect.classList.remove('hidden');
                    positionSelect.value = rank;
                } else {
                    cb.checked = false;
                    positionSelect.classList.add('hidden');
                    positionSelect.value = '';
                }
            });
        });
    }

    // Initialize on page load
    document.querySelectorAll('.winner-checkbox:checked').forEach(cb => {
        togglePositionSelect(cb);
    });
</script>
@endpush
@endsection
