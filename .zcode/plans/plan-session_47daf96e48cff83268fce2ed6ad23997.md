# Tahap 1 — Analisis Kesiapan + Lapisan Database (Laravel 13 + PHP 8.4)

Sesuai persetujuan Anda, dengan penambahan: project Laravel dibuat langsung di root `D:\Projects\Festival-Inovasi` dan repository diinisialisasi ke GitHub `https://github.com/rendiwidyanggita/Festival-Inovasi`.

## Langkah Implementasi

1. **Dokumen analisis** — tulis `festival_inovasi_ai_docs/READINESS_ANALYSIS.md` (temuan verifikasi migration-vs-ERD: konsisten penuh; keputusan versi; item terbuka: PHP/MySQL ArenHost, metode deploy, library PDF Phase 4).

2. **Scaffold Laravel 13 di root** — `composer create-project laravel/laravel` ke folder temp (root tidak kosong), pindahkan seluruh isi ke root; folder `festival_inovasi_ai_docs/` dan `festival_inovasi_migrations/` tetap utuh. PHP 8.4 dari Laragon.

3. **Git + GitHub** — `git init`, commit scaffold awal, `git remote add origin https://github.com/rendiwidyanggita/Festival-Inovasi`, push branch `main`.

4. **Database** — `.env` MySQL + buat database **kosong baru** `festival_inovasi` (non-destruktif), charset utf8mb4.

5. **Migrations** — salin 18 file paket v0.1 (dipakai tanpa modifikasi); hapus default `create_users_table` Laravel; tambah 1 migration infrastruktur `sessions` (standar Laravel, bukan struktur domain); `migrate --pretend` → review → `migrate`.

6. **Enums** (7 string-backed): UserRole, AssessmentType, FestivalStatus, ScoringStatus, ParticipationStatus, AssessmentStatus, FestivalJuryStatus.

7. **17 Eloquent Models + relasi** (single-key; integritas lintas-festival dijaga composite FK DB + validasi service): User (SoftDeletes), Festival, FestivalSetting, FestivalCategory, AwardCategory, Institution (SoftDeletes), Innovation (SoftDeletes), FestivalJury, JurySubcategoryParticipation, AssessmentCriterion, InnovationAssessment, InnovationAssessmentDetail, InstitutionAssessment, InstitutionAssessmentDetail, InnovationWinner, InstitutionWinner, AuditLog (UPDATED_AT null). Morph map alias stabil didaftarkan di AppServiceProvider.

8. **Seeders idempotent** (semua di bawah record festival, tanpa hard-coded ID): SuperadminSeeder (kredensial dari env, gagal keras bila kosong), FestivalSeeder (tahun berjalan, status preparation, + festival_settings), CategorySeeder (2 kategori utama + 13 subkategori PRD §4.3, winner_count 3), AssessmentCriterionSeeder (4 kriteria inovasi 20/30/20/30 + 4 instansi 20/30/30/20), DemoSeeder (local-only, dipanggil eksplisit), DatabaseSeeder mengorkestrasi.

9. **Verifikasi** — seed, smoke test schema (`DatabaseSchemaTest`: 19 tabel, unique index, jumlah kategori/kriteria), `php artisan test`, sanity check relasi via tinker.

10. **Penutup** — commit bertahap (scaffold → database layer), push ke GitHub, laporan akhir: file yang diubah, hasil verifikasi, item belum terverifikasi, usulan tahap berikutnya (Phase 2 PRD: auth + master data).

## Yang TIDAK dikerjakan tahap ini
Controller/route/view/Blade/Tailwind, service penilaian, policy, PDF, CI/CD, deployment. Tidak ada `migrate:fresh`/`db:wipe`; DB target adalah database kosong baru.