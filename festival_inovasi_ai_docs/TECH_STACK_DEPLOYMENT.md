# Tech Stack & Deployment — Aplikasi Penilaian Festival Inovasi

**Status:** Tech stack disepakati.  
**Target hosting awal:** ArenHost Shared Hosting Competent.  
**Strategi:** mulai dengan paket Competent, ukur resource, lalu upgrade ke Expert jika hasil pengujian menunjukkan kebutuhan.

## 1. Tech stack yang disepakati

| Layer | Teknologi | Tanggung jawab |
|---|---|---|
| Backend | Laravel | Business logic, routing, autentikasi, validasi, authorization |
| Backend runtime | PHP | Menjalankan Laravel di production |
| Frontend | Blade | Server-rendered views |
| Styling | Tailwind CSS | Styling antarmuka |
| Interaksi | JavaScript | Interaksi komponen frontend |
| Asset bundler | Vite | Build CSS dan JavaScript |
| PHP dependencies | Composer | Install dan autoload package PHP |
| JavaScript runtime | Node.js LTS | Development dan build frontend |
| JavaScript package manager | npm | Mengelola package frontend |
| Database | MySQL / InnoDB | Penyimpanan relasional |
| Version control | Git + GitHub | Source code dan collaboration |
| CI/CD | GitHub Actions | Test, build, dan deployment automation |
| Hosting | ArenHost Competent | Hosting awal aplikasi |

Bahasa antarmuka adalah Bahasa Indonesia. Nama tabel dan kolom menggunakan bahasa Inggris dengan `snake_case`.

## 2. Mengapa Laravel + shared hosting

Pemilihan ini didasarkan pada prioritas menekan biaya awal dan kesesuaian teknologi dengan shared hosting yang tersedia.

- Laravel menyediakan routing, autentikasi, validasi request, middleware, Eloquent ORM, migration, queue, dan mekanisme keamanan aplikasi.
- Blade cukup untuk kebutuhan halaman publik dan dashboard. JavaScript digunakan pada bagian yang memerlukan interaksi tambahan.
- PHP adalah runtime production untuk aplikasi Laravel.
- Node.js dan npm digunakan untuk tooling frontend/Vite pada development atau CI build. Node.js tidak perlu berjalan sebagai server aplikasi di shared hosting untuk arsitektur Laravel + Blade ini.
- MySQL mengikuti layanan database yang tercantum pada paket shared hosting.
- GitHub Actions dapat menjalankan test dan build sebelum deployment.

## 3. Paket hosting awal

Spesifikasi berdasarkan tabel paket ArenHost yang diberikan pemilik proyek:

| Resource | Competent |
|---|---:|
| Harga 1 bulan | Rp7.000 |
| Harga 1 tahun | Rp69.000 |
| Domain | 1 |
| Storage | 2 GB NVMe |
| Bandwidth | Unlimited (sesuai klaim paket) |
| CPU | 1 Core (100%) |
| RAM | 1 GB |
| Database | Unlimited MySQL (sesuai tabel paket) |

Strategi biaya:
- Mulai dari Competent.
- Kembangkan dan uji aplikasi pada lingkungan lokal/staging.
- Pantau CPU, RAM, I/O, Entry Processes, PHP workers, dan koneksi database jika provider menyediakan metriknya.
- Upgrade ke Expert jika pengujian atau monitoring menunjukkan resource Competent tidak mencukupi.
- Jangan menunggu sampai festival resmi berjalan untuk melakukan upgrade.

Harga dan spesifikasi harus diperiksa kembali pada halaman checkout atau konfirmasi provider karena paket dapat berubah.

## 4. Runtime dan package manager

### 4.1 Production

Production menjalankan:
- PHP untuk aplikasi Laravel.
- MySQL untuk database.
- File JavaScript/CSS yang telah di-build di `public/build`.

Node.js/Bun tidak diperlukan sebagai runtime aplikasi production dalam arsitektur ini.

### 4.2 Development dan CI build

Gunakan:
- PHP versi yang kompatibel dengan Laravel yang dipilih.
- Composer untuk dependency PHP.
- Node.js LTS yang kompatibel dengan Vite dan package frontend.
- npm untuk dependency JavaScript.

Perintah standar:

```bash
composer install
npm ci
npm run dev
```

Build asset production:

```bash
npm run build
```

Jangan mengasumsikan Bun tersedia atau diperlukan. npm dipilih sebagai jalur standar agar sesuai dokumentasi Laravel/Vite dan meminimalkan risiko kompatibilitas.

## 5. Arsitektur deployment

### Development

```text
Developer workstation
├── Laravel / PHP
├── Composer dependencies
├── Node.js LTS + npm
├── Vite development server
└── MySQL local database
```

### Production

```text
ArenHost Competent
├── PHP runtime
├── Laravel application
├── Composer dependencies (vendor/)
├── public/ (document root)
│   └── build/ (compiled Vite assets)
└── MySQL database
```

Document root domain harus diarahkan ke direktori `public` Laravel. File `.env`, `vendor`, `storage`, dan source internal tidak boleh dapat diakses secara langsung dari web.

Jika konfigurasi document root tidak dapat diarahkan ke `public`, gunakan prosedur deployment aman yang disetujui provider; jangan sekadar menaruh seluruh root Laravel dalam `public_html` tanpa proteksi.

## 6. CI/CD yang direkomendasikan

Gunakan GitHub Actions dengan tahapan:

1. Trigger saat pull request dan push ke branch yang ditentukan.
2. Install dependency PHP dengan Composer.
3. Install dependency JavaScript dengan `npm ci`.
4. Jalankan pemeriksaan format/lint dan automated tests.
5. Jalankan `npm run build`.
6. Jika semua pemeriksaan berhasil dan commit berada pada branch production, deploy menggunakan metode yang didukung ArenHost.
7. Setelah deployment, jalankan migration secara terkontrol setelah backup dan pemeriksaan kompatibilitas.

Metode deployment ke ArenHost (SSH, cPanel Git, FTP/SFTP, atau metode lain) belum dikonfirmasi. Jangan mengasumsikan SSH tersedia pada Competent hanya karena paket Expert mencantumkan developer tools tertentu. Konfirmasi metode deployment dengan provider sebelum membuat workflow final.

Secrets seperti database password, deployment credentials, dan `APP_KEY` harus disimpan pada GitHub Actions Secrets atau environment secrets, bukan pada repository.

## 7. Environment production

Contoh variabel penting (nilai rahasia tidak boleh dimasukkan ke repository):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.example

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Gunakan `APP_KEY` yang unik. Aktifkan HTTPS/SSL. Jangan commit file `.env`.

## 8. Performance dan kapasitas

Bandwidth unlimited tidak berarti CPU, RAM, PHP workers, I/O, Entry Processes, atau database connections tidak terbatas.

Prioritas optimasi:
- Gunakan pagination pada tabel besar.
- Gunakan eager loading untuk menghindari N+1 queries.
- Tambahkan index pada foreign key dan kolom filter yang relevan.
- Gunakan cache untuk halaman publik yang jarang berubah.
- Revalidate/clear cache setelah perubahan data dan saat hasil dipublikasikan.
- Gunakan transaksi database untuk submit penilaian, pembukaan/penutupan subkategori, revisi/penguncian, penetapan pemenang, dan publikasi.
- Jangan membuat ulang PDF besar pada setiap request; simpan hasil yang dapat digunakan kembali jika sesuai kebutuhan.
- Optimalkan gambar dan batasi ukuran upload.
- Jalankan load test sebelum festival resmi.

## 9. Backup dan pemulihan

- Buat backup database secara rutin.
- Simpan salinan backup di luar akun shared hosting.
- Uji proses restore, bukan hanya proses backup.
- Backup penting dilakukan sebelum migration production dan perubahan data besar.
- Jangan mengandalkan backup otomatis provider sebagai satu-satunya salinan.

## 10. Hal yang masih harus dikonfirmasi ke ArenHost

- Versi PHP yang tersedia pada Competent.
- Versi MySQL/MariaDB yang tersedia.
- Batas CPU/RAM aktual dan cara memantaunya.
- Entry Processes / NPROC / PHP workers.
- Batas koneksi MySQL simultan.
- Batas I/O dan IOPS.
- Ketersediaan SSH, cPanel Git, atau SFTP/FTP untuk deployment.
- Batas ukuran upload dan konfigurasi PHP seperti `upload_max_filesize` serta `memory_limit`.
- Jadwal backup dan prosedur restore.
- Proses dan downtime saat upgrade Competent ke Expert.

## 11. Keputusan yang tidak digunakan

- Tidak menggunakan Vercel Hobby sebagai production hosting.
- Tidak menggunakan NestJS atau Next.js untuk arsitektur proyek saat ini.
- Tidak menggunakan PostgreSQL karena pilihan hosting awal adalah shared hosting dengan MySQL yang tercantum pada paket.
- Tidak menggunakan Bun sebagai package manager standar awal; Node.js LTS + npm dipilih untuk kompatibilitas dan kesederhanaan.

Perubahan terhadap poin-poin ini memerlukan keputusan pemilik proyek dan pembaruan dokumentasi.
