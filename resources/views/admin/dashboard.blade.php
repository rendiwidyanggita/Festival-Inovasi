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
        
        <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.winners.index') }}" class="flex items-center p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 bg-yellow-400 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Penetapan Pemenang</p>
                            <p class="text-sm text-gray-600">Kelola pemenang festival</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-lg border border-red-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Laporan PDF</p>
                            <p class="text-sm text-gray-600">Unduh laporan hasil</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.scoring.monitoring') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Monitoring Penilaian</p>
                            <p class="text-sm text-gray-600">Pantau progress juri</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.festivals.index') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200 hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Kelola Festival</p>
                            <p class="text-sm text-gray-600">Atur data festival</p>
                        </div>
                    </a>
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
