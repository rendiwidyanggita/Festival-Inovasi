<x-layouts.app>
    <x-slot name="header">
        Tambah Juri ke Festival - {{ $currentFestival->name ?? 'Pilih Festival' }}
    </x-slot>

    @if($currentFestival)
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('admin.festival-juries.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Pilih Juri -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Pilih Juri <span class="text-red-500">*</span></label>
                            <select name="user_id" id="user_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('user_id') border-red-500 @enderror"
                                required>
                                <option value="">Pilih juri...</option>
                                @foreach($juries as $jury)
                                    <option value="{{ $jury->id }}" {{ old('user_id') == $jury->id ? 'selected' : '' }}>
                                        {{ $jury->name }} ({{ $jury->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Simpan
                        </button>
                        <a href="{{ route('admin.festival-juries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-center">
                <p class="text-gray-600">Pilih festival terlebih dahulu.</p>
                <a href="{{ route('admin.select-festival.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Pilih Festival
                </a>
            </div>
        </div>
    @endif
</x-layouts.app>
