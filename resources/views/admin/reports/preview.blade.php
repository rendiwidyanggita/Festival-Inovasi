@extends('layouts.app')

@section('title', 'Pratinjau Laporan - ' . $category->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pratinjau Laporan</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $category->name }} • {{ $festival->name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm">
                ← Kembali
            </a>
            <a href="{{ route('admin.reports.pdf', $category) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                Unduh PDF
            </a>
        </div>
    </div>

    <!-- Preview Container -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center border-b-2 border-blue-800 pb-6 mb-6">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 bg-blue-800 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-blue-800">Festival Inovasi</h1>
                <h2 class="text-lg text-gray-600">{{ $festival->name }} ({{ $festival->year }})</h2>
                <p class="text-sm text-gray-500">{{ $festivalCategory->name }}</p>
                <p class="text-sm text-gray-500 mt-2">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Category Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    <strong>Jenis:</strong> {{ $festivalCategory->assessment_type->label() }} • 
                    <strong>Jumlah Pemenang:</strong> {{ $category->winner_count }}
                </p>
            </div>

            <!-- Winners Section -->
            @if($winners->count() > 0)
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Daftar Pemenang</h4>
                <div class="space-y-3">
                    @foreach($winners as $winner)
                        <div class="flex items-center p-4 rounded-lg
                            @if($winner->position === 1) bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-300
                            @elseif($winner->position === 2) bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300
                            @elseif($winner->position === 3) bg-gradient-to-r from-orange-50 to-orange-100 border border-orange-300
                            @else bg-gray-50 border border-gray-200 @endif">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold
                                @if($winner->position === 1) bg-yellow-500
                                @elseif($winner->position === 2) bg-gray-400
                                @elseif($winner->position === 3) bg-orange-500
                                @else bg-gray-500 @endif">
                                {{ $winner->position }}
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900">
                                    @if($isInnovation)
                                        {{ $winner->innovation->name ?? '-' }}
                                    @else
                                        {{ $winner->institution->name ?? '-' }}
                                    @endif
                                </p>
                                @if($isInnovation && $winner->innovation->institution)
                                    <p class="text-sm text-gray-600">{{ $winner->innovation->institution->name }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ number_format($winner->total_score, 2) }}</p>
                                <p class="text-sm text-gray-500">poin</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Participants Table -->
            <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Daftar Peserta dan Peringkat</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase">Peringkat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    {{ $isInnovation ? 'Nama Inovasi' : 'Nama Instansi' }}
                                </th>
                                @if($isInnovation)
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Inovator</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Instansi</th>
                                @endif
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rankings as $ranking)
                                @php
                                    $winner = $isInnovation
                        ? $winners->firstWhere('innovation_id', $ranking->id)
                        : $winners->firstWhere('institution_id', $ranking->id);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        @if($winner)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Juara {{ $winner->position }}
                                            </span>
                                        @else
                                            {{ $ranking->rank }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ranking->name }}</td>
                                    @if($isInnovation)
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $ranking->innovator_name ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $ranking->institution_name ?? '-' }}</td>
                                    @endif
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900">{{ number_format($ranking->average_score, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Festival Inovasi. Hak cipta dilindungi.</p>
                <p class="mt-1">Dokumen ini diterbitkan secara resmi oleh penyelenggara Festival Inovasi.</p>
            </div>
        </div>
    </div>
</div>
@endsection
