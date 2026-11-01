# Progress & Rencana Implementasi

**Terakhir diperbarui:** 2026-11-01  
**Repositori:** https://github.com/rendiwidyanggita/Festival-Inovasi (branch `main`)  
**Lokasi lokal:** `/home/rendiwidyanggita/Projects/Festival-Inovasi` (Linux WSL2)

## Status saat ini — Phase 2 SELESAI (2026-11-01)

### Sudah terimplementasi & terverifikasi (Phase 1 + 2)

**Phase 1:**
1. **Scaffold Laravel 13.17** (PHP 8.5.4, Composer 2.9.5), `.env` MySQL lokal, `git init`, remote GitHub, 3 commit ter-push.
2. **Database**: 18 migration paket v0.1 terpasang tanpa modifikasi + 1 migration infrastruktur `sessions`; default `create_users_table` Laravel dihapus (sesuai README paket). DB `festival_inovasi` (dev) & `festival_inovasi_test` (test), MySQL.
3. **7 Enums** (`app/Enums`): UserRole, AssessmentType, FestivalStatus, ScoringStatus, ParticipationStatus, AssessmentStatus, FestivalJuryStatus — string-backed + label Indonesia.
4. **17 Eloquent Models** (`app/Models`) dengan relasi lengkap sesuai ERD, `#[Fillable]`, casts enum, soft delete (User/Institution/Innovation), `AuditLog::UPDATED_AT = null`, `weightedTotal()` di header assessment, morph map alias stabil di `AppServiceProvider`.
5. **Seeder** (`database/seeders`): `DatabaseSeeder` → SuperadminSeeder (kredensial dari env, gagal keras bila kosong), FestivalSeeder (tahun berjalan, override `FESTIVAL_YEAR`, + festival_settings), CategorySeeder (2 kategori + 13 subkategori PRD §4.3, `winner_count` default 3 — konfigurabel), AssessmentCriterionSeeder (4+4 kriteria, total bobot 100), `DemoSeeder` (local-only, via `--class=DemoSeeder`: 3 juri, 18 instansi, 14 inovasi contoh).
6. **Test Phase 1**: `tests/Feature/DatabaseSchemaTest.php` (phpunit.xml dialihkan ke MySQL test) — 9 test / 38 assertions **semua lulus**: keberadaan tabel, unique tahun festival, composite FK lintas-festival, duplikasi partisipasi juri, duplikasi posisi juara, hasil seeder sesuai PRD, soft delete user mempertahankan histori.

**Phase 2 (Authentication & Master Data):**

7. **Middleware**: `EnsureRole` middleware + alias `role` — verifikasi `is_active` + role user (superadmin/jury).
8. **Authentication**:
   - `LoginController` (show, login, logout) + `LoginRequest` (validasi + rate limiting: 5 attempts/menit).
   - Route: `GET /login` (guest), `POST /login`, `POST /logout` (auth).
   - View: `auth/login.blade.php` (Tailwind, Bahasa Indonesia, responsive).
9. **Layouts & Components**:
   - `layouts/guest.blade.php` — layout login.
   - `layouts/app.blade.php` — authenticated layout, sidebar responsif, nav role-based (superadmin/jury).
   - `components/nav-link.blade.php` — link sidebar dengan active state.
10. **Controllers & Services**:
    - Admin: `DashboardController`, `FestivalSelectionController`, `FestivalCrudController`, `MasterDataCrudController`.
    - Jury: `DashboardController`, `FestivalSelectionController`, `AssessmentCriterionController`.
    - Public: `PublicController` (index, festival, category — read-only).
    - `Services/EnsureRole.php` — helper otorisasi role.
    - `Services/Audit.php` — audit log writer (relasi polimorfik).
11. **Policies**: `FestivalPolicy`, `InstitutionPolicy`, `InnovationPolicy`, `UserPolicy`, `AssessmentCriterionPolicy` — registered di `AuthServiceProvider`.
12. **Form Requests**: validasi untuk create/update Festival, Institution, Innovation, User, AssessmentCriterion — bobot total 100 untuk kriteria, unique email user, dll.
13. **Views** (Blade + Tailwind, Bahasa Indonesia):
    - Admin: dashboard, festival selection, festival CRUD (index, create, edit), master data CRUD (institutions, innovations, users, criteria).
    - Jury: dashboard, festival selection, criteria view/edit (bobot locked jika penilaian sudah dimulai).
    - Public: homepage, festival list, category list (tanpa nilai sebelum publikasi).
14. **Routes** (`routes/web.php`):
    - Public: `/`, `/festivals`, `/festivals/{festival}`, `/festivals/{festival}/categories/{category}`.
    - Auth: `/login` (guest), `/logout` (auth).
    - Admin: `/admin` prefix, middleware `auth,role:superadmin`.
    - Jury: `/jury` prefix, middleware `auth,role:jury`.
15. **Feature Tests** (76 tests total):
    - `AuthenticationTest` (login, logout, validation, rate limiting, is_active check).
    - `Admin/FestivalCrudTest` (list, create, edit, delete festivals + audit log).
    - `Admin/MasterDataCrudTest` (CRUD institutions, innovations, users, assessment criteria).
    - `PublicAccessTest` (guest access, authorization boundaries, public pages).
    - **Semua test lulus** (76 passed, database schema tests included).
16. **Audit Logs**: Implementasi `Services/Audit::log()` — mencatat actor, action, auditable (morph), changes (JSON), route integration di controllers.


### Commit di GitHub
- `chore: scaffold Laravel 13 project`
- `feat(db): install festival inovasi migrations v0.1 + sessions table`
- `feat(models): 7 enums, 17 eloquent models + relasi, morph map audit`
- (commit seeder + test + analisis — lihat `git log` terbaru)

## Catatan penting untuk sesi berikutnya

- Validasi lintas-row (score 6–10, bobot 100, roster lock, dsb.) **belum** diimplementasikan — tugas service layer Phase 3.
- Kredensial superadmin dev ada di `.env` lokal (tidak di-commit); `.env.example` memuat placeholder.

## Rencana tahap berikutnya

### Phase 3 — Scoring System (BELUM)
Pemilihan subkategori juri (hanya `preparation`), roster lock saat open (transaksi), form penilaian (draft/submit), service perhitungan tertimbang + rata-rata, monitoring, revisi & lock, audit nilai.

### Phase 4 — Reporting & Winner (BELUM)
Ranking (hitung on-the-fly dari assessment locked), penetapan pemenang manual + validasi `winner_count`, PDF (`barryvdh/laravel-dompdf` — usulan), publikasi manual.

### Phase 5 — Testing & Deployment (BELUM)
Feature test lengkap, CI GitHub Actions (setelah metode deploy ArenHost dikonfirmasi: PHP ≥ 8.3, MySQL/MariaDB versi), prosedur backup/rollback.

## Item menunggu keputusan/konfirmasi pemilik proyek
1. Versi PHP & MySQL/MariaDB ArenHost Competent (syarat Laravel 13: PHP ≥ 8.3).
2. Metode deployment (SSH / cPanel Git / SFTP).
3. `winner_count` default 3 per subkategori — silakan koreksi bila kebijakan berbeda.
