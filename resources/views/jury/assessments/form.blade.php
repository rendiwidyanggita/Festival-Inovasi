<x-layouts.app>
    <x-slot name="header">
        Form Evaluasi Dewan Juri
    </x-slot>

    <div class="space-y-6">
        <!-- Navigation Back -->
        <div class="flex items-center justify-between">
            <a href="{{ route('jury.subcategories.show', $awardCategory) }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Peserta {{ $awardCategory->name }}
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                Rubrik Skala 6 s/d 10
            </span>
        </div>

        <!-- Revision Notice Banner (Flowbite / Preline Banner) -->
        @if($assessment && $assessment->status->value === 'revision_requested')
            <div class="relative overflow-hidden bg-rose-50 border border-rose-200/80 rounded-2xl p-5 shadow-xs">
                <div class="flex items-start gap-3.5">
                    <div class="size-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-rose-900">Permintaan Revisi dari Superadmin</h3>
                        <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                            Penilaian ini dikembalikan untuk disesuaikan kembali. Silakan periksa catatan berikut:
                        </p>
                        <div class="mt-2.5 bg-white/80 border border-rose-200 rounded-xl p-3 text-xs text-rose-900 font-medium leading-relaxed">
                            "{{ $assessment->revision_note }}"
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Locked Notice Banner -->
        @if($assessment && $assessment->status->value === 'locked')
            <div class="bg-purple-50 border border-purple-200/80 rounded-2xl p-5 shadow-xs flex items-center gap-3.5">
                <div class="size-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-purple-900">Penilaian Telah Dikunci Permanen</h3>
                    <p class="text-xs text-purple-700 mt-0.5">
                        Nilai telah diverifikasi oleh Superadmin dan sudah tidak dapat diubah kembali.
                    </p>
                </div>
            </div>
        @endif

        <!-- Participant Information Card (Preline UI Hero Card) -->
        <div class="bg-white rounded-2xl p-6 md:p-7 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $assessmentType === 'innovation' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                            {{ $awardCategory->festivalCategory->name }}
                        </span>
                        <span class="text-xs text-slate-400">•</span>
                        <span class="text-xs font-semibold text-slate-600">
                            {{ $awardCategory->name }}
                        </span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">
                        {{ $object->name }}
                    </h1>
                    @if($assessmentType === 'innovation')
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-600">
                            <span class="inline-flex items-center gap-1 font-semibold text-slate-900">
                                <svg class="size-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Inovator: {{ $object->innovator_name ?? '-' }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="inline-flex items-center gap-1 text-slate-600">
                                <svg class="size-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Instansi: {{ $object->institution->name ?? '-' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                    @if($assessment)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                            {{ $assessment->status->value === 'draft' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                              ($assessment->status->value === 'submitted' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                              ($assessment->status->value === 'locked' ? 'bg-purple-50 text-purple-700 border border-purple-200' :
                              'bg-rose-50 text-rose-700 border border-rose-200')) }}">
                            <span class="size-1.5 rounded-full {{ $assessment->status->value === 'draft' ? 'bg-amber-500' : ($assessment->status->value === 'submitted' ? 'bg-blue-500' : ($assessment->status->value === 'locked' ? 'bg-purple-500' : 'bg-rose-500')) }}"></span>
                            Status Penilaian: {{ $assessment->status->label() }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <span class="size-1.5 rounded-full bg-slate-400"></span>
                            Belum Dinilai
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assessment Form -->
        <form id="assessmentForm" method="POST" action="{{ (!$assessment || $assessment->status->value !== 'locked') ? route('jury.assessments.submit', [$awardCategory, $assessmentType, $object->id]) : '#' }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Criteria Section (8 Cols) -->
                <div class="lg:col-span-8 space-y-4">
                    @php
                        $isLocked = $assessment && $assessment->status->value === 'locked';
                        $existingScores = [];
                        if($assessment) {
                            foreach($assessment->details as $detail) {
                                $existingScores[$detail->assessment_criterion_id] = $detail->score;
                            }
                        }
                    @endphp

                    @foreach($criteria as $criterion)
                        <div class="bg-white rounded-2xl p-5 md:p-6 border border-slate-200/80 shadow-xs hover:border-blue-200 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                                <div>
                                    <h3 class="text-sm md:text-base font-bold text-slate-900">{{ $criterion->name }}</h3>
                                    @if(!empty($criterion->description))
                                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $criterion->description }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shrink-0 self-start sm:self-auto tabular-nums">
                                    Bobot: {{ $criterion->weight }}%
                                </span>
                            </div>

                            <!-- Interactive Score Selector Buttons (Preline UI / DaisyUI Radio Pills) -->
                            <div class="mt-4">
                                <span class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Pilih Nilai (6 - 10):</span>
                                <div class="grid grid-cols-5 gap-2.5 sm:gap-3">
                                    @foreach([6, 7, 8, 9, 10] as $score)
                                        @php
                                            $isSelected = isset($existingScores[$criterion->id]) && $existingScores[$criterion->id] == $score;
                                        @endphp
                                        <label class="relative cursor-pointer block select-none">
                                            <input type="radio" 
                                                   name="criteria[{{ $criterion->id }}]" 
                                                   value="{{ $score }}"
                                                   data-criterion="{{ $criterion->id }}"
                                                   data-weight="{{ $criterion->weight }}"
                                                   class="sr-only peer"
                                                   {{ $isLocked ? 'disabled' : '' }}
                                                   {{ $isSelected ? 'checked' : '' }}>
                                            <div class="h-12 rounded-xl border flex flex-col items-center justify-center transition-all duration-150 active:scale-[0.96]
                                                {{ $isLocked 
                                                    ? ($isSelected ? 'border-purple-600 bg-purple-50 text-purple-900 font-black' : 'border-slate-200 bg-slate-50 text-slate-400 cursor-not-allowed')
                                                    : 'border-slate-200 bg-slate-50/70 text-slate-700 hover:bg-blue-50 hover:border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-blue-600/30' }}">
                                                <span class="text-sm font-extrabold tabular-nums">{{ $score }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Sticky Summary Calculator (4 Cols) (Preline UI Stats Card Pattern) -->
                <div class="lg:col-span-4 sticky top-6 space-y-4">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900">Kalkulasi Tertimbang</h3>
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Live Preview</span>
                        </div>

                        <!-- Big Weighted Score Display -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50/60 rounded-2xl p-5 border border-blue-100 text-center mb-4">
                            <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Total Skor Akhir</span>
                            <div class="mt-1 text-4xl font-black text-blue-900 tracking-tight tabular-nums" id="weightedScore">
                                {{ number_format($assessment->weighted_score ?? 0, 2) }}
                            </div>
                            <span class="inline-block mt-1 text-[11px] text-blue-600/80 font-medium">
                                Rentang Skala 6.00 – 10.00
                            </span>
                        </div>

                        <!-- Criteria Breakdown List -->
                        <div class="space-y-2 mb-6 text-xs">
                            @foreach($criteria as $criterion)
                                <div class="flex items-center justify-between py-1 border-b border-slate-50 last:border-0">
                                    <span class="text-slate-600 truncate mr-2" title="{{ $criterion->name }}">{{ $criterion->name }} ({{ $criterion->weight }}%)</span>
                                    <span class="font-extrabold text-slate-900 tabular-nums px-2 py-0.5 rounded-md bg-slate-100" id="score_{{ $criterion->id }}">
                                        {{ $existingScores[$criterion->id] ?? '—' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if(!$isLocked)
                            <!-- Action Buttons -->
                            <div class="space-y-2.5 pt-2 border-t border-slate-100">
                                <button type="button" 
                                        onclick="saveDraft()"
                                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-all duration-150 active:scale-[0.96]">
                                    <svg class="size-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                    Simpan sebagai Draft
                                </button>
                                
                                <button type="submit" 
                                        id="submitBtn"
                                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-blue-600/30 transition-all duration-150 active:scale-[0.96]">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Kirim Penilaian Final
                                </button>
                            </div>
                        @else
                            <div class="text-center py-2 text-xs text-purple-700 font-semibold bg-purple-50 rounded-xl border border-purple-100">
                                Penilaian Telah Dikunci
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
<script>
    // Live weighted score calculator
    const criteria = @json($criteria->mapWithKeys(function($c) {
        return [$c->id => ['weight' => $c->weight, 'name' => $c->name]];
    }));

    function calculateWeightedScore() {
        let totalWeighted = 0;
        let allFilled = true;

        Object.keys(criteria).forEach(criterionId => {
            const selected = document.querySelector(`input[name="criteria[${criterionId}]"]:checked`);
            const scoreElement = document.getElementById(`score_${criterionId}`);
            
            if (selected) {
                const score = parseInt(selected.value);
                const weight = criteria[criterionId].weight;
                totalWeighted += score * weight;
                if (scoreElement) scoreElement.textContent = score;
            } else {
                if (scoreElement) scoreElement.textContent = '—';
                allFilled = false;
            }
        });

        const finalScore = (totalWeighted / 100).toFixed(2);
        const weightedElem = document.getElementById('weightedScore');
        if (weightedElem) weightedElem.textContent = finalScore;
        
        return allFilled;
    }

    // Attach event listeners to all radio buttons
    document.querySelectorAll('input[type="radio"][name^="criteria"]').forEach(radio => {
        radio.addEventListener('change', calculateWeightedScore);
    });

    // Hitung sekali saat halaman dimuat agar nilai tersimpan (termasuk form
    // yang sudah terkirim/terkunci) langsung tampil di live preview
    calculateWeightedScore();

    // Save draft function
    function saveDraft() {
        const form = document.getElementById('assessmentForm');
        const draftUrl = '{{ route('jury.assessments.draft', [$awardCategory, $assessmentType, $object->id]) }}';
        
        form.action = draftUrl;
        
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'is_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        
        form.submit();
    }

    // Form validation before final submit — memakai modal global, bukan alert()/confirm() native
    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        if (!this.action.includes('/submit')) {
            return; // form terkunci (action '#') — biarkan
        }

        // Kumpulkan kriteria yang belum dinilai agar pesan modal spesifik
        const missing = Object.keys(criteria).filter(criterionId => {
            return !document.querySelector(`input[name="criteria[${criterionId}]"]:checked`);
        }).map(criterionId => criteria[criterionId].name || ('Kriteria #' + criterionId));

        if (missing.length > 0) {
            e.preventDefault();
            appAlert({
                title: 'Penilaian Belum Lengkap',
                message: 'Harap isi nilai (6-10) untuk semua kriteria sebelum mengirim penilaian final. Belum diisi: ' + missing.join(', ') + '.',
            });
            return;
        }

        e.preventDefault();
        appConfirm({
            title: 'Kirim Penilaian Final',
            message: 'Apakah Anda yakin ingin mengirim penilaian final? Penilaian yang sudah dikirim tidak dapat diubah kecuali diminta revisi oleh superadmin.',
            confirmText: 'Ya, Kirim Final',
            danger: false,
        }).then((ok) => {
            if (ok) e.target.submit(); // submit() native tidak memicu handler ini lagi
        });
    });

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', calculateWeightedScore);
</script>
@endpush
</x-layouts.app>
