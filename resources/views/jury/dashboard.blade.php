<x-layouts.app>
    <x-slot name="header">
        Dashboard Juri
    </x-slot>

    @if($festivals->count() > 0)
        @foreach($festivals as $festival)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $festival->name }} ({{ $festival->year }})
                        </h2>
                        @if($festival->participations->where('user_id', auth()->id())->count() > 0)
                            <a href="{{ route('jury.subcategories.index') }}" 
                               class="inline-flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800">
                                Pilih Subkategori Lain
                            </a>
                        @endif
                    </div>
                    
                    @php
                        $participations = $festival->participations->where('user_id', auth()->id());
                    @endphp
                    
                    @if($participations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subkategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Scoring</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($participations as $participation)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $participation->awardCategory->festivalCategory->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $participation->awardCategory->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                                    {{ $participation->awardCategory->scoring_status->value === 'preparation' ? 'bg-yellow-100 text-yellow-800' : 
                                                      ($participation->awardCategory->scoring_status->value === 'open' ? 'bg-green-100 text-green-800' : 
                                                      'bg-blue-100 text-blue-800') }}">
                                                    {{ $participation->awardCategory->scoring_status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $progressData = $participation->progress ?? ['assessed' => 0, 'total' => 0, 'percentage' => 0];
                                                @endphp
                                                
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="h-2 rounded-full transition-all duration-500 
                                                                {{ $progressData['percentage'] >= 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                                                                style="width: {{ $progressData['percentage'] }}%"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-medium {{ $progressData['percentage'] >= 100 ? 'text-green-600' : 'text-gray-700' }}">
                                                        {{ $progressData['assessed'] }}/{{ $progressData['total'] }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($participation->awardCategory->scoring_status->value === 'open')
                                                    <a href="{{ route('jury.subcategories.show', $participation->awardCategory) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                                                        @if($progressData['percentage'] >= 100)
                                                            Lihat Penilaian
                                                        @elseif($progressData['percentage'] > 0)
                                                            Lanjutkan
                                                        @else
                                                            Mulai Menilai
                                                        @endif
                                                    </a>
                                                @elseif($participation->awardCategory->scoring_status->value === 'preparation')
                                                    <span class="text-xs text-gray-400 italic">Belum dibuka</span>
                                                @else
                                                    <a href="{{ route('jury.subcategories.show', $participation->awardCategory) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-gray-100 border border-transparent rounded-md text-xs font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                                                        Lihat Hasil
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @php
                                    $totalSubcategories = $participations->count();
                                    $openSubcategories = $participations->filter(function($p) {
                                        return $p->awardCategory->scoring_status->value === 'open';
                                    })->count();
                                    $completedSubcategories = $participations->filter(function($p) {
                                        $progress = $p->progress ?? ['percentage' => 0];
                                        return $progress['percentage'] >= 100;
                                    })->count();
                                    $inProgressSubcategories = $participations->filter(function($p) {
                                        $progress = $p->progress ?? ['percentage' => 0];
                                        return $progress['percentage'] > 0 && $progress['percentage'] < 100;
                                    })->count();
                                @endphp
                                
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs text-gray-500 mb-1">Total Subkategori</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $totalSubcategories }}</div>
                                </div>
                                
                                <div class="bg-green-50 rounded-lg p-3">
                                    <div class="text-xs text-green-600 mb-1">Dibuka</div>
                                    <div class="text-2xl font-bold text-green-700">{{ $openSubcategories }}</div>
                                </div>
                                
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <div class="text-xs text-blue-600 mb-1">Sedang Dinilai</div>
                                    <div class="text-2xl font-bold text-blue-700">{{ $inProgressSubcategories }}</div>
                                </div>
                                
                                <div class="bg-purple-50 rounded-lg p-3">
                                    <div class="text-xs text-purple-600 mb-1">Selesai</div>
                                    <div class="text-2xl font-bold text-purple-700">{{ $completedSubcategories }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada subkategori dipilih</h3>
                            <p class="mt-1 text-sm text-gray-500 mb-4">Anda belum memilih subkategori penilaian pada festival ini.</p>
                            <a href="{{ route('jury.subcategories.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 transition-colors">
                                Pilih Subkategori
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada festival</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum terdaftar pada festival manapun.</p>
            </div>
        </div>
    @endif
</x-layouts.app>
