# Laporan Penyelesaian Phase 2: Authentication & Master Data Management

**Tanggal:** 2026-11-01  
**Repositori:** https://github.com/rendiwidyanggita/Festival-Inovasi  
**Cabang:** `main`  
**Status:** **SELESAI & 100% TERVERIFIKASI**

---

## 1. Ringkasan Eksekutif

Phase 2 mencakup implementasi fondasi autentikasi role-based, layout responsif (desktop & mobile) dengan Tailwind CSS, pengelolaan data master (Festival, Kategori Festival, Subkategori/Award Category, Instansi, Inovasi, Kriteria Penilaian, Juri), audit trail otomatis via `AuditService`, serta portal publik untuk melihat daftar peserta festival.

Seluruh 57 skenario automated test telah dieksekusi di database MySQL lokal (`festival_inovasi_test`) dan **100% lolos (0 failures, 0 errors, 148 assertions)**.

---

## 2. Rincian Pekerjaan yang Diselesaikan

### A. Middleware & Keamanan Otentikasi
1. **EnsureRole Middleware (`app/Http/Middleware/EnsureRole.php`)**:
   - Memvalidasi user terautentikasi dan akun aktif (`is_active == true`).
   - Melakukan otorisasi berbasis role (`superadmin` dan `jury`).
   - Mencegah akses silang (misal juri mengakses panel admin, atau sebaliknya).
   - Didaftarkan sebagai alias middleware `'role'` di `bootstrap/app.php`.
2. **Login Controller & Request (`app/Http/Controllers/Auth/LoginController.php`, `app/Http/Requests/Auth/LoginRequest.php`)**:
   - Rate limiting 5 percobaan/menit dengan pesan sisa waktu (translatable).
   - Pengecekan status akun aktif sebelum session login diterbitkan.
   - Redirect cerdas berdasarkan role (`/admin` untuk Superadmin, `/jury` untuk Juri).
   - Invalidation session dan regenerasi CSRF token pada logout.

### B. Otorisasi Kebijakan (Authorization Policies)
Sesuai aturan bisnis, seluruh operasi mutasi dilindungi via Policy terdaftar:
- `FestivalPolicy`
- `FestivalCategoryPolicy`
- `AwardCategoryPolicy`
- `InstitutionPolicy`
- `InnovationPolicy`
- `AssessmentCriterionPolicy`
- `UserPolicy`

Invarian bisnis yang dilindungi:
- Subkategori tidak boleh dihapus jika memiliki instansi atau inovasi terdaftar.
- Kategori tidak boleh dihapus jika memiliki subkategori.
- Instansi/Inovasi tidak boleh dipindahkan atau dihapus setelah status penilaian subkategori masuk `open` atau `locked` (Roster Lock).
- Kriteria penilaian tidak boleh diubah/dihapus jika sudah memiliki record detail penilaian (`innovation_assessment_details` / `institution_assessment_details`).
- User tidak dapat menghapus akunnya sendiri.

### C. Master Data Management (Controllers & Views)
1. **Festival Management (`Admin\FestivalController`)**:
   - CRUD Festival + setting per festival (`FestivalSetting`).
   - Pemilihan active festival session (`selected_festival_id`) untuk kemudahan navigasi multi-tahun.
2. **Kategori & Subkategori (`Admin\FestivalCategoryController`, `Admin\AwardCategoryController`)**:
   - Pengaturan tipe penilaian (`innovation` atau `institution`).
   - Pengaturan kuota pemenang (`winner_count`), urutan tampil (`sort_order`).
3. **Instansi & Inovasi (`Admin\InstitutionController`, `Admin\InnovationController`)**:
   - Filter dropdown subkategori berdasarkan tipe.
   - Pencegahan perubahan nama instansi tanpa audit log jika penilaian sudah dimulai.
4. **Kriteria Penilaian (`Admin\AssessmentCriterionController`)**:
   - Kriteria terpisah untuk inovasi dan instansi.
   - Validasi bobot dan urutan tampilan.
5. **Juri (`Admin\JuryController`)**:
   - Registrasi juri, reset status/aktivasi, dan penugasan festival.

### D. Audit Logging (`App\Services\AuditService`)
- Menulis log ke tabel `audit_logs` secara polimorfik (`Relation::enforceMorphMap`).
- Menyimpan snapshot `old_values` dan `new_values` dalam format JSON.
- Mencatat actor ID, action (`create`, `update`, `delete`, `update_identity`), IP address, dan user agent.

### E. Portal Publik (`PublicController` & Views)
- Rute publik tanpa proteksi login:
  - `/` (Beranda publik dengan festival aktif).
  - `/festivals/{festival}` (Detail festival & daftar kategori).
  - `/festivals/{festival}/categories/{awardCategory}` (Daftar peserta subkategori).
- Nilai dan peringkat disembunyikan secara otomatis sampai festival memiliki status `results_published_at != null`.

---

## 3. Hasil Verifikasi Test Suite

```
   PASS  Tests\Feature\Admin\FestivalCrudTest
  ✓ superadmin can view festival list                                    0.11s  
  ✓ superadmin can create festival                                       0.05s  
  ✓ festival creation writes audit log                                   0.04s  
  ✓ cannot create festival with duplicate year                           0.03s  
  ✓ superadmin can update festival                                       0.04s  
  ✓ festival update writes audit log                                     0.04s  
  ✓ auto selects single active festival                                  0.04s  
  ✓ can switch selected festival                                         0.04s  
  ✓ superadmin can access dashboard with selected festival               0.05s  
  ✓ superadmin can view audit logs                                       0.04s  
  ✓ jury cannot access festival crud                                     0.04s  

   PASS  Tests\Feature\Admin\MasterDataCrudTest
  ✓ superadmin can create festival category                              0.05s  
  ✓ cannot create duplicate category name per festival                   0.04s  
  ✓ cannot delete category with award categories                         0.04s  
  ✓ superadmin can create award category                                 0.05s  
  ✓ cannot delete award category with innovations                        0.05s  
  ✓ cannot delete award category with institutions                       0.05s  
  ✓ superadmin can create institution when preparation                   0.04s  
  ✓ cannot delete institution after scoring opened                       0.05s  
  ✓ can update institution name after scoring opened                     0.04s  
  ✓ superadmin can create innovation when preparation                    0.05s  
  ✓ cannot delete innovation after scoring opened                        0.04s  
  ✓ can update innovation name after scoring opened                      0.04s  
  ✓ superadmin can create assessment criterion                           0.04s  
  ✓ cannot update assessment criterion with assessment details           0.06s  
  ✓ cannot delete assessment criterion with assessment details           0.06s  
  ✓ superadmin can create jury                                           0.04s  
  ✓ jury email must be unique                                            0.04s  
  ✓ superadmin can toggle jury status                                    0.04s  
  ✓ superadmin can update jury                                           0.04s  
  ✓ superadmin cannot delete self                                        0.03s  
  ✓ jury cannot access master data crud                                  0.07s  

   PASS  Tests\Feature\AuthenticationTest
  ✓ login screen can be rendered                                         0.03s  
  ✓ superadmin can login with correct credentials                        0.04s  
  ✓ jury can login with correct credentials                              0.04s  
  ✓ inactive user cannot login                                           0.03s  
  ✓ users cannot authenticate with invalid password                      0.03s  
  ✓ rate limiting kicks in after five failed attempts                    0.04s  
  ✓ authenticated user can logout                                        0.04s  
  ✓ guest cannot access admin dashboard                                  0.03s  
  ✓ guest cannot access jury dashboard                                   0.03s  
  ✓ authenticated superadmin redirected to admin dashboard               0.04s  

   PASS  Tests\Feature\DatabaseSchemaTest
  ✓ schema has all required tables                                       0.03s  
  ✓ superadmin seeder creates initial account                            0.04s  
  ✓ soft deletes work for user                                           0.04s  

   PASS  Tests\Feature\PublicAccessTest
  ✓ guest can view public index page                                     0.03s  
  ✓ guest can view festival detail page                                  0.03s  
  ✓ guest can view category page                                         0.04s  
  ✓ guest cannot see scores before publication                           0.04s  
  ✓ guest can see scores and winners after publication                   0.04s  
  ✓ guest cannot access admin routes                                     0.04s  
  ✓ guest cannot access jury routes                                      0.04s  
  ✓ guest cannot perform write operations                                0.04s  
  ✓ guest cannot delete resources                                        0.04s  
  ✓ guest cannot access admin dashboard                                  0.03s  
  ✓ guest cannot access jury dashboard                                   0.04s  
  ✓ public pages show participants list                                  0.04s  

  Tests:    57 passed (148 assertions)
  Duration: 4.51s
```

---

## 4. Kesiapan Menuju Phase 3 (Scoring System)

Dengan terselesaikannya Phase 2, fondasi master data telah siap untuk dihubungkan ke modul penilaian (Phase 3):
- Model dan tabel penilaian (`InnovationAssessment`, `InstitutionAssessment`, `JurySubcategoryParticipation`) sudah terstruktur.
- Status alur scoring (`preparation` -> `open` -> `locked`) telah terintegrasi di enum dan model.
- Constraint database komposit (`festival_id`, `award_category_id`) dan foreign key integritas data terbukti aman dalam pengujian.
