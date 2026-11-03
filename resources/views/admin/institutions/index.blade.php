<x-layouts.app>
    <x-slot name="header">
        Daftar Instansi - {{ $currentFestival->name ?? 'Pilih Festival' }}
    </x-slot>

    @if($currentFestival)
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('admin.institutions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Tambah Instansi
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                @if($institutions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Instansi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subkategori</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($institutions as $institution)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $institution->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $institution->awardCategory->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.institutions.edit', $institution) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            @if(!$institution->awardCategory?->isRosterLocked())
                                                <form method="POST" action="{{ route('admin.institutions.destroy', $institution) }}" class="inline" onsubmit="return confirmSubmit(event, 'Instansi yang dihapus tidak dapat dikembalikan.', { title: 'Hapus Instansi' })">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $institutions->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada instansi</h3>
                        <p class="mt-1 text-sm text-gray-500">Tambahkan instansi untuk festival ini.</p>
                    </div>
                @endif
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
