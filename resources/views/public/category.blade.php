<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $awardCategory->name }} - {{ $festival->name }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-800">Festival Inovasi</a>
                    <span class="text-gray-300">/</span>
                    <a href="{{ route('public.festival', $festival) }}" class="text-gray-600 hover:text-gray-900">
                        {{ $festival->name }}
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $awardCategory->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $awardCategory->festivalCategory->name }} • {{ $awardCategory->festivalCategory->assessment_type->label() }}
                    </p>
                </div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ $awardCategory->winner_count }} Pemenang
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @php
            $isInnovation = $awardCategory->festivalCategory->assessment_type->value === 'innovation';
        @endphp

        @if(!$festival->hasPublishedResults())
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-blue-900 font-semibold">Hasil belum diumumkan</h3>
                        <p class="text-blue-800 text-sm mt-1">
                            Penilaian sedang berlangsung. Daftar peserta ditampilkan untuk informasi, namun nilai dan peringkat belum tersedia hingga pengumuman resmi.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Winners Section (if results published) -->
        @if($festival->hasPublishedResults())
            @php
                $winners = $isInnovation ? $awardCategory->innovationWinners : $awardCategory->institutionWinners;
            @endphp
            
            @if($winners && $winners->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                        </svg>
                        Pemenang {{ $awardCategory->name }}
                    </h2>
                    <div class="space-y-4">
                        @foreach($winners->sortBy('position') as $winner)
                            <div class="flex items-center p-4 rounded-lg border-2 
                                @if($winner->position === 1) 
                                    bg-gradient-to-r from-yellow-50 to-yellow-100 border-yellow-300
                                @elseif($winner->position === 2) 
                                    bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300
                                @elseif($winner->position === 3) 
                                    bg-gradient-to-r from-amber-50 to-amber-100 border-amber-300
                                @else 
                                    bg-gradient-to-r from-blue-50 to-blue-100 border-blue-300
                                @endif">
                                <div class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-full font-bold text-xl
                                    @if($winner->position === 1) 
                                        bg-yellow-400 text-white shadow-lg
                                    @elseif($winner->position === 2) 
                                        bg-gray-300 text-gray-900 shadow
                                    @elseif($winner->position === 3) 
                                        bg-amber-600 text-white shadow
                                    @else 
                                        bg-blue-500 text-white shadow
                                    @endif">
                                    {{ $winner->position }}
                                </div>
                                <div class="ml-4 flex-1">
                                    @if($isInnovation)
                                        <p class="font-semibold text-gray-900 text-lg">{{ $winner->innovation->name ?? '-' }}</p>
                                        <p class="text-sm text-gray-600">{{ $winner->innovation->institution->name ?? '-' }}</p>
                                    @else
                                        <p class="font-semibold text-gray-900 text-lg">{{ $winner->institution->name ?? '-' }}</p>
                                    @endif
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-2xl font-bold text-gray-900">{{ number_format($winner->total_score, 2) }}</p>
                                    <p class="text-sm text-gray-500">poin</p>
                                </div>
                                <div class="ml-4">
                                    @if($winner->position === 1)
                                        <svg class="w-10 h-10 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($winner->position === 2)
                                        <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($winner->position === 3)
                                        <svg class="w-10 h-10 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <!-- Participants List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Daftar Peserta ({{ $participants->count() }})</h2>
                
                @if($participants->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if($festival->hasPublishedResults())
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Peringkat</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $isInnovation ? 'Nama Inovasi' : 'Nama Instansi' }}
                                    </th>
                                    @if($isInnovation)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inovator</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instansi</th>
                                    @endif
                                    @if($festival->hasPublishedResults())
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Rata-rata</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($participants as $index => $participant)
                                    @php
                                        $rank = $index + 1;
                                        $winner = null;
                                        $score = null;
                                        
                                        if ($festival->hasPublishedResults()) {
                                            $winner = $isInnovation
                                                ? ($awardCategory->innovationWinners->firstWhere('innovation_id', $participant->id) ?? null)
                                                : ($awardCategory->institutionWinners->firstWhere('institution_id', $participant->id) ?? null);
                                            
                                            // Get score from rankings if available
                                            if (isset($rankings)) {
                                                $rankingData = $rankings->firstWhere('id', $participant->id);
                                                $score = $rankingData->average_score ?? null;
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ $winner ? 'bg-yellow-50' : '' }} hover:bg-gray-50 transition-colors">
                                        @if($festival->hasPublishedResults())
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($winner)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $winner->position === 1 ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 
                                                           ($winner->position === 2 ? 'bg-gray-100 text-gray-800 border border-gray-300' : 
                                                           ($winner->position === 3 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-100 text-blue-800 border border-blue-300')) }}">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        Juara {{ $winner->position }}
                                                    </span>
                                                @else
                                                    <div class="flex items-center justify-center w-8 h-8 rounded-full font-semibold text-sm
                                                        {{ $rank === 1 ? 'bg-yellow-100 text-yellow-800' : 
                                                           ($rank === 2 ? 'bg-gray-100 text-gray-800' : 
                                                           ($rank === 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600')) }}">
                                                        {{ $rank }}
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $participant->name }}
                                        </td>
                                        @if($isInnovation)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $participant->innovator_name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $participant->institution->name ?? '-' }}
                                            </td>
                                        @endif
                                        @if($festival->hasPublishedResults())
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                                @if($winner)
                                                    {{ number_format($winner->total_score, 2) }}
                                                @elseif($score)
                                                    {{ number_format($score, 2) }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada peserta</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada peserta terdaftar pada subkategori ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-gray-600">
                &copy; {{ date('Y') }} Festival Inovasi. Hak cipta dilindungi.
            </p>
        </div>
    </footer>
</body>
</html>
