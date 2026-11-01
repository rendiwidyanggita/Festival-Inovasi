# Laravel Migration Package Reference

**Paket terpisah:** `festival_inovasi_laravel_migrations_v0_1.zip`  
**Database target:** MySQL / InnoDB  
**Jumlah file migration:** 18

## 1. Daftar migration

1. `create_users_table`
2. `create_password_reset_tokens_table`
3. `create_festivals_table`
4. `create_festival_settings_table`
5. `create_festival_categories_table`
6. `create_award_categories_table`
7. `create_institutions_table`
8. `create_innovations_table`
9. `create_festival_juries_table`
10. `create_jury_subcategory_participations_table`
11. `create_assessment_criteria_table`
12. `create_innovation_assessments_table`
13. `create_innovation_assessment_details_table`
14. `create_institution_assessments_table`
15. `create_institution_assessment_details_table`
16. `create_innovation_winners_table`
17. `create_institution_winners_table`
18. `create_audit_logs_table`

## 2. Cara menggunakan

1. Buat project Laravel dengan versi yang kompatibel dengan PHP yang tersedia di hosting.
2. Periksa migration default Laravel. Jika sudah ada migration `create_users_table`, gabungkan/adaptasi dengan migration `users` dari paket ini. Jangan menyimpan dua migration yang sama-sama membuat tabel `users`.
3. Atur `.env` ke database MySQL development.
4. Periksa semua migration dan versi MySQL/MariaDB lokal.
5. Jalankan terlebih dahulu:

```bash
php artisan migrate:pretend
```

6. Setelah review SQL, jalankan migration pada database lokal kosong:

```bash
php artisan migrate
```

7. Lakukan feature/integration tests untuk foreign key, unique constraints, dan validasi lintas tabel.
8. Setelah semua pengujian berhasil, rencanakan deployment production dengan backup database terlebih dahulu.

**Jangan gunakan `php artisan migrate:fresh` atau `db:wipe` pada database yang berisi data penting.**

## 3. Catatan kompatibilitas

- Paket dibuat menggunakan Laravel Schema Builder dan anonymous migration classes.
- PHP syntax lint telah dilakukan terhadap seluruh 18 file.
- Migration belum dijalankan pada instalasi Laravel atau database ArenHost target.
- Versi MySQL/MariaDB ArenHost Competent belum dikonfirmasi.
- Composite foreign keys harus diuji pada versi engine target.
- Nilai score 6–10, total bobot 100%, status workflow, kecocokan assessment type, serta aturan lintas-row tetap harus divalidasi pada layer Laravel.
- `unsignedTinyInteger('score')` tidak dengan sendirinya membatasi nilai 6–10.
- Pembukaan subkategori, penguncian roster/peserta, submit, revisi, locking, penetapan pemenang, dan publikasi perlu transaksi database.

## 4. Seeder yang diperlukan berikutnya

Buat seeder setelah migration berhasil:
- Superadmin awal (credential melalui environment/command aman, bukan password hard-coded).
- Kategori awal festival: ASN TERINOVATIF (`innovation`) dan LEMBAGA/INSTANSI TERINOVATIF (`institution`).
- Subkategori awal berdasarkan `PRD.md`.
- Kriteria dan bobot penilaian inovasi.
- Kriteria dan bobot penilaian instansi.

Kategori dan kriteria harus dibuat di bawah record festival yang dipilih. Jangan hard-code seeder agar semua periode festival menggunakan ID yang sama.

## 5. Validasi sebelum production

- Konfirmasi versi PHP, Laravel, dan MySQL/MariaDB.
- Uji migration pada database lokal yang kosong.
- Uji `down()` hanya pada lingkungan development/test.
- Uji composite foreign keys.
- Uji unique constraints.
- Uji soft delete dan histori.
- Uji semua aturan pada `BUSINESS_RULES.md`.
- Backup database dan siapkan prosedur restore.
