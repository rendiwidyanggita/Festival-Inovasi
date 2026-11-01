@extends('layouts.app')

@section('title', 'Manajemen Pemenang')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pemenang</h1>
            @if($festival)
                <p class="text-sm text-gray-500 mt-1">{{ $festival->name }} ({{ $festival->year }})</p>
            @endif
        </div>
    </div>

    @if(!$festival)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">Pilih festival terlebih dahulu untuk mengelola pemenang.</p>
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
            <div class="space-y-4">
                @foreach($awardCategories as $category)
                    @php
                        $winners = $category->festivalCategory->assessment_type->value === 'innovation'
                            ? $category->innovationWinners
                            : $category->institutionWinners;
                    @endphp
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $category->festivalCategory->name }} • {{ $category->festivalCategory->assessment_type->label() }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600">
                                    {{ $winners->count() }}/{{ $category->winner_count }} pemenang
                                </span>
                                <a href="{{ route('admin.award-categories.rankings', $category) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                                    Lihat Peringkat
                                </a>
                            </div>
                        </div>

                        @if($winners->count() > 0)
                            <div class="mt-4 border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Pemenang:</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($winners as $winner)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($winner->position === 1) bg-yellow-100 text-yellow-800
                                            @elseif($winner->position === 2) bg-gray-100 text-gray-800
                                            @elseif($winner->position === 3) bg-orange-100 text-orange-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            Juara {{ $winner->position }}:
                                            @if($category->festivalCategory->assessment_type->value === 'innovation')
                                                {{ $winner->innovation->name ?? '-' }}
                                            @else
                                                {{ $winner->institution->name ?? '-' }}
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
