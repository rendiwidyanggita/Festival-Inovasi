<x-layouts.app>
    <x-slot name="header">
        Edit Subkategori: {{ $awardCategory->name }}
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.award-categories.update', [$festivalCategory, $awardCategory]) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Subkategori -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Subkategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $awardCategory->name) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Jumlah Pemenang -->
                    <div>
                        <label for="winner_count" class="block text-sm font-medium text-gray-700">Jumlah Pemenang <span class="text-red-500">*</span></label>
                        <input type="number" name="winner_count" id="winner_count" value="{{ old('winner_count', $awardCategory->winner_count) }}" min="1" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('winner_count') border-red-500 @enderror" 
                            required>
                        @error('winner_count')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Urutan -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $awardCategory->sort_order) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <!-- Status Scoring (read-only info) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Scoring</label>
                        <div class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $awardCategory->scoring_status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : 
                                  ($awardCategory->scoring_status->value === 'open' ? 'bg-green-100 text-green-800' : 
                                  ($awardCategory->scoring_status->value === 'closed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $awardCategory->scoring_status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($awardCategory->isRosterLocked())
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            Subkategori sudah dibuka. Relasi dan tipe penilaian tidak dapat diubah.
                        </p>
                    </div>
                @endif
                
                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('admin.award-categories.index', $festivalCategory) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
