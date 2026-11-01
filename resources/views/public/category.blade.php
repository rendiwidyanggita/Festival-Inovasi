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
        @if(!$festival->hasPublishedResults())
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800">
                    <strong>Penilaian sedang berlangsung.</strong> 
                    Daftar peserta ditampilkan, namun nilai dan peringkat belum tersedia hingga pengumuman resmi.
                </p>
            </div>
        @endif

        <!-- Winners Section (if results published) -->
        @if($festival->hasPublishedResults())
            @php
                $isInnovation = $awardCategory->festivalCategory->assessment_type->value === 'innovation';
                $winners = $isInnovation ? $awardCategory->innovationWinners : $awardCategory->institutionWinners;
            @endphp
            
            @if($winners && $winners->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Pemenang</h2>
                    <div class="space-y-4">
                        @foreach($winners as $winner)
                            <div class="flex items-center p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200">
                                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full 
                                    @if($winner->position === 1) bg-yellow-400 text-white @elseif($winner->position === 2) bg-gray-300 text-gray-900 @else bg-amber-600 text-white @endif font-bold text-lg">
                                    {{ $winner->position }}
                                </div>
                                <div class="ml-4">
                                    @if($isInnovation)
                                        <p class="font-semibold text-gray-900">{{ $winner->innovation->name ?? '-' }}</p>
                                        <p class="text-sm text-gray-600">{{ $winner->innovation->institution->name ?? '-' }}</p>
                                    @else
                                        <p class="font-semibold text-gray-900">{{ $winner->institution->name ?? '-' }}</p>
                                    @endif
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ number_format($winner->total_score, 2) }}</p>
                                    <p class="text-sm text-gray-500">poin</p>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peringkat</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $awardCategory->festivalCategory->assessment_type->value === 'innovation' ? 'Nama Inovasi' : 'Nama Instansi' }}
                                    </th>
                                    @if($awardCategory->festivalCategory->assessment_type->value === 'innovation')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inovator</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instansi</th>
                                    @endif
                                    @if($festival->hasPublishedResults())
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($participants as $participant)
                                    <tr>
                                        @if($festival->hasPublishedResults())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @php
                                                    $winner = $awardCategory->festivalCategory->assessment_type->value === 'innovation'
                                                        ? $awardCategory->innovationWinners?->firstWhere('innovation_id', $participant->id)
                                                        : $awardCategory->institutionWinners?->firstWhere('institution_id', $participant->id);
                                                @endphp
                                                @if($winner)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Juara {{ $winner->position }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $participant->name }}
                                        </td>
                                        @if($awardCategory->festivalCategory->assessment_type->value === 'innovation')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $participant->innovator_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $participant->institution->name ?? '-' }}
                                            </td>
                                        @endif
                                        @if($festival->hasPublishedResults())
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                                @if($winner)
                                                    {{ number_format($winner->total_score, 2) }}
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
                        <p class="text-gray-500">Belum ada peserta terdaftar pada subkategori ini.</p>
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
