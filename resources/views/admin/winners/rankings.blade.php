@extends('layouts.app')

@section('title', 'Peringkat - ' . $awardCategory->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Peringkat {{ $awardCategory->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $awardCategory->festivalCategory->name }} • {{ $awardCategory->festivalCategory->assessment_type->label() }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                {{ $awardCategory->winner_count }} Pemenang
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="list-disc list-inside text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Peserta dan Peringkat</h2>
            <p class="text-sm text-gray-500 mt-1">
                Peringkat dihitung dari rata-rata nilai penilaian yang sudah dikunci.
            </p>
        </div>

        <form action="{{ route('admin.award-categories.winners.store', $awardCategory) }}" method="POST" id="winnersForm">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peringkat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $isInnovation ? 'Nama Inovasi' : 'Nama Instansi' }}
                            </th>
                            @if($isInnovation)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inovator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instansi</th>
                            @endif
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Rata-rata</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi Juara</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rankings as $ranking)
                            @php
                                $existingWinner = $existingWinners->get($ranking->id);
                            @endphp
                            <tr class="@if($existingWinner) bg-green-50 @endif">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="winners[{{ $loop->index }}][selected]" 
                                           value="1" 
                                           class="winner-checkbox rounded border-gray-300"
                                           @if($existingWinner) checked @endif
                                           data-id="{{ $ranking->id }}"
                                           data-type="{{ $isInnovation ? 'innovation' : 'institution' }}">
                                    <input type="hidden" name="winners[{{ $loop->index }}][target_type]" 
                                           value="{{ $isInnovation ? 'innovation' : 'institution' }}">
                                    <input type="hidden" name="winners[{{ $loop->index }}][target_id]" 
                                           value="{{ $ranking->id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ranking->rank }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $ranking->name }}
                                </td>
                                @if($isInnovation)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ranking->innovator_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ranking->institution_name ?? '-' }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    {{ number_format($ranking->average_score, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($existingWinner)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Juara {{ $existingWinner->position }}
                                        </span>
                                    @endif
                                    <select name="winners[{{ $loop->index }}][position]" 
                                            class="position-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm @if(!$existingWinner) hidden @endif"
                                            data-selected="{{ $existingWinner->position ?? '' }}">
                                        <option value="">Pilih posisi</option>
                                        @for($i = 1; $i <= $awardCategory->winner_count; $i++)
                                            <option value="{{ $i }}" @if($existingWinner && $existingWinner->position == $i) selected @endif>
                                                Juara {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isInnovation ? 7 : 5 }}" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada peserta dalam subkategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Pilih peserta dan tentukan posisi juara untuk menetapkan pemenang.
                    </p>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                        Simpan Pemenang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('.winner-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = e.target.checked;
            togglePositionSelect(cb);
        });
    });

    document.querySelectorAll('.winner-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            togglePositionSelect(this);
        });
    });

    function togglePositionSelect(checkbox) {
        const row = checkbox.closest('tr');
        const positionSelect = row.querySelector('.position-select');
        
        if (checkbox.checked) {
            positionSelect.classList.remove('hidden');
        } else {
            positionSelect.classList.add('hidden');
            positionSelect.value = '';
        }
    }

    // Initialize on page load
    document.querySelectorAll('.winner-checkbox:checked').forEach(cb => {
        togglePositionSelect(cb);
    });
</script>
@endpush
@endsection
