<x-layouts.app>
    <x-slot name="header">
        Monitoring Penilaian
    </x-slot>

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="festival" class="block text-sm font-medium text-gray-700 mb-2">Festival</label>
                    <select name="festival" id="festival" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($festivals as $festival)
                            <option value="{{ $festival->id }}" {{ $selectedFestival == $festival->id ? 'selected' : '' }}>
                                {{ $festival->name }} ({{ $festival->year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-2">Subkategori</label>
                    <select name="subcategory" id="subcategory" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Subkategori</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ $selectedSubcategory == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->festivalCategory->name }} - {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedSubcategory)
        <!-- Subcategory Actions -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $subcategories->find($selectedSubcategory)->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $subcategories->find($selectedSubcategory)->festivalCategory->name }} - 
                            Status: 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $subcategories->find($selectedSubcategory)->scoring_status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : 
                                  ($subcategories->find($selectedSubcategory)->scoring_status->value === 'open' ? 'bg-green-100 text-green-800' : 
                                  'bg-blue-100 text-blue-800') }}">
                                {{ $subcategories->find($selectedSubcategory)->scoring_status->label() }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        @if($subcategories->find($selectedSubcategory)->scoring_status->value === 'preparation')
                            <button type="button" 
                                onclick="confirmOpenScoring('{{ $selectedSubcategory }}')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                Buka Penilaian
                            </button>
                        @elseif($subcategories->find($selectedSubcategory)->scoring_status->value === 'open')
                            <form method="POST" action="{{ route('admin.award-categories.close-scoring', $selectedSubcategory) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                    onclick="return confirm('Tutup penilaian untuk subkategori ini? Tindakan ini tidak dapat dibatalkan.')"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Tutup Penilaian
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitoring Matrix -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Matriks Progress Penilaian</h3>
                
                @if($juries->count() > 0 && $objects->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 sticky left-0 z-10">
                                        Juri
                                    </th>
                                    @foreach($objects as $object)
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 whitespace-nowrap">
                                            <div class="max-w-32 truncate" title="{{ $object->name }}">
                                                {{ $object->name }}
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                        Progress
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($juries as $jury)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-white sticky left-0 z-10">
                                            {{ $jury->user->name }}
                                        </td>
                                        
                                        @php
                                            $juryProgress = 0;
                                            $juryTotal = $objects->count();
                                        @endphp
                                        
                                        @foreach($objects as $object)
                                            @php
                                                $assessmentData = $matrix[$jury->id][$object->id] ?? null;
                                                $status = $assessmentData['status'] ?? 'not_started';
                                                $canLock = $status === 'submitted';
                                                $canRequestRevision = in_array($status, ['submitted', 'locked']);
                                                
                                                if(in_array($status, ['submitted', 'locked'])) {
                                                    $juryProgress++;
                                                }
                                            @endphp
                                            
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <div class="flex flex-col items-center gap-2">
                                                    <!-- Status Badge -->
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full inline-block min-w-20
                                                        {{ $status === 'not_started' ? 'bg-gray-100 text-gray-800' : 
                                                          ($status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                                          ($status === 'submitted' ? 'bg-blue-100 text-blue-800' : 
                                                          ($status === 'locked' ? 'bg-purple-100 text-purple-800' : 
                                                          'bg-red-100 text-red-800'))) }}">
                                                        @if($status === 'not_started')
                                                            -
                                                        @elseif($status === 'draft')
                                                            Draft
                                                        @elseif($status === 'submitted')
                                                            Terkirim
                                                        @elseif($status === 'locked')
                                                            Terkunci
                                                        @elseif($status === 'revision_requested')
                                                            Revisi
                                                        @endif
                                                    </span>
                                                    
                                                    <!-- Action Buttons -->
                                                    @if($status !== 'not_started')
                                                        <div class="flex gap-1">
                                                            @if($status === 'submitted')
                                                                <form method="POST" action="{{ route('admin.assessments.lock', [$assessmentType, $assessmentData['id'] ?? $object->id]) }}" class="inline">
                                                                    @csrf
                                                                    <button type="submit" 
                                                                        onclick="return confirm('Kunci penilaian ini?')"
                                                                        class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200">
                                                                        Kunci
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            
                                                            @if(in_array($status, ['submitted', 'locked']))
                                                                <button type="button" 
                                                                    onclick="showRevisionModal('{{ $assessmentType }}', '{{ $assessmentData['id'] ?? $object->id }}', '{{ $jury->user->name }}', '{{ $object->name }}')"
                                                                    class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">
                                                                    Revisi
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                        
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium {{ $juryProgress == $juryTotal ? 'text-green-600' : 'text-gray-900' }}">
                                                {{ $juryProgress }}/{{ $juryTotal }}
                                            </div>
                                            <div class="w-16 h-2 bg-gray-200 rounded-full mt-1 mx-auto">
                                                <div class="h-2 rounded-full {{ $juryProgress == $juryTotal ? 'bg-green-500' : 'bg-blue-500' }}" 
                                                    style="width: {{ $juryTotal > 0 ? ($juryProgress / $juryTotal * 100) : 0 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $juries->count() === 0 ? 'Belum ada juri yang terdaftar pada subkategori ini.' : 'Belum ada peserta pada subkategori ini.' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Pilih Subkategori</h3>
                <p class="mt-1 text-sm text-gray-500">Gunakan filter di atas untuk memilih subkategori yang ingin dimonitor.</p>
            </div>
        </div>
    @endif
</x-layouts.app>

<!-- Open Scoring Confirmation Modal -->
<div id="openScoringModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Buka Penilaian</h3>
                <div class="mt-2 text-sm text-gray-500">
                    <p class="font-semibold text-red-600 mb-2">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Roster juri dan peserta akan dikunci permanen</li>
                        <li>Juri tidak dapat menambah/menghapus subkategori</li>
                        <li>Tindakan ini tidak dapat dibatalkan</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-5 flex gap-3 justify-end">
            <button type="button" onclick="closeOpenScoringModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </button>
            <form id="openScoringForm" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                    Ya, Buka Penilaian
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Request Revision Modal -->
<div id="revisionModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Minta Revisi Penilaian</h3>
            <p class="text-sm text-gray-500 mt-1" id="revisionInfo"></p>
        </div>
        
        <form id="revisionForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="revision_note" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Revisi <span class="text-red-500">*</span>
                </label>
                <textarea name="revision_note" id="revision_note" rows="4" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Jelaskan alasan mengapa penilaian perlu direvisi..."></textarea>
            </div>
            
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeRevisionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
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
        
        info.textContent = `Juri: ${juryName} | Objek: ${objectName}`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRevisionModal() {
        const modal = document.getElementById('revisionModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('revision_note').value = '';
    }

    // Close modals when clicking outside
    document.getElementById('openScoringModal').addEventListener('click', function(e) {
        if (e.target === this) closeOpenScoringModal();
    });

    document.getElementById('revisionModal').addEventListener('click', function(e) {
        if (e.target === this) closeRevisionModal();
    });
</script>
@endpush
