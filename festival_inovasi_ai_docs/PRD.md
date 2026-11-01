# Product Requirements Document (PRD)

# Aplikasi Penilaian Festival Inovasi

| Informasi | Detail |
|---|---|
| Nama Produk | Aplikasi Penilaian Festival Inovasi |
| Versi PRD | 1.0 |
| Status | Final — disetujui pemilik proyek |
| Jenis Produk | Website |
| Pengguna | Pengunjung, Superadmin, Juri |
| Periode | Tahunan, satu festival per tahun |
| Bahasa antarmuka | Bahasa Indonesia |
| Penamaan tabel/kolom | Bahasa Inggris, `snake_case` |

## 1. TL;DR

Aplikasi Penilaian Festival Inovasi adalah website untuk mengelola penyelenggaraan festival, mulai dari pengelolaan periode, kategori penghargaan, instansi, inovasi, akun juri, proses penilaian, hingga penetapan dan publikasi pemenang.

Sistem memiliki tiga jenis pengguna: pengunjung (read-only), superadmin, dan juri. Pengunjung dapat mengakses informasi festival tanpa login. Superadmin mengelola seluruh data dan konfigurasi aplikasi. Juri memberikan penilaian terhadap inovasi dan instansi sesuai kategori yang dipilih.

Kategori utama dan subkategori dapat dikelola superadmin untuk setiap periode. Juri bebas memilih kategori utama dan satu atau beberapa subkategori yang ingin dinilai. Setelah memilih subkategori, juri wajib menilai seluruh objek di dalam subkategori tersebut. Seorang juri dapat mengerjakan beberapa subkategori secara bertahap, dengan progres yang terpisah.

Setiap kriteria menggunakan nilai bilangan bulat 6–10 dengan bobot yang ditetapkan. Sistem menghitung total nilai tertimbang setiap juri, kemudian menghitung rata-rata antarjuri. Superadmin dapat melihat rincian nilai, mengunci atau membuka kembali penilaian, menentukan jumlah pemenang per subkategori, menetapkan pemenang secara manual, serta mencetak laporan PDF.

Data setiap tahun disimpan secara terpisah agar riwayat festival dan pemenang sebelumnya tetap tersedia.

## 2. Goals

### 2.1 Business Goals
1. Memusatkan pengelolaan data festival dalam satu aplikasi.
2. Mempermudah administrasi dan proses penilaian.
3. Menyediakan dokumentasi nilai setiap juri secara terperinci.
4. Mempermudah pemantauan penilaian dan penetapan pemenang.
5. Menyimpan arsip data dan hasil festival setiap tahun.
6. Menyediakan laporan hasil festival yang dapat dicetak dalam PDF.

### 2.2 User Goals
1. Superadmin dapat mengelola seluruh kebutuhan festival melalui dashboard.
2. Juri dapat memberikan penilaian sesuai kategori dan subkategori yang dipilih.
3. Pengunjung dapat mengakses informasi festival tanpa login.
4. Superadmin dapat memeriksa rincian nilai setiap kriteria dan progres masing-masing juri.
5. Superadmin dapat melihat peringkat dan menetapkan pemenang secara manual.
6. Superadmin dapat membuat laporan berdasarkan kategori dan periode.

### 2.3 Non-Goals
1. Registrasi peserta secara mandiri melalui website.
2. Penentuan pemenang otomatis tanpa keputusan superadmin.
3. Penentuan juara instansi berdasarkan jumlah inovasi.
4. Pembagian juri secara otomatis oleh sistem.
5. Integrasi dengan sistem eksternal yang belum ditentukan.

## 3. User Roles

### 3.1 Pengunjung (Read-Only)
Pengunjung adalah pengguna anonim dan tidak memerlukan akun.

Hak akses:
- Melihat informasi festival.
- Melihat kategori utama dan subkategori.
- Melihat daftar instansi dan inovasi yang mengikuti festival.
- Melihat status pelaksanaan festival.
- Setelah hasil dipublikasikan, melihat daftar peserta, total nilai, peringkat, dan pemenang.
- Tidak dapat menambah, mengubah, atau menghapus data.

### 3.2 Superadmin
Superadmin mempunyai kewenangan administratif tertinggi.

Hak akses:
- Mengelola periode festival.
- Mengelola kategori utama dan subkategori.
- Mengelola instansi dan inovasi.
- Mengelola akun juri dan keikutsertaan juri dalam periode.
- Mengatur kriteria dan bobot sebelum penilaian dimulai.
- Melihat partisipasi juri pada setiap subkategori.
- Membuka dan menutup penilaian subkategori.
- Memantau progres dan rincian seluruh nilai.
- Membuka kembali penilaian tertentu untuk revisi.
- Mengunci penilaian.
- Mengatur jumlah pemenang per subkategori.
- Menetapkan pemenang secara manual.
- Membuat dan mengunduh laporan PDF.
- Mempublikasikan hasil.
- Melihat arsip festival sebelumnya.

### 3.3 Juri
Juri merupakan pengguna terautentikasi yang bertugas memberikan penilaian.

Hak akses:
- Login dengan akun pribadi.
- Memilih periode festival yang diikuti jika lebih dari satu periode tersedia.
- Memilih kategori utama.
- Memilih satu atau beberapa subkategori sebelum subkategori dibuka.
- Melihat seluruh objek dalam subkategori yang dipilih.
- Menilai seluruh objek di subkategori yang diikuti.
- Menyimpan penilaian secara bertahap dan mengirimkannya.
- Melihat progres setiap subkategori yang diikuti.
- Merevisi penilaian apabila superadmin membukanya kembali.
- Tidak dapat mengelola data peserta, kategori, kriteria, bobot, atau pemenang.

## 4. Functional Requirements

### 4.1 Authentication & Role Management — High
- Login tersedia untuk superadmin dan juri.
- Pengunjung tidak memerlukan login.
- Role akun terautentikasi adalah `superadmin` atau `jury`.
- Setiap juri mempunyai akun sendiri.
- Akun juri dapat digunakan kembali pada periode festival berikutnya.
- Superadmin dapat menambah, mengubah, mengaktifkan, menonaktifkan, atau melakukan soft delete akun juri.
- Riwayat penilaian tetap tersimpan ketika akun juri dinonaktifkan atau di-soft-delete.
- Password harus disimpan dalam bentuk hash.

### 4.2 Pengelolaan Periode Festival — High
- Superadmin dapat membuat satu periode festival untuk setiap tahun kalender.
- Tahun festival harus unik.
- Setiap periode mempunyai identitas dan konfigurasi tersendiri.
- Kategori, subkategori, peserta, kriteria, penilaian, dan pemenang terikat pada periode terkait.
- Data instansi dan inovasi dibuat sebagai record baru pada periode berikutnya; record tahun sebelumnya tetap disimpan.
- Superadmin dapat memilih periode yang dikelola dan melihat arsip periode sebelumnya.
- Data antarperiode tidak boleh tercampur.

### 4.3 Pengelolaan Kategori Festival — High
Kategori utama dan subkategori tidak dikunci secara permanen. Superadmin dapat mengelolanya untuk setiap periode.

Kategori awal berdasarkan dokumen sumber:

**ASN TERINOVATIF**
1. ASN TERINOVATIF DIGITAL OPD.
2. ASN TERINOVATIF NON DIGITAL OPD.
3. ASN TERINOVATIF DIGITAL UNIT.
4. ASN TERINOVATIF NON DIGITAL UNIT.
5. ASN TERINOVATIF SEKOLAH TK.
6. ASN TERINOVATIF SEKOLAH SD.
7. ASN TERINOVATIF SEKOLAH SMP.

**LEMBAGA/INSTANSI TERINOVATIF**
1. LEMBAGA TERINOVATIF KELURAHAN.
2. LEMBAGA TERINOVATIF OPD.
3. LEMBAGA TERINOVATIF PUSKESMAS.
4. LEMBAGA TERINOVATIF TK.
5. LEMBAGA TERINOVATIF SD.
6. LEMBAGA TERINOVATIF SMP.

Ketentuan:
- Superadmin dapat menambah, mengubah nama, mengurutkan, dan menghapus kategori yang belum memiliki data terkait.
- Setiap kategori utama mempunyai `assessment_type`: `innovation` atau `institution`.
- Setiap subkategori berada di bawah tepat satu kategori utama.
- Juri hanya melihat kategori/subkategori pada periode yang dipilih.
- Nama kategori dan subkategori boleh diubah setelah penilaian dimulai, tetapi ID, relasi parent-child, dan jenis penilaian tidak boleh berubah.

### 4.4 Master Data Instansi — High
Data awal mengikuti dokumen sumber:
- ID instansi.
- Kategori juara instansi.
- Nama instansi.

Ketentuan:
- Satu subkategori instansi memiliki banyak instansi.
- Setiap instansi terhubung dengan tepat satu subkategori instansi.
- Satu instansi dapat memiliki banyak inovasi.
- Data instansi dibuat terpisah untuk setiap periode festival.

### 4.5 Master Data Inovasi — High
Data awal mengikuti dokumen sumber:
- ID inovasi.
- Kategori juara ASN.
- Nama inovasi.
- Inovator.
- Instansi.

Ketentuan:
- Setiap inovasi memiliki tepat satu subkategori ASN Terinovatif.
- Setiap inovasi terhubung dengan tepat satu instansi dalam periode festival yang sama.
- Setiap inovasi hanya memiliki satu inovator.
- Kategori inovasi tidak harus sama dengan kategori instansi tempat inovasi terdaftar.

### 4.6 Pembatasan Perubahan Data Peserta — High
Penguncian dilakukan per subkategori.

Sebelum penilaian pada subkategori dimulai:
- Superadmin dapat menambah, mengubah, menghapus peserta, dan mengatur relasinya.

Setelah penilaian pada subkategori dimulai:
- Peserta tidak boleh ditambah atau dihapus pada subkategori tersebut.
- Identitas boleh diperbaiki, termasuk nama instansi, nama inovasi, dan nama inovator.
- Relasi peserta dengan instansi dan subkategori tidak boleh diubah.
- Perubahan identitas harus dicatat pada audit log.
- Data subkategori lain yang belum dibuka tetap dapat dikelola.

### 4.7 Pengelolaan Juri — High
- Superadmin mengelola akun juri.
- Akun juri dapat digunakan kembali lintas periode.
- Superadmin menentukan juri aktif pada periode festival melalui relasi periode-juri.
- Juri memilih subkategori sendiri sebelum subkategori dibuka.
- Identitas juri tersimpan pada setiap penilaian.
- Soft delete tidak menghapus penilaian historis.

### 4.8 Kriteria dan Bobot Penilaian — High

**Penilaian Inovasi**

| Kriteria | Bobot |
|---|---:|
| Kemampuan Presentasi | 20% |
| Orisinalitas Ide | 30% |
| Manfaat dan Dampak | 20% |
| Proses Transfer Knowledge | 30% |
| **Total** | **100%** |

**Penilaian Instansi**

| Kriteria | Bobot |
|---|---:|
| Kemampuan Presentasi | 20% |
| Managerial | 30% |
| Komitmen | 30% |
| Strategi dan Mitigasi | 20% |
| **Total** | **100%** |

Ketentuan:
- Nilai input setiap kriteria adalah bilangan bulat 6–10.
- Total bobot untuk setiap festival dan jenis penilaian harus tepat 100%.
- Kriteria dan bobot dikelola superadmin.
- Kriteria dan bobot yang sudah digunakan tidak dapat diubah setelah penilaian terkait dimulai.
- Perubahan konfigurasi pada periode berikutnya tidak mengubah hasil historis.

### 4.9 Pemilihan Kategori dan Subkategori oleh Juri — High
- Alur UI: pilih periode → pilih kategori utama → pilih subkategori.
- Juri bebas memilih salah satu atau kedua kategori utama.
- Juri dapat memilih beberapa subkategori selama statusnya masih `preparation`.
- Setelah memilih subkategori, juri wajib menilai seluruh objek di dalamnya.
- Juri dapat mengerjakan beberapa subkategori secara bertahap.
- Progres setiap subkategori dicatat secara terpisah.
- Subkategori tidak dapat dinyatakan selesai jika masih ada objek yang belum dinilai lengkap.

### 4.10 Penilaian Inovasi — High
- Juri memilih kategori utama ASN Terinovatif, lalu subkategori.
- Sistem menampilkan seluruh inovasi pada subkategori.
- Juri wajib menilai seluruh inovasi dalam subkategori yang dipilih.
- Juri dapat menyimpan draft secara bertahap.
- Setiap kriteria menerima nilai bilangan bulat 6–10.
- Sistem menyimpan juri, inovasi, periode, status, dan setiap nilai kriteria.
- Satu juri tidak boleh membuat dua penilaian untuk inovasi yang sama dalam satu partisipasi/periode.

### 4.11 Penilaian Instansi — High
- Juri memilih kategori utama Lembaga/Instansi Terinovatif, lalu subkategori.
- Sistem menampilkan seluruh instansi pada subkategori.
- Juri wajib menilai seluruh instansi di subkategori yang dipilih.
- Setiap kriteria menerima nilai bilangan bulat 6–10.
- Sistem menyimpan juri, instansi, periode, status, dan nilai setiap kriteria.
- Jumlah inovasi dalam instansi bukan metrik penilaian dan bukan dasar penentuan juara instansi.
- Satu juri tidak boleh membuat dua penilaian untuk instansi yang sama dalam satu partisipasi/periode.

### 4.12 Pemerataan dan Penguncian Daftar Juri — High
- Juri bebas memilih subkategori ketika masih `preparation`.
- Superadmin melihat daftar juri yang memilih setiap subkategori.
- Ketika superadmin membuka subkategori, daftar juri yang telah memilih dikunci.
- Juri baru tidak dapat bergabung ke subkategori yang sudah `open`.
- Semua objek pada satu subkategori dinilai oleh daftar juri yang sama.
- Jumlah juri antar-subkategori boleh berbeda.
- Juri wajib menilai seluruh objek dalam subkategori yang diikuti.
- Sistem menampilkan partisipasi dan progres juri.
- Tidak ditentukan jumlah minimum juri per subkategori.
- Jika juri berhalangan setelah daftar dikunci, kasus ditangani manual oleh superadmin sebagai pengecualian operasional. Tidak ada mekanisme penggantian otomatis pada versi awal.

### 4.13 Perhitungan Nilai dan Peringkat — High

Tahap 1 — Total tertimbang per juri:

`total_jury_score = SUM(score * weight) / 100`

Contoh penilaian inovasi:
- Kemampuan Presentasi: 8 × 20%.
- Orisinalitas Ide: 9 × 30%.
- Manfaat dan Dampak: 8 × 20%.
- Proses Transfer Knowledge: 9 × 30%.
- Total tertimbang juri = 8,60.

Tahap 2 — Rata-rata antarjuri:

`average_score = SUM(jury_total_scores) / COUNT(valid_jury_assessments)`

Contoh:

| Juri | Total nilai |
|---|---:|
| Juri 1 | 8,60 |
| Juri 2 | 8,90 |
| Juri 3 | 8,70 |
| **Rata-rata** | **8,73** |

Ketentuan:
- Nilai input tetap integer 6–10.
- Total tertimbang dan rata-rata dapat berupa desimal.
- Tampilan total dan rata-rata menggunakan dua angka di belakang koma.
- Perhitungan internal menggunakan presisi memadai sebelum pembulatan tampilan.
- Peringkat diurutkan dari rata-rata tertinggi ke terendah.
- Hasil peringkat final memakai penilaian lengkap dan terkunci.
- Jumlah juri pada seluruh objek dalam subkategori yang sama harus sama.
- Pemenang tetap ditentukan manual oleh superadmin.

### 4.14 Monitoring Penilaian — High
Superadmin dapat melihat:
- Daftar juri dan subkategori yang diikuti.
- Daftar inovasi dan instansi yang dinilai.
- Jumlah penilaian yang selesai.
- Progres setiap juri.
- Status setiap penilaian.
- Nilai setiap kriteria dan bobotnya.
- Total nilai tertimbang per juri.
- Rata-rata nilai per objek.

Filter: periode, kategori utama, subkategori, juri, inovasi, instansi, dan status.

### 4.15 Penguncian dan Revisi Penilaian — High
Status penilaian:
- `draft`: penilaian sedang diisi dan belum dikirim.
- `submitted`: penilaian lengkap telah dikirim.
- `locked`: penilaian dikunci superadmin.
- `revision_requested`: penilaian dibuka kembali untuk revisi.

Ketentuan:
- Juri dapat menyimpan draft dan mengubahnya sebelum dikunci.
- Juri tidak dapat mengubah penilaian yang terkunci.
- Superadmin dapat membuka kembali penilaian tertentu.
- Juri terkait memperbaiki nilai dan mengirimkannya kembali.
- Superadmin mengunci kembali penilaian.
- Riwayat revisi menyimpan nilai lama, nilai baru, waktu, dan pelaku perubahan.

### 4.16 Penetapan Pemenang — High
- Sistem menyediakan hasil inovasi dan instansi secara terpisah.
- Sistem menampilkan rata-rata nilai dan peringkat.
- Pemenang ditentukan manual oleh superadmin.
- Pemenang inovasi ditetapkan per subkategori ASN.
- Pemenang instansi ditetapkan per subkategori Lembaga/Instansi.
- Superadmin menentukan jumlah pemenang setiap subkategori.
- Jumlah pemenang dapat berbeda antar-subkategori dan antarperiode.
- Superadmin menetapkan posisi Juara 1, Juara 2, Juara 3, dan seterusnya sesuai konfigurasi.
- Jumlah inovasi di instansi bukan dasar penetapan juara instansi.
- Sistem menyimpan festival, subkategori, objek pemenang, posisi, superadmin penetap, waktu, dan catatan opsional.

Penanganan nilai seri:
1. Superadmin memeriksa hasil.
2. Superadmin membuka kembali penilaian tertentu.
3. Juri terkait memperbaiki nilai setelah pembahasan bersama.
4. Sistem menyimpan riwayat revisi.
5. Penilaian dikirim dan dikunci kembali.
6. Superadmin menetapkan pemenang berdasarkan hasil terbaru.

### 4.17 Laporan dan Ekspor PDF — High
Format awal laporan:

**Header:**
- Logo penyelenggara.
- Nama instansi penyelenggara.
- Nama festival.
- Tahun.
- Tanggal cetak.

**Isi:**
- Kategori utama.
- Subkategori.
- Daftar peserta.
- Nama inovasi atau instansi.
- Nama inovator pada laporan inovasi.
- Total nilai.
- Peringkat.
- Keterangan pemenang dan posisi juara.

Superadmin memfilter berdasarkan periode, kategori utama, dan subkategori. PDF tidak memuat detail seluruh kriteria per juri pada format awal. Format PDF dapat direvisi pada versi kebutuhan berikutnya.

### 4.18 Publikasi dan Halaman Pengunjung — High
- Website dapat diakses tanpa login.
- Sebelum publikasi, pengunjung dapat melihat kategori, subkategori, instansi, dan inovasi peserta.
- Selama penilaian, halaman menampilkan informasi bahwa penilaian berlangsung atau hasil belum diumumkan.
- Sebelum publikasi, nilai dan peringkat tidak ditampilkan.
- Hasil tidak dipublikasikan otomatis.
- Superadmin memublikasikan hasil secara manual.
- Setelah publikasi, pengunjung dapat melihat daftar peserta, total nilai, peringkat, dan pemenang.
- Pengunjung selalu memiliki akses read-only.

## 5. User Experience

### 5.1 Pengunjung
1. Membuka website tanpa login.
2. Melihat informasi festival.
3. Melihat kategori, subkategori, instansi, dan inovasi peserta.
4. Melihat pemberitahuan selama penilaian berlangsung.
5. Melihat total nilai, peringkat, dan pemenang setelah publikasi.

### 5.2 Superadmin
1. Login.
2. Memilih atau membuat periode festival.
3. Mengatur kategori utama dan subkategori.
4. Memasukkan data instansi dan inovasi.
5. Mengelola akun juri dan keikutsertaan juri dalam periode.
6. Mengelola kriteria dan bobot.
7. Melihat daftar juri yang memilih subkategori.
8. Membuka subkategori setelah persiapan selesai.
9. Memantau progres dan detail penilaian.
10. Membuka kembali penilaian jika perlu revisi.
11. Mengunci penilaian.
12. Melihat rata-rata dan peringkat.
13. Mengatur jumlah pemenang dan menetapkan juara manual.
14. Membuat laporan PDF.
15. Memublikasikan hasil.

### 5.3 Juri
1. Login.
2. Memilih periode festival.
3. Memilih kategori utama: ASN atau Lembaga/Instansi.
4. Memilih satu atau beberapa subkategori selama status masih persiapan.
5. Melihat seluruh objek pada subkategori yang dipilih.
6. Memberikan nilai untuk seluruh objek.
7. Menyimpan draft atau mengirim penilaian.
8. Melihat progres setiap subkategori.
9. Melakukan revisi jika superadmin membuka kembali penilaian.

### 5.4 UI/UX Highlights
- Dashboard berbeda berdasarkan role.
- Superadmin memiliki pemilih periode.
- Juri memiliki navigasi berurutan kategori utama lalu subkategori.
- Jumlah objek dan progres ditampilkan per subkategori.
- Form penilaian menampilkan kriteria dan bobot secara jelas.
- Monitoring superadmin menampilkan nilai per juri dan per objek.
- Tabel menyediakan pencarian, filter, dan pagination.
- Halaman publik menampilkan status festival dan hasil sesuai status publikasi.
- Tampilan responsif untuk desktop dan perangkat mobile.

## 6. Technical Considerations

### 6.1 Kebutuhan teknis
Aplikasi menggunakan Laravel dengan server-rendered Blade, Tailwind CSS, JavaScript, dan Vite. Data disimpan pada MySQL. Detail stack dan hosting dijelaskan pada `TECH_STACK_DEPLOYMENT.md`.

### 6.2 Entitas database utama
- `users`
- `password_reset_tokens`
- `festivals`
- `festival_settings`
- `festival_categories`
- `award_categories`
- `institutions`
- `innovations`
- `festival_juries`
- `jury_subcategory_participations`
- `assessment_criteria`
- `innovation_assessments`
- `innovation_assessment_details`
- `institution_assessments`
- `institution_assessment_details`
- `innovation_winners`
- `institution_winners`
- `audit_logs`

Detail kolom, relasi, key, dan constraints terdapat pada `ERD.md`.

### 6.3 Data storage & privacy
- Password disimpan dalam bentuk hash.
- Pengunjung tidak memiliki akses administratif.
- Juri hanya mempunyai hak penilaian.
- Superadmin memiliki akses pengelolaan.
- Nilai tidak ditampilkan ke publik sebelum publikasi.
- Data historis harus dipertahankan.
- Penonaktifan akun juri tidak menghilangkan riwayat penilaian.
- Perubahan nilai dan identitas peserta harus tercatat.

### 6.4 Scalability & performance
- Mendukung beberapa periode festival.
- Mendukung banyak kategori, instansi, inovasi, dan juri sesuai kapasitas hosting.
- Gunakan indeks untuk foreign key dan kolom filter yang relevan.
- Gunakan pagination dan eager loading untuk tabel data.
- Hitung nilai berdasarkan periode dan subkategori.
- Gunakan cache untuk halaman publik yang datanya tidak sering berubah, dan invalidasi saat data/publikasi berubah.

### 6.5 Potential challenges
- Konsistensi jumlah juri di setiap subkategori.
- Penilaian belum lengkap.
- Penguncian dan revisi nilai.
- Perhitungan bobot dan rata-rata.
- Penanganan nilai seri.
- Pemisahan data antarperiode.
- Pemeliharaan histori akun juri.
- Keterbatasan resource shared hosting.

## 7. Acceptance Criteria
1. Pengunjung dapat membuka website tanpa login.
2. Superadmin dapat membuat dan mengelola periode festival.
3. Superadmin dapat mengelola kategori utama dan subkategori per periode.
4. Superadmin dapat mengelola instansi dan inovasi sebelum subkategori dibuka.
5. Akun juri dapat digunakan kembali pada periode berbeda.
6. Juri dapat memilih kategori utama terlebih dahulu, kemudian satu atau beberapa subkategori.
7. Juri wajib menilai seluruh objek pada subkategori yang dipilih.
8. Setiap objek dalam satu subkategori dinilai oleh daftar juri yang sama.
9. Daftar juri dikunci ketika subkategori dibuka.
10. Nilai input dibatasi pada bilangan bulat 6–10.
11. Total tertimbang per juri dihitung berdasarkan bobot.
12. Rata-rata antarjuri dihitung dengan benar dan ditampilkan dua angka desimal.
13. Superadmin dapat melihat seluruh detail nilai per juri.
14. Superadmin dapat membuka kembali penilaian tertentu dan melihat riwayat revisi.
15. Peserta tidak dapat ditambah atau dihapus setelah subkategori dimulai.
16. Identitas peserta dapat diperbaiki dengan audit log.
17. Relasi peserta dan konfigurasi bobot yang sudah digunakan tidak dapat diubah setelah penilaian dimulai.
18. Superadmin dapat menentukan jumlah pemenang per subkategori dan menetapkannya secara manual.
19. Laporan PDF dapat difilter berdasarkan periode dan kategori.
20. Sebelum publikasi, pengunjung melihat data peserta tetapi tidak melihat nilai/peringkat.
21. Setelah publikasi, pengunjung dapat melihat peserta, total nilai, peringkat, dan pemenang.
22. Data festival sebelumnya tetap tersedia.

## 8. Milestones & Sequencing

### Phase 1 — Requirement & Database Design
- Finalisasi kebutuhan.
- Finalisasi kategori dan aturan penilaian.
- Finalisasi ERD, relasi, dan constraints.

### Phase 2 — Authentication & Master Data
- Login dan role.
- Pengelolaan periode festival.
- Pengelolaan kategori.
- Pengelolaan instansi dan inovasi.
- Pengelolaan akun juri.

### Phase 3 — Scoring System
- Pemilihan subkategori oleh juri.
- Form penilaian inovasi dan instansi.
- Pengelolaan partisipasi juri.
- Perhitungan bobot dan rata-rata.
- Monitoring, penguncian, dan revisi.

### Phase 4 — Reporting & Winner Management
- Tabel hasil dan peringkat.
- Konfigurasi jumlah pemenang.
- Penetapan pemenang.
- Ekspor PDF.
- Publikasi.

### Phase 5 — Testing & Deployment
- Pengujian role.
- Pengujian penilaian dan pemerataan juri.
- Pengujian revisi.
- Pengujian laporan dan arsip tahunan.
- Deployment.

## 9. Scope Boundary & Change Management

PRD v1.0 menjadi acuan kebutuhan produk. Perubahan berikutnya dicatat dalam versi PRD baru.

- Perubahan fungsional harus didokumentasikan.
- Perubahan database dievaluasi terhadap data historis dan relasi.
- Perubahan bobot/kriteria dievaluasi terhadap hasil penilaian.
- Perubahan kategori dievaluasi terhadap konfigurasi periode.
- Format PDF dapat diperbarui sesuai kebutuhan penyelenggara.
- Implementasi teknis tidak boleh mengubah aturan bisnis yang telah disepakati tanpa persetujuan pemilik proyek.
