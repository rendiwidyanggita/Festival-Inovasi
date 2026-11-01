@extends('layouts.app')

@section('title', 'Laporan Hasil')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Hasil</h1>
            @if($festival)
                <p class="text-sm text-gray-500 mt-1">{{ $festival->name }} ({{ $festival->year }})</p>
            @endif
        </div>
    </div>

    @if(!$festival)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">Pilih festival terlebih dahulu untuk melihat laporan.</p>
            <a href="{{ route('admin.select-festival.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-900 font-medium">
                Pilih Festival →
            </a>
        </div>
    @else
        @if($awardCategories->count() === 0)
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <p class="text-gray-600">Belum ada subkategori dalam festival ini.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subkategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pemenang</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($awardCategories as $category)
                            @php
                                $winnersCount = $category->festivalCategory->assessment_type->value === 'innovation'
                                    ? $category->innovationWinners->count()
                                    : $category->institutionWinners->count();
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $category->festivalCategory->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $category->festivalCategory->assessment_type->label() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($winnersCount === 0) bg-gray-100 text-gray-800
                                        @elseif($winnersCount < $category->winner_count) bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $winnersCount }}/{{ $category->winner_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center space-x-2">
                                    <a href="{{ route('admin.reports.preview', $category) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                                        Pratinjau
                                    </a>
                                    <a href="{{ route('admin.reports.pdf', $category) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                        Unduh PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
@endsection
