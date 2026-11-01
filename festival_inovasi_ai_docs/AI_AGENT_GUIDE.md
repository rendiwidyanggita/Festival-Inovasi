# AI Agent Guide — Festival Inovasi

Dokumen ini berisi instruksi bagi AI coding agent yang akan mengerjakan repository aplikasi.

## 1. Baca sebelum coding

Baca seluruh file berikut terlebih dahulu:
1. `PRD.md`
2. `BUSINESS_RULES.md`
3. `ERD.md`
4. `TECH_STACK_DEPLOYMENT.md`
5. `MIGRATION_PACKAGE_REFERENCE.md`

Paket migration PHP diberikan terpisah sebagai `festival_inovasi_laravel_migrations_v0_1.zip`.

## 2. Sumber kebenaran

- PRD menentukan apa yang harus dilakukan aplikasi.
- Business rules menentukan validasi dan perilaku yang tidak boleh dilanggar.
- ERD menentukan entitas, relasi, key, dan integritas data.
- Tech stack menentukan teknologi dan batasan deployment.
- Migration ZIP adalah implementasi schema awal yang harus dibandingkan dengan ERD sebelum dipakai.

Jangan mengubah keputusan bisnis atau schema secara diam-diam. Jika menemukan konflik atau kekurangan, tulis penjelasan, dampak, dan rekomendasi, lalu minta persetujuan pemilik proyek sebelum perubahan yang memengaruhi data.

## 3. Stack wajib

- Laravel untuk backend.
- PHP sebagai runtime production.
- Blade untuk rendering frontend.
- Tailwind CSS untuk styling.
- JavaScript untuk interaksi.
- Vite untuk build assets.
- Composer untuk dependencies PHP.
- Node.js LTS + npm untuk tooling frontend dan build.
- MySQL/InnoDB sebagai database.
- GitHub untuk version control.
- GitHub Actions untuk testing/build/deployment setelah metode deploy hosting dikonfirmasi.
- Target hosting awal: ArenHost Competent shared hosting.

Jangan mengganti stack ke Next.js, NestJS, PostgreSQL, atau serverless tanpa keputusan pemilik proyek.

## 4. Batasan deployment

- Laravel berjalan dengan PHP pada production.
- Node.js tidak perlu berjalan sebagai server aplikasi production.
- Build Vite dilakukan di lokal atau CI; deploy hasil `public/build`.
- Jangan menganggap SSH, cPanel Git, Node.js server, atau Composer di hosting tersedia sebelum dikonfirmasi.
- Domain document root harus mengarah ke folder `public` Laravel.
- Rahasia aplikasi tidak boleh masuk Git repository.
- Backup database di luar hosting harus tersedia sebelum migration production.

## 5. Aturan arsitektur aplikasi

- Gunakan Laravel conventions dan struktur yang mudah dipelihara.
- Pisahkan routing, controller, Form Request, Policy, model, dan service untuk business logic yang kompleks.
- Controller harus tipis; hitung nilai dan transisi status di service.
- Gunakan authorization di backend, bukan hanya menyembunyikan tombol di UI.
- Gunakan database transaction untuk proses yang mengubah beberapa tabel terkait.
- Gunakan Eloquent relationships dan eager loading untuk menghindari N+1 query.
- Gunakan pagination pada daftar peserta, juri, dan penilaian.
- Pertahankan audit trail.
- Jangan membuat ranking sebagai data permanen duplikat; hitung dari assessment terkunci, kecuali kemudian disetujui kebutuhan snapshot historis.

## 6. Aturan domain yang tidak boleh dilanggar

- Pengunjung anonim hanya read-only.
- Juri memilih kategori utama terlebih dahulu, kemudian subkategori.
- Juri boleh mengikuti beberapa subkategori.
- Setelah subkategori dibuka, daftar juri dan keanggotaan peserta dikunci.
- Semua objek dalam satu subkategori harus dinilai seluruh juri yang ada di roster subkategori tersebut.
- Roster berbeda antar-subkategori diperbolehkan.
- Juri baru tidak dapat bergabung setelah subkategori dibuka.
- Peserta tidak boleh ditambah/dihapus setelah subkategori dibuka.
- Nama peserta boleh diperbaiki dengan audit, tetapi relasi peserta tidak boleh diubah.
- Nilai kriteria integer 6–10.
- Bobot per jenis penilaian total 100%.
- Total juri dihitung dari nilai tertimbang; rata-rata antarjuri ditampilkan dua desimal.
- Hasil final hanya memakai penilaian lengkap dan terkunci.
- Pemenang dipilih manual oleh superadmin; jumlah pemenang diatur per subkategori.
- Publik tidak melihat nilai/peringkat sebelum publikasi manual.
- Instansi dan inovasi disimpan sebagai record terpisah setiap tahun.

## 7. Keamanan dan privasi

- Hash password menggunakan mekanisme yang didukung Laravel dan konfigurasi keamanan yang sesuai.
- Validasi semua input di server.
- Terapkan CSRF protection untuk form web.
- Gunakan authorization policy untuk setiap operasi superadmin/juri.
- Jangan mempercayai `user_id`, `festival_id`, `award_category_id`, atau `assessment_id` dari browser tanpa pemeriksaan akses dan relasi.
- Jangan expose endpoint penilaian kepada pengunjung.
- Jangan mengirim detail nilai ke halaman publik sebelum publikasi.
- Lindungi file laporan dan file internal.
- Catat perubahan administratif dan nilai.

## 8. Testing yang wajib

Buat feature tests untuk sekurang-kurangnya:
- Pengunjung tidak dapat mengubah data.
- Juri tidak dapat mengakses CRUD peserta atau pengaturan.
- Juri hanya dapat menilai subkategori yang diikuti.
- Juri tidak dapat bergabung setelah subkategori open.
- Penambahan/penghapusan peserta ditolak setelah open.
- Perbaikan field identitas diizinkan dan diaudit.
- Relasi peserta tidak dapat diubah setelah open.
- Nilai di luar 6–10 ditolak.
- Total bobot selain 100 ditolak.
- Assessment ganda ditolak.
- Assessment tidak lengkap tidak dapat disubmit/lock.
- Semua objek pada subkategori mempunyai roster juri yang sama.
- Juri tidak dapat mengubah assessment locked tanpa dibuka superadmin.
- Perhitungan weighted total dan average benar.
- Posisi pemenang tidak duplikat dan tidak melebihi winner_count.
- Nilai dan ranking tersembunyi sebelum publikasi.
- Data antarperiode tidak tercampur.
- Audit log mencatat perubahan penting.

## 9. Cara bekerja yang diharapkan

1. Periksa repository sebelum mengubah file.
2. Jangan menimpa migration yang sudah ada tanpa memeriksa status database dan kebutuhan proyek.
3. Buat perubahan kecil dan terukur.
4. Jalankan test dan lint yang tersedia.
5. Laporkan file yang diubah, migration yang ditambah, test yang dijalankan, hasilnya, dan hal yang belum diverifikasi.
6. Jangan mengklaim deployment, migration database, atau pengujian production berhasil jika belum benar-benar dilakukan.
7. Untuk operasi destruktif seperti `migrate:fresh`, `db:wipe`, penghapusan tabel, atau penghapusan data, minta persetujuan eksplisit terlebih dahulu.

## 10. Keluaran tiap tahap

- Database: migration, model, relasi, seeder.
- Backend: route, controller, Form Request, policy, service.
- Frontend: Blade views, Tailwind, JavaScript.
- Quality: feature/unit tests dan hasil test.
- Deployment: workflow CI/CD, konfigurasi hosting, dan dokumentasi rollback.
