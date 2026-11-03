<x-layouts.app>
    <x-slot name="header">
        Kelola Festival
    </x-slot>

    <div x-data="{
        // Modal state
        isEdit: false,
        formAction: '{{ route('admin.festivals.store') }}',
        modalTitle: 'Tambah Festival Baru',
        submitting: false,
        errors: {},
        form: {
            id: null,
            year: {{ old('year', date('Y')) }},
            name: '{{ old('name', '') }}',
            status: '{{ old('status', 'preparation') }}',
            organizer_name: '{{ old('organizer_name', '') }}',
            organizer_logo_path: '{{ old('organizer_logo_path', '') }}',
            report_title: '{{ old('report_title', '') }}',
            report_footer: '{{ old('report_footer', '') }}'
        },

        // Delete confirmation state
        deleteAction: '',
        deleteFestivalName: '',
        deleteCategoriesCount: 0,
        deleteSubmitting: false,

        openCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('admin.festivals.store') }}';
            this.modalTitle = 'Tambah Festival Baru';
            this.errors = {};
            this.form = {
                id: null,
                year: new Date().getFullYear(),
                name: '',
                status: 'preparation',
                organizer_name: '',
                organizer_logo_path: '',
                report_title: '',
                report_footer: ''
            };
            $dispatch('open-modal', 'festival-modal');
        },

        openEdit(festival) {
            this.isEdit = true;
            this.formAction = '/admin/festivals/' + festival.id;
            this.modalTitle = 'Edit Festival: ' + festival.name;
            this.errors = {};
            this.form = {
                id: festival.id,
                year: festival.year,
                name: festival.name,
                status: festival.status || 'preparation',
                organizer_name: festival.organizer_name || '',
                organizer_logo_path: festival.organizer_logo_path || '',
                report_title: festival.report_title || '',
                report_footer: festival.report_footer || ''
            };
            $dispatch('open-modal', 'festival-modal');
        },

        openDelete(festival) {
            this.deleteAction = '/admin/festivals/' + festival.id;
            this.deleteFestivalName = festival.name + ' (' + festival.year + ')';
            this.deleteCategoriesCount = festival.categories_count || 0;
            $dispatch('open-modal', 'delete-festival-modal');
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            const payload = {
                year: this.form.year,
                name: this.form.name,
                organizer_name: this.form.organizer_name,
                organizer_logo_path: this.form.organizer_logo_path,
                report_title: this.form.report_title,
                report_footer: this.form.report_footer,
            };

            if (this.isEdit) {
                payload.status = this.form.status;
                payload._method = 'PUT';
            }

            try {
                const response = await fetch(this.formAction, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        this.errors = data.errors;
                    } else {
                        appAlert({
                            title: 'Gagal Memproses Data',
                            message: data.message || 'Terjadi kesalahan saat memproses data.',
                        });
                    }
                    this.submitting = false;
                    return;
                }

                // Sukses: reload halaman agar tabel dan session flash diperbarui
                window.location.reload();
            } catch (err) {
                console.error('Fetch error:', err);
                // Fallback submission
                this.$refs.festivalForm.submit();
            }
        }
    }">

        <!-- Page Header & Action Bar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Daftar Festival Inovasi</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data periode festival, status penyelenggaraan, dan pengaturan laporan.</p>
            </div>
            <div>
                <button 
                    type="button" 
                    @click="openCreate()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs uppercase tracking-wider rounded-xl shadow-sm transition-colors shadow-blue-500/10"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Festival</span>
                </button>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            @if($festivals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50/75">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Tahun</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Nama Festival</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Status</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Kategori</th>
                                <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($festivals as $festival)
                                @php
                                    $categoriesCount = $festival->festival_categories_count ?? $festival->festivalCategories()->count();
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <!-- Tahun -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono text-xs">
                                            {{ $festival->year }}
                                        </span>
                                    </td>

                                    <!-- Nama Festival -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-slate-900">{{ $festival->name }}</div>
                                        @if($festival->settings?->organizer_name)
                                            <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span>{{ $festival->settings->organizer_name }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($festival->status->value === 'preparation')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ $festival->status->label() }}
                                            </span>
                                        @elseif($festival->status->value === 'ongoing')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                {{ $festival->status->label() }}
                                            </span>
                                        @elseif($festival->status->value === 'judging')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                {{ $festival->status->label() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $festival->status->label() }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span>{{ $categoriesCount }} Kategori</span>
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Tombol Edit Modal -->
                                            <button 
                                                type="button" 
                                                @click="openEdit({{ json_encode([
                                                    'id' => $festival->id,
                                                    'year' => $festival->year,
                                                    'name' => $festival->name,
                                                    'status' => $festival->status->value,
                                                    'organizer_name' => $festival->settings?->organizer_name ?? '',
                                                    'organizer_logo_path' => $festival->settings?->organizer_logo_path ?? '',
                                                    'report_title' => $festival->settings?->report_title ?? '',
                                                    'report_footer' => $festival->settings?->report_footer ?? '',
                                                ]) }})" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors text-xs font-semibold"
                                                title="Edit Festival"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Tombol Hapus Modal -->
                                            <button 
                                                type="button" 
                                                @click="openDelete({{ json_encode([
                                                    'id' => $festival->id,
                                                    'year' => $festival->year,
                                                    'name' => $festival->name,
                                                    'categories_count' => $categoriesCount,
                                                ]) }})" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors text-xs font-semibold"
                                                title="Hapus Festival"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($festivals->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $festivals->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16 px-4">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-900">Belum ada festival</h3>
                    <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto">Mulai dengan membuat periode festival inovasi baru untuk mengelola kompetisi.</p>
                    <div class="mt-6">
                        <button 
                            type="button" 
                            @click="openCreate()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase tracking-wider rounded-xl shadow-sm transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Buat Festival Pertama</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Form Modal (Create / Edit) -->
        @include('admin.festivals._form-modal')

        <!-- Delete Confirmation Modal -->
        <x-modal name="delete-festival-modal" maxWidth="md">
            <div class="p-2 sm:p-4 text-center">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-red-50 text-red-600 mb-4 ring-8 ring-red-50/50">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Festival?</h3>
                <p class="text-sm text-slate-500 mb-5 leading-relaxed">
                    Apakah Anda yakin ingin menghapus festival <span class="font-semibold text-slate-800" x-text="deleteFestivalName"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <template x-if="deleteCategoriesCount > 0">
                    <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200/80 rounded-xl text-amber-800 text-xs text-left flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="leading-relaxed">
                            Festival ini memiliki <strong class="font-bold" x-text="deleteCategoriesCount"></strong> kategori terkait. Hapus atau pindahkan kategori terlebih dahulu sebelum menghapus festival ini.
                        </div>
                    </div>
                </template>

                <form :action="deleteAction" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        @click="$dispatch('close-modal', 'delete-festival-modal')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors w-full"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="deleteCategoriesCount > 0"
                        :class="deleteCategoriesCount > 0 ? 'opacity-50 cursor-not-allowed bg-red-300' : 'bg-red-600 hover:bg-red-700'"
                        class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition-colors w-full"
                    >
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </x-modal>

    </div>
</x-layouts.app>
