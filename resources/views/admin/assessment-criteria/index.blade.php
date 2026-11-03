<x-layouts.app>
    <x-slot name="header">
        Kriteria Penilaian - {{ $currentFestival->name ?? 'Pilih Festival' }}
    </x-slot>

    @if($currentFestival)
        <div class="mb-6">
            <a href="{{ route('admin.assessment-criteria.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Tambah Kriteria
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                @if($criteria->count() > 0)
                    @php
                        $innovationCriteria = $criteria->where('assessment_type', 'innovation');
                        $institutionCriteria = $criteria->where('assessment_type', 'institution');
                    @endphp
                    
                    @if($innovationCriteria->count() > 0)
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kriteria Inovasi</h3>
                        <div class="overflow-x-auto mb-8">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kriteria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bobot</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($innovationCriteria as $criterion)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->sort_order }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->weight }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.assessment-criteria.edit', $criterion) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                                @if($criterion->innovationAssessmentDetails()->count() === 0 && $criterion->institutionAssessmentDetails()->count() === 0)
                                                    <form method="POST" action="{{ route('admin.assessment-criteria.destroy', $criterion) }}" class="inline" onsubmit="return confirmSubmit(event, 'Kriteria yang dihapus tidak dapat dikembalikan.', { title: 'Hapus Kriteria' })">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-6 py-3 text-sm font-medium text-gray-900">Total Bobot</td>
                                        <td class="px-6 py-3 text-sm font-bold text-gray-900">{{ $innovationCriteria->sum('weight') }}%</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                    
                    @if($institutionCriteria->count() > 0)
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kriteria Instansi</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kriteria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bobot</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($institutionCriteria as $criterion)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->sort_order }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $criterion->weight }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.assessment-criteria.edit', $criterion) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                                @if($criterion->innovationAssessmentDetails()->count() === 0 && $criterion->institutionAssessmentDetails()->count() === 0)
                                                    <form method="POST" action="{{ route('admin.assessment-criteria.destroy', $criterion) }}" class="inline" onsubmit="return confirmSubmit(event, 'Kriteria yang dihapus tidak dapat dikembalikan.', { title: 'Hapus Kriteria' })">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-6 py-3 text-sm font-medium text-gray-900">Total Bobot</td>
                                        <td class="px-6 py-3 text-sm font-bold text-gray-900">{{ $institutionCriteria->sum('weight') }}%</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kriteria</h3>
                        <p class="mt-1 text-sm text-gray-500">Buat kriteria penilaian untuk festival ini.</p>
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
