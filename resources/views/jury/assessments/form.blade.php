<x-layouts.app>
    <x-slot name="header">
        Form Penilaian
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('jury.subcategories.show', $awardCategory) }}" class="text-blue-600 hover:text-blue-900">
            ← Kembali ke Daftar {{ $assessmentType === 'innovation' ? 'Inovasi' : 'Instansi' }}
        </a>
    </div>

    <!-- Revision Banner -->
    @if($assessment && $assessment->status->value === 'revision_requested')
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Penilaian Perlu Direvisi</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p><strong>Catatan Superadmin:</strong></p>
                        <p class="mt-1">{{ $assessment->revision_note }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Locked Banner -->
    @if($assessment && $assessment->status->value === 'locked')
        <div class="bg-purple-50 border-l-4 border-purple-400 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-purple-800">Penilaian Terkunci</h3>
                    <p class="text-sm text-purple-700 mt-1">
                        Penilaian ini sudah dikunci dan tidak dapat diubah.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Object Details Card -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ $assessmentType === 'innovation' ? 'Nama Inovasi' : 'Nama Instansi' }}
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $object->name }}</dd>
                </div>
                @if($assessmentType === 'innovation')
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inovator</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $object->innovator_name ?? '-' }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $awardCategory->festivalCategory->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Subkategori</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $awardCategory->name }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Form -->
    <form id="assessmentForm" method="POST" action="{{ $assessment && $assessment->status->value !== 'locked' ? route('jury.assessments.submit', [$awardCategory, $assessmentType, $object->id]) : '#' }}">
        @csrf
    
    <!-- Criteria & Scoring -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Criteria Section -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kriteria Penilaian</h3>
                    
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
                        <div class="mb-6 last:mb-0 {{ !$loop->last ? 'pb-6 border-b border-gray-200' : '' }}">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-gray-900">
                                    {{ $criterion->name }}
                                </label>
                                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                    Bobot: {{ $criterion->weight }}%
                                </span>
                            </div>
                            
                            <div class="flex flex-wrap gap-2">
                                @foreach([6, 7, 8, 9, 10] as $score)
                                    @php
                                        $isSelected = isset($existingScores[$criterion->id]) && $existingScores[$criterion->id] == $score;
                                    @endphp
                                    
                                    <label class="score-button relative cursor-pointer">
                                        <input type="radio" 
                                            name="criteria[{{ $criterion->id }}]" 
                                            value="{{ $score }}"
                                            data-criterion="{{ $criterion->id }}"
                                            data-weight="{{ $criterion->weight }}"
                                            class="sr-only peer"
                                            {{ $isLocked ? 'disabled' : '' }}
                                            {{ $isSelected ? 'checked' : '' }}>
                                        <div class="w-12 h-12 flex items-center justify-center rounded-lg border-2 transition-all
                                            {{ $isLocked 
                                                ? 'border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed' 
                                                : 'border-gray-300 bg-white text-gray-700 hover:border-blue-400 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white' }}">
                                            <span class="text-sm font-semibold">{{ $score }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            @if($isLocked && isset($existingScores[$criterion->id]))
                                <p class="mt-2 text-xs text-gray-500">
                                    Nilai saat ini: <span class="font-semibold text-gray-900">{{ $existingScores[$criterion->id] }}</span>
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Calculator Section -->
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg sticky top-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Kalkulator Nilai</h3>
                    
                    <div class="space-y-4">
                        <!-- Criteria Scores -->
                        <div id="criteriaScores" class="space-y-2">
                            @foreach($criteria as $criterion)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $criterion->name }}</span>
                                    <span class="font-medium text-gray-900" id="score_{{ $criterion->id }}">
                                        {{ $existingScores[$criterion->id] ?? '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        
                        <hr class="border-gray-200">
                        
                        <!-- Weighted Score Calculation -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Nilai Tertimbang</div>
                            <div class="text-3xl font-bold text-blue-600" id="weightedScore">
                                {{ number_format($assessment->weighted_score ?? 0, 2) }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Rumus: Σ (nilai × bobot) / 100
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        @if(!$isLocked)
                            <div class="space-y-3 pt-4">
                                <button type="button" 
                                    onclick="saveDraft()"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                    Simpan sebagai Draft
                                </button>
                                
                                <button type="submit" 
                                    id="submitBtn"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Kirim Penilaian Final
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</x-layouts.app>

@push('scripts')
<script>
    // Live weighted score calculator
    const criteria = @json($criteria->mapWithKeys(function($c) {
        return [$c->id => ['weight' => $c->weight]];
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
                scoreElement.textContent = score;
            } else {
                scoreElement.textContent = '-';
                allFilled = false;
            }
        });

        const finalScore = (totalWeighted / 100).toFixed(2);
        document.getElementById('weightedScore').textContent = finalScore;
        
        return allFilled;
    }

    // Attach event listeners to all radio buttons
    document.querySelectorAll('input[type="radio"][name^="criteria"]').forEach(radio => {
        radio.addEventListener('change', calculateWeightedScore);
    });

    // Save draft function
    function saveDraft() {
        const form = document.getElementById('assessmentForm');
        const originalAction = form.action;
        const draftUrl = '{{ route('jury.assessments.draft', [$awardCategory, $assessmentType, $object->id]) }}';
        
        form.action = draftUrl;
        
        // Add draft flag
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'is_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        
        form.submit();
    }

    // Form validation before final submit
    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        if (this.action.includes('/submit')) {
            const allFilled = calculateWeightedScore();
            
            if (!allFilled) {
                e.preventDefault();
                alert('Harap isi semua kriteria penilaian sebelum mengirim penilaian final.');
            } else {
                if (!confirm('Apakah Anda yakin ingin mengirim penilaian final? Penilaian yang sudah dikirim tidak dapat diubah kecuali diminta revisi oleh superadmin.')) {
                    e.preventDefault();
                }
            }
        }
    });

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', calculateWeightedScore);
</script>
@endpush
