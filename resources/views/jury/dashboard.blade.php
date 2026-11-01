<x-layouts.app>
    <x-slot name="header">
        Dashboard Juri
    </x-slot>

    @if($festivals->count() > 0)
        @foreach($festivals as $festival)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ $festival->name }} ({{ $festival->year }})
                    </h2>
                    
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($participations as $participation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $participation->awardCategory->festivalCategory->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $participation->awardCategory->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $participation->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                      ($participation->status->value === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                      ($participation->status->value === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ $participation->status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($participation->status->value === 'completed')
                                                    <span class="text-green-600">Selesai</span>
                                                @elseif($participation->status->value === 'in_progress')
                                                    <span class="text-blue-600">Sedang Dinilai</span>
                                                @else
                                                    <span class="text-yellow-600">Belum Dimulai</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($participation->awardCategory->scoring_status->value === 'open')
                                                    <a href="{{ route('jury.assessments.show', $participation) }}" class="text-blue-600 hover:text-blue-900">
                                                        @if($participation->status->value === 'completed')
                                                            Lihat
                                                        @else
                                                            Nilai
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Anda belum ditugaskan ke subkategori manapun pada festival ini.</p>
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
