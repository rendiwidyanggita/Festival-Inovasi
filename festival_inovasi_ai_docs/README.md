# Dokumentasi Proyek — Aplikasi Penilaian Festival Inovasi

Dokumen ini merupakan paket referensi untuk AI agent dan developer yang akan melanjutkan pengembangan aplikasi.

## Dokumen sumber

| File | Fungsi | Status |
|---|---|---|
| `PRD.md` | Kebutuhan produk, role, alur pengguna, fitur, dan acceptance criteria | Final v1.0 |
| `ERD.md` | Relasi database, data dictionary, kardinalitas, dan constraints | ERD disetujui |
| `TECH_STACK_DEPLOYMENT.md` | Stack Laravel, database, hosting, serta pendekatan CI/CD | Disepakati |
| `BUSINESS_RULES.md` | Aturan bisnis terperinci yang wajib dijaga saat implementasi | Acuan implementasi |
| `AI_AGENT_GUIDE.md` | Instruksi kerja dan batasan untuk AI coding agent | Acuan implementasi |
| `MIGRATION_PACKAGE_REFERENCE.md` | Penjelasan paket migration Laravel yang disediakan terpisah | Referensi |

## Paket Laravel Migration

Paket PHP migration telah diberikan terpisah dan diunduh oleh pemilik proyek:

`festival_inovasi_laravel_migrations_v0_1.zip`

Paket tersebut berisi 18 file migration. **Jangan membuat ulang atau mengganti migration tanpa memeriksa isi paket terlebih dahulu.** Lihat `MIGRATION_PACKAGE_REFERENCE.md`.

## Urutan membaca yang disarankan

1. Baca `PRD.md` untuk memahami ruang lingkup produk.
2. Baca `BUSINESS_RULES.md` untuk memahami aturan yang tidak boleh dilanggar.
3. Baca `ERD.md` untuk memahami struktur database dan relasi.
4. Baca `TECH_STACK_DEPLOYMENT.md` untuk batasan teknologi dan deployment.
5. Baca `AI_AGENT_GUIDE.md` sebelum mulai melakukan perubahan kode.
6. Cocokkan dengan paket migration yang sudah diunduh.

## Hierarki sumber kebenaran

Jika ada ketidaksesuaian antarfile:

1. Keputusan eksplisit terbaru dari pemilik proyek menjadi sumber keputusan utama.
2. `PRD.md` menjadi acuan kebutuhan produk.
3. `BUSINESS_RULES.md` menjadi acuan perilaku dan validasi bisnis.
4. `ERD.md` menjadi acuan struktur dan relasi database yang disetujui.
5. Paket migration menjadi artefak implementasi awal yang perlu diuji terhadap ERD.
6. `TECH_STACK_DEPLOYMENT.md` menjadi acuan teknologi dan batasan hosting.

Jangan menyelesaikan konflik dengan asumsi diam-diam. Laporkan konflik dan minta keputusan pemilik proyek jika konflik mengubah kebutuhan atau integritas data.

## Catatan status

- PRD v1.0 telah disetujui.
- ERD konseptual dan relasi utama telah disetujui.
- Tech stack yang disepakati: Laravel, PHP, Blade, Tailwind CSS, JavaScript, Vite, Composer, Node.js LTS, npm, MySQL, ArenHost Competent, GitHub, dan GitHub Actions.
- Migration v0.1 telah dibuat dan pemeriksaan sintaks PHP telah dilakukan. Migration belum diuji dengan menjalankan `php artisan migrate` pada database MySQL target.
- Versi PHP, Laravel, dan MySQL/MariaDB yang benar-benar tersedia pada paket ArenHost Competent harus dikonfirmasi sebelum memilih versi final dependency dan menjalankan migration.
