<x-layouts.app>
    <x-slot name="header">
        Edit Instansi: {{ $institution->name }}
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.institutions.update', $institution) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Subkategori -->
                    <div>
                        <label for="award_category_id" class="block text-sm font-medium text-gray-700">Subkategori</label>
                        @if($institution->awardCategory->isRosterLocked())
                            <div class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 px-3 py-2 text-gray-500">
                                {{ $institution->awardCategory->festivalCategory->name }} - {{ $institution->awardCategory->name }}
                            </div>
                            <p class="mt-2 text-sm text-yellow-600">Subkategori sudah dibuka, tidak dapat diubah.</p>
                        @else
                            <select name="award_category_id" id="award_category_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                                @foreach($awardCategories as $awardCategory)
                                    <option value="{{ $awardCategory->id }}" {{ old('award_category_id', $institution->award_category_id) == $awardCategory->id ? 'selected' : '' }}>
                                        {{ $awardCategory->festivalCategory->name }} - {{ $awardCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    
                    <!-- Nama Instansi -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Instansi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $institution->name) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                @if($institution->awardCategory->isRosterLocked())
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            Subkategori sudah dibuka. Hanya nama instansi yang dapat diubah. Perubahan akan dicatat di audit log.
                        </p>
                    </div>
                @endif
                
                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('admin.institutions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
