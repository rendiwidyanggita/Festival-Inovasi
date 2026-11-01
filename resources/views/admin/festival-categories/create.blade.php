<x-layouts.app>
    <x-slot name="header">
        Tambah Kategori Utama
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.festival-categories.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kategori -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Tipe Penilaian -->
                    <div>
                        <label for="assessment_type" class="block text-sm font-medium text-gray-700">Tipe Penilaian <span class="text-red-500">*</span></label>
                        <select name="assessment_type" id="assessment_type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('assessment_type') border-red-500 @enderror"
                            required>
                            <option value="">Pilih tipe...</option>
                            <option value="innovation" {{ old('assessment_type') === 'innovation' ? 'selected' : '' }}>Inovasi</option>
                            <option value="institution" {{ old('assessment_type') === 'institution' ? 'selected' : '' }}>Instansi</option>
                        </select>
                        @error('assessment_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Urutan -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Urutan</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('admin.festival-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
