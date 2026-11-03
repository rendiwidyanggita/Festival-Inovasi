<x-layouts.app>
    <x-slot name="header">
        Pilih Subkategori Penilaian
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('jury.dashboard') }}" class="text-blue-600 hover:text-blue-900">
            ← Kembali ke Dashboard
        </a>
    </div>

    @if($festivals->count() > 0)
        <form method="POST" action="{{ route('jury.participations.store', $festivals->first()->id) }}">
            @csrf
            
            @foreach($festivals as $festival)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ $festival->name }} ({{ $festival->year }})
                        </h2>
                        
                        @php
                            $festivalCategories = $festival->categories()->with(['awardCategories' => function($query) {
                                $query->orderBy('sort_order');
                            }])->orderBy('sort_order')->get();
                        @endphp
                        
                        @if($festivalCategories->count() > 0)
                            @foreach($festivalCategories as $category)
                                <div class="mb-6 last:mb-0">
                                    <h3 class="text-md font-medium text-gray-800 mb-3 border-b pb-2">
                                        {{ $category->name }}
                                        <span class="text-sm text-gray-500 ml-2">
                                            ({{ $category->assessment_type->label() }})
                                        </span>
                                    </h3>
                                    
                                    @if($category->awardCategories->count() > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($category->awardCategories as $awardCategory)
                                                @php
                                                    $isAlreadySelected = $selectedSubcategories->contains($awardCategory->id);
                                                    $isLocked = $awardCategory->scoring_status->value !== 'preparation';
                                                    $canSelect = !$isLocked;
                                                @endphp
                                                
                                                <div class="relative flex items-start p-3 border rounded-lg 
                                                    {{ $isLocked ? 'bg-gray-50 border-gray-200' : 'bg-white border-blue-200 hover:border-blue-400' }}">
                                                    
                                                    <div class="flex items-center h-5">
                                                        @if($canSelect)
                                                            <input type="checkbox" 
                                                                name="subcategories[]" 
                                                                value="{{ $awardCategory->id }}"
                                                                id="subcategory_{{ $awardCategory->id }}"
                                                                {{ $isAlreadySelected ? 'checked' : '' }}
                                                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded cursor-pointer">
                                                        @else
                                                            {{-- Checkbox disabled tidak dikirim browser; hidden input
                                                                 menjaga subkategori terkunci tetap ada dalam seleksi
                                                                 agar tidak dianggap dilepas saat sinkronisasi --}}
                                                            @if($isAlreadySelected)
                                                                <input type="hidden" name="subcategories[]" value="{{ $awardCategory->id }}">
                                                            @endif
                                                            <input type="checkbox"
                                                                disabled
                                                                {{ $isAlreadySelected ? 'checked' : '' }}
                                                                class="h-4 w-4 text-gray-400 border-gray-300 rounded cursor-not-allowed">
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="ml-3 flex-1">
                                                        <label for="subcategory_{{ $awardCategory->id }}" 
                                                            class="{{ $canSelect ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                                            <span class="block text-sm font-medium text-gray-900">
                                                                {{ $awardCategory->name }}
                                                            </span>
                                                            <span class="block text-xs text-gray-500 mt-1">
                                                                {{ $awardCategory->winner_count }} Pemenang
                                                            </span>
                                                        </label>
                                                    </div>
                                                    
                                                    @if($isLocked)
                                                        <div class="ml-2">
                                                            @if($awardCategory->scoring_status->value === 'open')
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    Sudah Dibuka
                                                                </span>
                                                            @elseif($awardCategory->scoring_status->value === 'closed')
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    Sudah Ditutup
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic">Belum ada subkategori pada kategori ini.</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">Belum ada kategori pada festival ini.</p>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Pilih subkategori yang ingin Anda nilai. Subkategori yang sudah dibuka tidak dapat dipilih.
                    </p>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 transition-colors">
                        Simpan Pilihan
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada festival aktif</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum terdaftar pada festival aktif manapun.</p>
            </div>
        </div>
    @endif
</x-layouts.app>
