<x-layouts.app>
    <x-slot name="header">
        Dashboard Superadmin
    </x-slot>

    @if(session('selected_festival_id') && $currentFestival)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Festival: {{ $currentFestival->name }} ({{ $currentFestival->year }})
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-blue-600">Kategori Utama</div>
                        <div class="mt-1 text-3xl font-bold text-blue-900">{{ $currentFestival->festivalCategories()->count() }}</div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-green-600">Subkategori</div>
                        <div class="mt-1 text-3xl font-bold text-green-900">{{ $currentFestival->awardCategories()->count() }}</div>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-purple-600">Instansi</div>
                        <div class="mt-1 text-3xl font-bold text-purple-900">{{ $currentFestival->institutions()->count() }}</div>
                    </div>
                    
                    <div class="bg-orange-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-orange-600">Inovasi</div>
                        <div class="mt-1 text-3xl font-bold text-orange-900">{{ $currentFestival->innovations()->count() }}</div>
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-indigo-600">Juri Aktif</div>
                        <div class="mt-1 text-3xl font-bold text-indigo-900">{{ $currentFestival->festivalJuries()->where('status', 'active')->count() }}</div>
                    </div>
                    
                    <div class="bg-teal-50 rounded-lg p-4">
                        <div class="text-sm font-medium text-teal-600">Kriteria Penilaian</div>
                        <div class="mt-1 text-3xl font-bold text-teal-900">{{ $currentFestival->assessmentCriteria()->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Festival</h3>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 w-32">Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $currentFestival->status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : ($currentFestival->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $currentFestival->status->label() }}
                        </span>
                    </div>
                    @if($currentFestival->hasPublishedResults())
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 w-32">Hasil Diumumkan:</span>
                            <span class="text-sm text-gray-900">{{ $currentFestival->results_published_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada festival terpilih</h3>
                <p class="mt-1 text-sm text-gray-500">Pilih festival terlebih dahulu untuk melihat dashboard.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.select-festival.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Pilih Festival
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
