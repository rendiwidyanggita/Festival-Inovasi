# 🏆 Festival Inovasi

Aplikasi web manajemen **Festival & Kompetisi Inovasi** (ASN Terinovatif & Lembaga/Instansi Terinovatif) — dari pengelolaan peserta, penugasan dewan juri, penilaian tertimbang, hingga penetapan pemenang dan publikasi hasil ke publik.

Dibangun dengan **Laravel 13** (PHP 8.3+) dan frontend **Tailwind CSS 4 + Alpine.js**.

---

## ✨ Fitur Utama

| Modul | Deskripsi |
|---|---|
| 🏠 **Portal Publik** | Landing page, detail festival & subkategori, hasil juara tampil setelah dipublikasi |
| 🗓️ **Manajemen Festival** | Periode festival per tahun, kategori utama (Inovasi/Instansi) & subkategori dengan kuota juara |
| 🏢 **Master Data** | Instansi, inovasi (beserta innovator), kriteria & bobot penilaian (total wajib 100%) |
| 👥 **Manajemen Juri** | Akun juri (soft delete + restore), penugasan juri per festival, aktif/nonaktif |
| ✍️ **Pemilihan Subkategori** | Juri memilih subkategori yang akan dinilai (roster terkunci saat penilaian dibuka) |
| 🧮 **Penilaian Tertimbang** | Juri memberi nilai bulat 6–10 per kriteria; total = Σ(nilai × bobot) ÷ 100; draft & kirim final |
| 📊 **Monitoring Real-Time** | Matriks progres per juri × per objek; admin dapat **mengunci** (menyahkan) atau **meminta revisi** penilaian dengan catatan |
| 🏅 **Penetapan Pemenang** | Ranking otomatis (rata-rata antar-juri dari penilaian terkunci, skor sama = peringkat seri), validasi kuota & posisi |
| 📄 **Laporan & Publikasi** | Preview + unduh PDF per subkategori; publikasi hasil ke halaman publik |
| 🧾 **Audit Trail** | Semua aksi penting tercatat di `audit_logs` (aktor, nilai lama/baru, IP) |

### Alur Kerja (Lifecycle)

```
Persiapan ──▶ Buka Penilaian ──▶ Juri Menilai ──▶ Kunci/Revisi ──▶ Tutup Penilaian
                                                                  ──▶ Tetapkan Pemenang ──▶ Publikasi
```

1. **Persiapan** — admin menyiapkan data; juri memilih subkategori (status `preparation`).
2. **Buka Penilaian** — roster juri terkunci; juri mulai menilai (draft → kirim final).
3. **Monitoring** — admin memantau progres, mengunci penilaian yang disahkan, atau meminta revisi (alasan tampil di form juri). *Hanya penilaian terkunci yang masuk perhitungan ranking.*
4. **Tutup Penilaian** — tidak ada lagi perubahan nilai (dijaga backend).
5. **Pemenang & Publikasi** — tetapkan juara (maks kuota, posisi unik), publikasikan hasil.

---

## 🧰 Prasyarat

| Tools | Versi | Catatan |
|---|---|---|
| PHP | **>= 8.3** (dikembangkan di 8.5) | ekstensi: `pdo_mysql`, `mbstring`, `dom`, `xml`, `gd` (render PDF), `openssl`, `curl` |
| Composer | 2.x | |
| Node.js | **>= 20.19** | untuk Vite 8 (build asset Tailwind/Alpine) |
| MySQL | 8.x / MariaDB 10.11+ | default; bisa diganti DB lain yang didukung Laravel |

---

## 🚀 Instalasi (dari Clone sampai Jalan)

### 1. Clone repository

```bash
git clone https://github.com/rendiwidyanggita/Festival-Inovasi.git
cd Festival-Inovasi
```

### 2. Instal dependensi

```bash
composer install
npm install
```

### 3. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Lalu edit `.env` — bagian yang **wajib** disesuaikan:

```dotenv
# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=festival_inovasi       # buat dulu: CREATE DATABASE festival_inovasi;
DB_USERNAME=root                   # ganti sesuai user MySQL Anda
DB_PASSWORD=

# URL aplikasi
APP_URL=http://localhost:8000

# Kredensial superadmin awal — WAJIB diisi sebelum seeding
SUPERADMIN_NAME="Super Admin"
SUPERADMIN_EMAIL="admin@festival-inovasi.local"
SUPERADMIN_PASSWORD="admin123"     # gunakan password kuat untuk produksi!

# Opsional: tahun festival yang dibuat seeder (default: tahun berjalan)
#FESTIVAL_YEAR=2026

# Opsional: nama penyelenggara pada pengaturan festival
#ORGANIZER_NAME="Panitia Festival Inovasi"
```

> ⚠️ Kredensial superadmin diambil oleh `SuperadminSeeder` saat `db:seed`. Nilai asli jangan pernah di-commit.

### 4. Migrasi & seed database

```bash
php artisan migrate --seed
```

Seeder yang berjalan: superadmin awal, festival (tahun berjalan), kategori & 13 subkategori, serta 4 kriteria penilaian per tipe sesuai PRD (bobot total 100%).

### 5. (Opsional, untuk demo) Data contoh lengkap

```bash
php artisan db:seed --class=DemoSeeder
```

Mengisi: **5 akun juri**, instansi & inovasi di tiap subkategori, partisipasi juri, dan **2 subkategori dengan status penilaian terbuka** yang sudah berisi nilai contoh.

### 6. Build frontend & jalankan

```bash
npm run build          # sekali (produksi / tanpa hot-reload)
php artisan serve      # server: http://localhost:8000
```

Atau untuk pengembangan (build otomatis saat file berubah):

```bash
npm run dev            # Vite dev server
php artisan serve      # di terminal lain
```

Buka **http://localhost:8000** 🎉

---

## 🔑 Akun Login

| Role | Email | Password |
|------|-------|----------|
| Superadmin | `admin@festival-inovasi.local` | `admin123` *(dari `SUPERADMIN_*` di .env)* |
| Juri 1 – Juri 5 *(khusus DemoSeeder)* | `juri1@festivalinovasi.test` … `juri5@festivalinovasi.test` | `password` |

---

## 🧪 Menjalankan Test

```bash
composer test          # atau: php artisan test
```

Suite mencakup: skema database, autentikasi & role, akses publik, alur scoring (draft → submit → kunci → revisi), laporan/PDF, dan smoke test end-to-end.

---

## 🗂️ Struktur Penting

```
app/
├── Enums/            # UserRole, FestivalStatus, ScoringStatus, AssessmentStatus, ...
├── Http/Controllers/ # Admin/, Jury/, Auth/, PublicController
├── Models/           # Festival, AwardCategory, InnovationAssessment, ...
├── Policies/         # Otorisasi (roster terkunci, hapus master data, dll.)
└── Services/         # ScoringService, RankingService, WinnerService, ReportService, AuditService
database/seeders/     # Superadmin, Festival, Category, AssessmentCriterion, Demo
routes/web.php        # Semua route (publik, admin, juri)
resources/views/      # Blade + Tailwind 4 + Alpine
```

Dokumen bisnis (PRD, ERD, aturan bisnis) tersedia di [`festival_inovasi_ai_docs/`](./festival_inovasi_ai_docs/).

---

## 🛠️ Troubleshooting

| Masalah | Solusi |
|---|---|
| `Unable to locate file in Vite manifest` | Jalankan `npm run build` (atau `npm run dev` saat pengembangan) |
| Seeder gagal: `SUPERADMIN_EMAIL ... harus diisi` | Isi `SUPERADMIN_*` di `.env` sebelum `db:seed` |
| Gagal koneksi DB | Pastikan MySQL berjalan, database sudah dibuat, kredensial `.env` benar |
| Halaman admin redirect "pilih festival" | Belum ada festival — buat di menu **Festival**, atau jalankan seeder |
| Error 429 saat login | Rate limit 5 percobaan/menit — tunggu sebentar |
| PDF kosong/gagal | Pastikan ekstensi PHP `dom`, `xml`, `gd` terpasang |

---

## 🔒 Catatan Keamanan (Produksi)

- Set `APP_DEBUG=false` dan gunakan `APP_ENV=production`.
- Selalu gunakan HTTPS; set `SESSION_SECURE_COOKIE=true`.
- Ganti kredensial default superadmin & MySQL.
- Hapus/komentari kredensial demo sebelum deploy.

---

## 📄 Lisensi

Aplikasi ini open-sourced di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
