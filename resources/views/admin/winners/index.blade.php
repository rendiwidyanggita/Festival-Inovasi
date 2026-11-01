@extends('layouts.app')

@section('title', 'Manajemen Pemenang')

@section('content')
<div class="space-y-6">
    <!-- Header Card (Preline UI / Flowbite style) -->
    <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                    <svg class="size-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                    </svg>
                    Penetapan Juara & Pengumuman
                </span>
                @if($festival)
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700">
                        {{ $festival->name }} ({{ $festival->year }})
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Penetapan Pemenang</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Tetapkan pemenang tiap subkategori berdasarkan hasil kalkulasi ranking skor tertimbang dewan juri yang telah berstatus terkunci (locked).
            </p>
        </div>

        @if($festival)
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.reports.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-blue-600/30 transition-all duration-150 active:scale-[0.96]">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Lihat Dokumen Laporan PDF
                </a>
            </div>
        @endif
    </div>

    @if(!$festival)
        <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-6 text-center max-w-lg mx-auto my-8">
            <p class="text-xs text-amber-800 font-medium">Pilih periode festival terlebih dahulu untuk mengelola pemenang.</p>
            <a href="{{ route('admin.select-festival.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-amber-900 underline">
                Pilih Festival Sekarang →
            </a>
        </div>
    @else
        @if($awardCategories->count() === 0)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center">
                <p class="text-sm text-slate-500">Belum ada subkategori dalam festival ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($awardCategories as $category)
                    @php
                        $isInnovation = $category->festivalCategory->assessment_type->value === 'innovation';
                        $winners = $isInnovation ? $category->innovationWinners : $category->institutionWinners;
                        $quotaFilled = $winners->count() >= $category->winner_count;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 hover:border-slate-300 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $isInnovation ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                        {{ $category->festivalCategory->name }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs font-medium text-slate-500">
                                        {{ $category->festivalCategory->assessment_type->label() }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">{{ $category->name }}</h3>
                            </div>
                            
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tabular-nums {{ $quotaFilled ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    <span class="size-1.5 rounded-full {{ $quotaFilled ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $winners->count() }}/{{ $category->winner_count }} Juara Ditetapkan
                                </span>
                                <a href="{{ route('admin.award-categories.rankings', $category) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm shadow-blue-600/30 transition-all duration-150 active:scale-[0.96]">
                                    <svg class="size-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                    </svg>
                                    Lihat Peringkat & Tetapkan
                                </a>
                            </div>
                        </div>

                        @if($winners->count() > 0)
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2.5">Pemenang Resmi:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    @foreach($winners->sortBy('position') as $winner)
                                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ $winner->position === 1 ? 'bg-amber-50/60 border-amber-200 text-amber-900' : ($winner->position === 2 ? 'bg-slate-50 border-slate-200 text-slate-900' : 'bg-orange-50/60 border-orange-200 text-orange-900') }}">
                                            <div class="size-8 rounded-lg font-black text-xs flex items-center justify-center shrink-0 {{ $winner->position === 1 ? 'bg-amber-400 text-amber-950 shadow-xs' : ($winner->position === 2 ? 'bg-slate-300 text-slate-800' : 'bg-orange-400 text-orange-950') }}">
                                                #{{ $winner->position }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold truncate">
                                                    @if($isInnovation)
                                                        {{ $winner->innovation->name ?? '-' }}
                                                    @else
                                                        {{ $winner->institution->name ?? '-' }}
                                                    @endif
                                                </div>
                                                @if($isInnovation && $winner->innovation)
                                                    <div class="text-[10px] text-slate-500 truncate mt-0.5">
                                                        {{ $winner->innovation->innovator_name }} • {{ $winner->innovation->institution->name ?? '' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
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
