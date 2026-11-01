<x-layouts.app>
    <x-slot name="header">
        Edit Festival: {{ $festival->name }}
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.festivals.update', $festival) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tahun -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" name="year" id="year" value="{{ old('year', $festival->year) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('year') border-red-500 @enderror" 
                            required>
                        @error('year')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Nama Festival -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Festival <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $festival->name) }}" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="preparation" {{ old('status', $festival->status->value) === 'preparation' ? 'selected' : '' }}>Persiapan</option>
                            <option value="active" {{ old('status', $festival->status->value) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="archived" {{ old('status', $festival->status->value) === 'archived' ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>
                </div>
                
                <!-- Festival Settings Section -->
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Festival</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Penyelenggara -->
                        <div>
                            <label for="organizer_name" class="block text-sm font-medium text-gray-700">Nama Penyelenggara</label>
                            <input type="text" name="organizer_name" id="organizer_name" 
                                value="{{ old('organizer_name', $festival->settings?->organizer_name) }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <!-- Logo Penyelenggara -->
                        <div>
                            <label for="organizer_logo_path" class="block text-sm font-medium text-gray-700">Path Logo Penyelenggara</label>
                            <input type="text" name="organizer_logo_path" id="organizer_logo_path" 
                                value="{{ old('organizer_logo_path', $festival->settings?->organizer_logo_path) }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <!-- Judul Laporan -->
                        <div class="md:col-span-2">
                            <label for="report_title" class="block text-sm font-medium text-gray-700">Judul Laporan</label>
                            <input type="text" name="report_title" id="report_title" 
                                value="{{ old('report_title', $festival->settings?->report_title) }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <!-- Footer Laporan -->
                        <div class="md:col-span-2">
                            <label for="report_footer" class="block text-sm font-medium text-gray-700">Footer Laporan</label>
                            <textarea name="report_footer" id="report_footer" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('report_footer', $festival->settings?->report_footer) }}</textarea>
                        </div>
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
