<x-layouts.app>
    <x-slot name="header">
        Subkategori - {{ $festivalCategory->name }}
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.festival-categories.index') }}" class="text-blue-600 hover:text-blue-900">
            ← Kembali ke Kategori
        </a>
        <a href="{{ route('admin.award-categories.create', $festivalCategory) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            Tambah Subkategori
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            @if($awardCategories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Subkategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pemenang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Scoring</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($awardCategories as $awardCategory)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $awardCategory->sort_order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $awardCategory->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $awardCategory->winner_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $awardCategory->scoring_status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : 
                                              ($awardCategory->scoring_status->value === 'open' ? 'bg-green-100 text-green-800' : 
                                              ($awardCategory->scoring_status->value === 'closed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $awardCategory->scoring_status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $festivalCategory->assessment_type->value === 'innovation' ? $awardCategory->innovations()->count() : $awardCategory->institutions()->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.award-categories.edit', [$festivalCategory, $awardCategory]) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        @if($awardCategory->scoring_status->value === 'preparation')
                                            <form method="POST" action="{{ route('admin.award-categories.destroy', [$festivalCategory, $awardCategory]) }}" class="inline" onsubmit="return confirm('Hapus subkategori ini?')">
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
                    {{ $awardCategories->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada subkategori</h3>
                    <p class="mt-1 text-sm text-gray-500">Buat subkategori untuk kategori ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
