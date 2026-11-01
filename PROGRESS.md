# Progress & Rencana Implementasi

**Terakhir diperbarui:** 2026-11-01
**Repositori:** https://github.com/rendiwidyanggita/Festival-Inovasi (branch `main`)

## Status saat ini — Tahap 1 SELESAI

### Sudah terimplementasi & terverifikasi

1. **Scaffold Laravel 13.17** di root `D:\Projects\Festival-Inovasi` (PHP 8.4.24 via Laragon), `.env` MySQL lokal, `git init`, remote GitHub, 3 commit ter-push.
2. **Database**: 18 migration paket v0.1 terpasang tanpa modifikasi + 1 migration infrastruktur `sessions`; default `create_users_table` Laravel dihapus (sesuai README paket). DB `festival_inovasi` (dev) & `festival_inovasi_test` (test), MySQL 8.0.30. `migrate --pretend` direview sebelum eksekusi.
3. **7 Enums** (`app/Enums`): UserRole, AssessmentType, FestivalStatus, ScoringStatus, ParticipationStatus, AssessmentStatus, FestivalJuryStatus — string-backed + label Indonesia.
4. **17 Eloquent Models** (`app/Models`) dengan relasi lengkap sesuai ERD, `#[Fillable]`, casts enum, soft delete (User/Institution/Innovation), `AuditLog::UPDATED_AT = null`, `weightedTotal()` di header assessment, morph map alias stabil di `AppServiceProvider`.
5. **Seeder** (`database/seeders`): `DatabaseSeeder` → SuperadminSeeder (kredensial dari env, gagal keras bila kosong), FestivalSeeder (tahun berjalan, override `FESTIVAL_YEAR`, + festival_settings), CategorySeeder (2 kategori + 13 subkategori PRD §4.3, `winner_count` default 3 — konfigurabel), AssessmentCriterionSeeder (4+4 kriteria, total bobot 100), `DemoSeeder` (local-only, via `--class=DemoSeeder`: 3 juri, 18 instansi, 14 inovasi contoh).
6. **Test**: `tests/Feature/DatabaseSchemaTest.php` (phpunit.xml dialihkan ke MySQL test) — 9 test / 38 assertions **semua lulus**: keberadaan tabel, unique tahun festival, composite FK lintas-festival, duplikasi partisipasi juri, duplikasi posisi juara, hasil seeder sesuai PRD, soft delete user mempertahankan histori.

### Commit di GitHub
- `chore: scaffold Laravel 13 project`
- `feat(db): install festival inovasi migrations v0.1 + sessions table`
- `feat(models): 7 enums, 17 eloquent models + relasi, morph map audit`
- (commit seeder + test + analisis — lihat `git log` terbaru)

## Catatan penting untuk sesi berikutnya

- **Menjalankan MySQL lokal**: `C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqld.exe --defaults-file="C:\laragon\bin\mysql\mysql-8.0.30-winx64\my.ini" --console` (root tanpa password). PHP: tambahkan `C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64` ke PATH.
- **Jangan** `migrate:fresh` / `db:wipe` di luar database test tanpa persetujuan.
- Validasi lintas-row (score 6–10, bobot 100, roster lock, dsb.) **belum** diimplementasikan — tugas service layer Phase 3.
- Kredensial superadmin dev ada di `.env` lokal (tidak di-commit); `.env.example` memuat placeholder.

## Rencana tahap berikutnya (belum diimplementasikan)

### Phase 2 — Authentication & Master Data (PRD §8)
1. Login/logout (Blade + Tailwind, UI Bahasa Indonesia), middleware `EnsureRole` (superadmin/jury), cek `is_active`.
2. Struktur `app/Http/Controllers/{Auth,Admin,Jury,Public}`, `Requests`, `Policies`, `Services/Audit`.
3. CRUD superadmin: periode festival (tahun unik), kategori utama & subkategori, instansi, inovasi, akun juri (soft delete + relasi periode-juri), kriteria & bobot (validasi total 100, kunci setelah penilaian dimulai).
4. Halaman publik read-only (tanpa nilai sebelum publikasi).
5. Feature test otorisasi dasar (AI_AGENT_GUIDE §8).

### Phase 3 — Scoring System
Pemilihan subkategori juri (hanya `preparation`), roster lock saat open (transaksi), form penilaian (draft/submit), service perhitungan tertimbang + rata-rata, monitoring, revisi & lock, audit nilai.

### Phase 4 — Reporting & Winner
Ranking (hitung on-the-fly dari assessment locked), penetapan pemenang manual + validasi `winner_count`, PDF (`barryvdh/laravel-dompdf` — usulan), publikasi manual.

### Phase 5 — Testing & Deployment
Feature test lengkap, CI GitHub Actions (setelah metode deploy ArenHost dikonfirmasi: PHP ≥ 8.3, MySQL/MariaDB versi), prosedur backup/rollback.

## Item menunggu keputusan/konfirmasi pemilik proyek
1. Versi PHP & MySQL/MariaDB ArenHost Competent (syarat Laravel 13: PHP ≥ 8.3).
2. Metode deployment (SSH / cPanel Git / SFTP).
3. `winner_count` default 3 per subkategori — silakan koreksi bila kebijakan berbeda.
