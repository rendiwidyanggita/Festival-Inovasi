<x-modal name="festival-modal" maxWidth="2xl">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900" x-text="modalTitle"></h3>
                <p class="text-xs text-slate-500" x-text="isEdit ? 'Perbarui informasi dan pengaturan festival' : 'Isi form di bawah untuk membuat periode festival baru'"></p>
            </div>
        </div>
    </x-slot>

    <form x-ref="festivalForm" :action="formAction" method="POST" @submit.prevent="submitForm" class="space-y-5">
        @csrf
        <template x-if="isEdit">
            <input type="hidden" name="_method" value="PUT">
        </template>

        <!-- Error Summary Alert (if any non-field errors) -->
        <template x-if="errors.general">
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="errors.general"></span>
            </div>
        </template>

        <!-- Grid: Tahun & Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Tahun -->
            <div>
                <label for="modal_year" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tahun <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="modal_year" 
                    name="year" 
                    x-model="form.year"
                    min="2000"
                    max="2100"
                    required
                    class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 py-2.5 px-3.5 transition-colors"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.year }"
                    placeholder="Contoh: {{ date('Y') }}"
                >
                <template x-if="errors.year">
                    <p class="mt-1 text-xs text-red-600 font-medium" x-text="errors.year[0]"></p>
                </template>
            </div>

            <!-- Status (visible in edit mode) -->
            <div x-show="isEdit">
                <label for="modal_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Status Festival
                </label>
                <select 
                    id="modal_status" 
                    name="status" 
                    x-model="form.status"
                    class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3.5 bg-white transition-colors"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.status }"
                >
                    @foreach(\App\Enums\FestivalStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
                <template x-if="errors.status">
                    <p class="mt-1 text-xs text-red-600 font-medium" x-text="errors.status[0]"></p>
                </template>
            </div>
        </div>

        <!-- Nama Festival -->
        <div>
            <label for="modal_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                Nama Festival <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="modal_name" 
                name="name" 
                x-model="form.name"
                required
                class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 py-2.5 px-3.5 transition-colors"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.name }"
                placeholder="Contoh: Festival Inovasi Daerah {{ date('Y') }}"
            >
            <template x-if="errors.name">
                <p class="mt-1 text-xs text-red-600 font-medium" x-text="errors.name[0]"></p>
            </template>
        </div>

        <!-- Section: Pengaturan & Penyelenggara -->
        <div class="pt-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600">
                    Pengaturan Laporan & Penyelenggara
                </span>
                <span class="text-[11px] text-slate-400 font-medium">(Opsional)</span>
            </div>

            <div class="space-y-4">
                <!-- Nama Penyelenggara -->
                <div>
                    <label for="modal_organizer_name" class="block text-xs font-medium text-slate-600 mb-1">
                        Nama Penyelenggara
                    </label>
                    <input 
                        type="text" 
                        id="modal_organizer_name" 
                        name="organizer_name" 
                        x-model="form.organizer_name"
                        class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 py-2.5 px-3.5 transition-colors"
                        :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.organizer_name }"
                        placeholder="Contoh: Badan Riset dan Inovasi Daerah"
                    >
                    <template x-if="errors.organizer_name">
                        <p class="mt-1 text-xs text-red-600 font-medium" x-text="errors.organizer_name[0]"></p>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Path Logo Penyelenggara -->
                    <div>
                        <label for="modal_organizer_logo_path" class="block text-xs font-medium text-slate-600 mb-1">
                            Path Logo Penyelenggara
                        </label>
                        <input 
                            type="text" 
                            id="modal_organizer_logo_path" 
                            name="organizer_logo_path" 
                            x-model="form.organizer_logo_path"
                            class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 py-2.5 px-3.5 transition-colors"
                            placeholder="Contoh: /images/logo.png"
                        >
                    </div>

                    <!-- Judul Laporan -->
                    <div>
                        <label for="modal_report_title" class="block text-xs font-medium text-slate-600 mb-1">
                            Judul Laporan Resmi
                        </label>
                        <input 
                            type="text" 
                            id="modal_report_title" 
                            name="report_title" 
                            x-model="form.report_title"
                            class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 py-2.5 px-3.5 transition-colors"
                            placeholder="Contoh: Laporan Hasil Penilaian Festival"
                        >
                    </div>
                </div>

                <!-- Footer Laporan -->
                <div>
                    <label for="modal_report_footer" class="block text-xs font-medium text-slate-600 mb-1">
                        Footer Laporan
                    </label>
                    <textarea 
                        id="modal_report_footer" 
                        name="report_footer" 
                        x-model="form.report_footer"
                        rows="2"
                        class="block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 py-2 px-3.5 transition-colors"
                        placeholder="Contoh: Dokumen ini diterbitkan secara sah oleh Panitia Penyelenggara."
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button 
                type="button" 
                @click="$dispatch('close-modal', 'festival-modal')"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
            >
                Batal
            </button>
            <button 
                type="submit" 
                :disabled="submitting"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Festival'"></span>
            </button>
        </div>
    </form>
</x-modal>
