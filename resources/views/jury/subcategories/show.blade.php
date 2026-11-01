<x-layouts.app>
    <x-slot name="header">
        {{ $awardCategory->name }}
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('jury.dashboard') }}" class="text-blue-600 hover:text-blue-900">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- Subcategory Info Card -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $awardCategory->festivalCategory->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jenis Penilaian</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $awardCategory->festivalCategory->assessment_type->label() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jumlah Pemenang</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $awardCategory->winner_count }}</dd>
                </div>
            </div>
            
            <div class="flex items-center justify-between pt-4 border-t">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $awardCategory->scoring_status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : 
                          ($awardCategory->scoring_status->value === 'open' ? 'bg-green-100 text-green-800' : 
                          'bg-blue-100 text-blue-800') }}">
                        {{ $awardCategory->scoring_status->label() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Progress Penilaian</h3>
                <span class="text-sm text-gray-600">
                    {{ $assessedCount }}/{{ $totalObjects }} objek dinilai
                </span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                <div class="h-4 rounded-full transition-all duration-500 
                    {{ $progressPercentage >= 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
            
            <p class="text-xs text-gray-500">
                @if($progressPercentage >= 100)
                    <span class="text-green-600 font-medium">✓ Semua objek sudah dinilai</span>
                @else
                    {{ 100 - $progressPercentage }}% tersisa
                @endif
            </p>
        </div>
    </div>

    <!-- Objects List -->
    @if($objects->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Daftar {{ $assessmentType === 'innovation' ? 'Inovasi' : 'Instansi' }}
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $assessmentType === 'innovation' ? 'Nama Inovasi' : 'Nama Instansi' }}
                                </th>
                                @if($assessmentType === 'innovation')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Inovator
                                    </th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Penilaian
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($objects as $index => $object)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $object->name }}
                                        </div>
                                    </td>
                                    @if($assessmentType === 'innovation')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $object->innovator_name ?? '-' }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $assessmentStatus = $object->assessment_status ?? 'not_started';
                                        @endphp
                                        
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            {{ $assessmentStatus === 'not_started' ? 'bg-gray-100 text-gray-800' : 
                                              ($assessmentStatus === 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                              ($assessmentStatus === 'submitted' ? 'bg-blue-100 text-blue-800' : 
                                              ($assessmentStatus === 'locked' ? 'bg-purple-100 text-purple-800' : 
                                              'bg-red-100 text-red-800'))) }}">
                                            @if($assessmentStatus === 'not_started')
                                                Belum Dinilai
                                            @elseif($assessmentStatus === 'draft')
                                                Draft
                                            @elseif($assessmentStatus === 'submitted')
                                                Terkirim
                                            @elseif($assessmentStatus === 'locked')
                                                Terkunci
                                            @elseif($assessmentStatus === 'revision_requested')
                                                Perlu Revisi
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($awardCategory->scoring_status->value === 'open')
                                            <a href="{{ route('jury.assessments.show', [$awardCategory, $assessmentType, $object->id]) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                                                @if($assessmentStatus === 'not_started')
                                                    Mulai Penilaian
                                                @elseif($assessmentStatus === 'revision_requested')
                                                    Revisi Penilaian
                                                @else
                                                    Lihat/Edit
                                                @endif
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs">
                                                {{ $awardCategory->scoring_status->value === 'preparation' ? 'Belum dibuka' : 'Sudah ditutup' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada {{ $assessmentType === 'innovation' ? 'inovasi' : 'instansi' }}</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada peserta pada subkategori ini.</p>
            </div>
        </div>
    @endif
</x-layouts.app>
