<x-layouts.app>
    <x-slot name="header">
        Tambah Festival Baru
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.festivals.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Tahun -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" name="year" id="year" value="{{ old('year', date('Y')) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('year') border-red-500 @enderror" 
                            required>
                        @error('year')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Nama Festival -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Festival <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Nama Penyelenggara -->
                    <div>
                        <label for="organizer_name" class="block text-sm font-medium text-gray-700">Nama Penyelenggara</label>
                        <input type="text" name="organizer_name" id="organizer_name" value="{{ old('organizer_name') }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Simpan
                    </button>
                    <a href="{{ route('admin.festivals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
