# Business Rules — Aplikasi Penilaian Festival Inovasi

Dokumen ini merangkum aturan bisnis yang wajib diterapkan pada backend Laravel, database, dan UI. Aturan ini melengkapi `PRD.md` dan `ERD.md`.

## 1. Identitas dan akses pengguna

- Role akun terautentikasi hanya `superadmin` dan `jury`.
- Pengunjung tidak perlu akun dan selalu read-only.
- Juri menggunakan akun yang sama lintas periode festival.
- Keikutsertaan juri dalam periode dicatat secara terpisah.
- Soft delete/nonaktif akun tidak boleh menghapus histori penilaian.
- Superadmin mempunyai hak administratif; juri hanya mempunyai hak penilaian.

## 2. Periode festival

- Festival diselenggarakan tahunan; satu record festival untuk satu tahun.
- Tahun festival unik.
- Data kategori, subkategori, instansi, inovasi, kriteria, penilaian, dan pemenang terikat ke periode.
- Instansi dan inovasi dibuat sebagai record baru pada periode baru.
- Histori periode lama harus tetap tersedia.

## 3. Kategori dan peserta

- Kategori utama memiliki `assessment_type` `innovation` atau `institution`.
- Satu kategori utama mempunyai banyak subkategori.
- Setiap subkategori hanya memiliki satu kategori utama.
- Subkategori inovasi berisi inovasi; subkategori instansi berisi instansi.
- Satu instansi memiliki banyak inovasi.
- Satu inovasi terhubung ke tepat satu instansi pada periode yang sama.
- Satu inovasi memiliki tepat satu inovator.
- Kategori inovasi tidak harus sama dengan kategori instansi pemiliknya.

## 4. Pemilihan dan partisipasi juri

- UI meminta juri memilih periode, lalu kategori utama, lalu subkategori.
- Juri bebas memilih salah satu atau kedua kategori utama.
- Juri boleh memilih beberapa subkategori saat statusnya masih `preparation`.
- Juri yang memilih suatu subkategori wajib menilai semua objek dalam subkategori tersebut.
- Juri dapat mengerjakan beberapa subkategori secara bertahap.
- Progres setiap subkategori terpisah.
- Superadmin melihat roster juri sebelum membuka subkategori.
- Saat subkategori dibuka, roster juri yang sudah memilih dikunci.
- Juri baru tidak dapat bergabung setelah subkategori berstatus `open`.
- Semua objek dalam satu subkategori dinilai oleh roster juri yang sama; jumlah penilai antar-subkategori boleh berbeda.
- Tidak ditentukan batas minimum jumlah juri.
- Jika juri berhalangan setelah roster dikunci, penanganan merupakan pengecualian manual oleh superadmin dengan audit. Tidak ada pergantian otomatis pada versi awal.

## 5. Pembukaan subkategori dan penguncian peserta

Status subkategori:
- `preparation`: data peserta dan roster juri masih dapat disiapkan.
- `open`: penilaian dimulai; roster juri dan keanggotaan peserta terkunci.
- `closed`: proses penilaian selesai dan dikunci.

Setelah `open`:
- Tidak boleh menambah peserta.
- Tidak boleh menghapus peserta.
- Nama instansi, nama inovasi, atau nama inovator boleh diperbaiki.
- Relasi peserta dengan subkategori atau instansi tidak boleh diubah.
- Nama kategori/subkategori boleh diubah, tetapi ID, relasi, dan jenis penilaian tidak boleh diubah.
- Kriteria dan bobot yang digunakan tidak boleh diubah.
- Perubahan identitas dicatat dalam audit log.

Pembukaan subkategori harus dijalankan dalam transaksi database dan memeriksa kesiapan data serta roster sebelum mengubah status menjadi `open`.

## 6. Kriteria dan nilai

### Penilaian inovasi

| Kriteria | Bobot |
|---|---:|
| Kemampuan Presentasi | 20% |
| Orisinalitas Ide | 30% |
| Manfaat dan Dampak | 20% |
| Proses Transfer Knowledge | 30% |

### Penilaian instansi

| Kriteria | Bobot |
|---|---:|
| Kemampuan Presentasi | 20% |
| Managerial | 30% |
| Komitmen | 30% |
| Strategi dan Mitigasi | 20% |

Aturan:
- Nilai input harus integer 6 sampai 10.
- Bobot seluruh kriteria per festival dan assessment type harus tepat 100.
- Kriteria dan bobot harus sesuai jenis objek.
- Kriteria/bobot tidak dapat diubah setelah penilaian terkait dimulai.

## 7. Penilaian dan penghitungan

- Penilaian inovasi dan instansi menggunakan tabel header dan detail yang terpisah.
- Satu penilaian header memiliki satu detail untuk setiap kriteria yang diwajibkan.
- Satu juri tidak dapat menilai objek yang sama dua kali dalam satu partisipasi subkategori.
- Penilaian dapat disimpan sebagai draft.
- Penilaian hanya dianggap lengkap setelah semua kriteria diisi dan dikirim.
- Total tertimbang juri: `SUM(score * weight) / 100`.
- Rata-rata objek: jumlah total tertimbang semua juri valid dibagi jumlah juri yang menilai.
- Tampilkan total dan rata-rata dengan dua angka desimal; perhitungan internal tidak membulatkan terlalu awal.
- Ranking final hanya memakai penilaian lengkap dan terkunci.
- Semua objek dalam satu subkategori memakai roster yang sama, sehingga jumlah penilai sama.

## 8. Status penilaian dan revisi

Status assessment:
- `draft`
- `submitted`
- `locked`
- `revision_requested`

Alur:
1. Juri mengisi nilai dan menyimpan draft.
2. Juri mengirim penilaian lengkap (`submitted`).
3. Superadmin mengunci (`locked`).
4. Jika perlu revisi, superadmin membuka penilaian (`revision_requested`).
5. Juri memperbaiki dan mengirim ulang.
6. Superadmin mengunci kembali.

Semua perubahan nilai harus mencatat old value, new value, timestamp, actor, dan objek terkait.

## 9. Nilai seri dan pemenang

- Sistem menyediakan total rata-rata dan peringkat.
- Superadmin menentukan jumlah pemenang untuk masing-masing subkategori.
- Jumlah pemenang boleh berbeda antar-subkategori dan antarperiode.
- Penetapan juara manual oleh superadmin, bukan otomatis.
- Posisi juara dimulai sesuai konfigurasi, seperti Juara 1, Juara 2, Juara 3, dan seterusnya.
- Objek tidak boleh mendapat lebih dari satu posisi pada subkategori yang sama.
- Satu posisi juara hanya dapat diberikan kepada satu objek dalam subkategori.
- Jika seri, pembahasan dilakukan bersama juri. Superadmin dapat membuka kembali penilaian tertentu agar juri merevisi nilai. Semua revisi diaudit.
- Jumlah inovasi yang dimiliki instansi bukan metrik penilaian atau dasar juara instansi.

## 10. Laporan dan publikasi

- Laporan PDF difilter berdasarkan periode, kategori utama, dan subkategori.
- Isi awal: logo/nama penyelenggara, nama festival, tahun, tanggal cetak, daftar peserta, total nilai, peringkat, dan pemenang.
- Nama inovator dicantumkan pada laporan inovasi.
- Detail per kriteria per juri tidak dimasukkan pada format laporan awal.
- Sebelum publikasi, pengunjung boleh melihat kategori, instansi, dan inovasi, tetapi tidak boleh melihat nilai atau peringkat.
- Publikasi hasil dilakukan manual oleh superadmin.
- Setelah publikasi, pengunjung dapat melihat daftar peserta, total nilai, peringkat, dan pemenang.

## 11. Audit dan transaksi

Aktivitas penting yang dicatat:
- Perubahan identitas peserta.
- Perubahan nilai.
- Pembukaan dan penutupan subkategori.
- Penguncian dan pembukaan kembali assessment.
- Penetapan atau perubahan pemenang.
- Publikasi hasil.
- Penonaktifan akun juri.

Gunakan transaksi database untuk:
- Membuka subkategori dan mengunci roster/peserta.
- Submit assessment dan detailnya.
- Revisi dan penguncian ulang assessment.
- Menutup subkategori.
- Menetapkan pemenang.
- Memublikasikan hasil.

## 12. Validasi minimum untuk Laravel

Gunakan Form Request untuk validasi input, Policy/Gate/Middleware untuk otorisasi, dan Service Layer untuk aturan lintas tabel.

Validasi minimum:
- Role dan status akun.
- Tahun festival unik.
- Kategori dan subkategori berada dalam periode yang sama.
- Jenis kategori sesuai objek.
- Objek berasal dari festival/subkategori yang benar.
- Juri terdaftar pada festival dan partisipasi subkategori.
- Subkategori masih memungkinkan tindakan yang diminta.
- Seluruh objek dinilai sebelum partisipasi dinyatakan selesai.
- Seluruh kriteria terisi sebelum assessment dikirim.
- Score integer 6–10.
- Bobot berjumlah 100.
- Assessment type kriteria cocok dengan assessment.
- Tidak ada assessment ganda.
- Peringkat dan winner hanya menggunakan assessment lengkap dan locked.
- Posisi winner valid terhadap `winner_count`.
- Publikasi hanya oleh superadmin.

Catatan: beberapa aturan lintas-row tidak bisa diwakili oleh unique index sederhana; implementasikan dengan transaksi dan service validation.
