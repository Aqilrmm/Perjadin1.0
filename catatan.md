# OVERVIEW LIBRARIES

## 📁 **app/Libraries/Auth/**

### **AuthService.php**
**Fungsi**: Menangani semua operasi autentikasi pengguna

**Terhubung dengan:**
- `UserModel.php`
- `SecurityLogModel.php`
- `Session` (CodeIgniter)

**Methods:**
- `attempt()` - Login user, validasi kredensial, set session
- `logout()` - Hapus session, log activity
- `check()` - Cek status autentikasi
- `getCurrentUser()` - Get data user dari session
- `getUserById()` - Get user by ID
- `refreshSession()` - Update session data
- `setUserSession()` - Set data ke session
- `setRememberMeCookie()` - Set cookie remember me
- `incrementLoginAttempts()` - +1 login attempts, auto-block setelah 3x
- `logFailedAttempt()` - Log percobaan login gagal
- `logSuccessfulLogin()` - Log login berhasil
- `getRedirectByRole()` - Get URL redirect berdasarkan role
- `getUserData()` - Format data user untuk response
- `validateSessionTimeout()` - Validasi timeout session (60 menit)
- `changePassword()` - Ubah password dengan validasi password lama
- `resetPassword()` - Reset password oleh admin

---

### **RBACService.php**
**Fungsi**: Role-Based Access Control - manajemen permission

**Terhubung dengan:**
- Semua Controllers (untuk cek permission)
- `AuthFilter.php`
- `RoleFilter.php`

**Methods:**
- `initializePermissions()` - Set permission matrix per role
- `initializeRoleHierarchy()` - Set hierarki level role
- `hasPermission()` - Cek apakah role punya permission
- `hasAnyPermission()` - Cek punya minimal 1 dari list permissions
- `hasAllPermissions()` - Cek punya semua permissions
- `canAccessRoute()` - Cek akses ke route tertentu
- `getRoutePermissions()` - Get permissions untuk route
- `matchRoute()` - Match route dengan pattern
- `isRoleHigherThan()` - Bandingkan level role
- `getRoleLevel()` - Get level hierarki role
- `getPermissions()` - Get semua permissions role
- `canManageUser()` - Cek bisa manage user lain
- `canApproveForBidang()` - Cek bisa approve untuk bidang
- `getAllowedRolesForCreation()` - Get roles yang bisa dibuat
- `canViewSppd()` - Cek bisa lihat SPPD
- `canEditSppd()` - Cek bisa edit SPPD
- `getMenuPermissions()` - Get menu yang bisa diakses

---

## 📁 **app/Libraries/SPPD/**

### **NotaDinasGenerator.php**
**Fungsi**: Generate PDF Nota Dinas dari SPPD

**Terhubung dengan:**
- `SPPDModel.php`
- `SPPDPegawaiModel.php`
- `UserModel.php`
- mPDF library

**Methods:**
- `generate()` - Generate PDF (inline/download/save)
- `saveToFile()` - Save PDF ke directory
- `generateHtml()` - Generate HTML template nota dinas
- `generateNoNotaDinas()` - Generate nomor nota dinas
- `getStyles()` - Get CSS styles untuk PDF
- `initMpdf()` - Initialize mPDF instance
- `getFileSize()` - Get ukuran file PDF

---

### **SPPDValidator.php**
**Fungsi**: Validasi business rules untuk SPPD

**Terhubung dengan:**
- `SPPDModel.php`
- `SPPDPegawaiModel.php`
- `SubKegiatanModel.php`
- `UserModel.php`

**Methods:**
- `validateCreate()` - Validasi create SPPD
- `validateUpdate()` - Validasi update SPPD
- `validateSubKegiatanApproved()` - Cek sub kegiatan approved
- `validateTanggalBerangkat()` - Validasi H+1
- `validateTanggalKembali()` - Validasi >= tanggal berangkat
- `validateLamaPerjalanan()` - Validasi sesuai selisih tanggal
- `validateEstimasiBiaya()` - Cek tidak melebihi anggaran
- `validatePegawaiList()` - Validasi minimal 1 pegawai
- `checkPegawaiOverlap()` - Cek jadwal overlap (warning)
- `validatePenanggungJawab()` - Validasi penanggung jawab valid
- `validateNoSPPD()` - Validasi nomor SPPD unique
- `validateLPPDSubmission()` - Validasi submit LPPD
- `validateKwitansiSubmission()` - Validasi submit kwitansi
- `validateKwitansiDalamDaerah()` - Validasi kwitansi dalam daerah
- `validateKwitansiLuarDaerahDalamProv()` - Validasi luar daerah dalam provinsi
- `validateKwitansiLuarDaerahLuarProv()` - Validasi luar daerah luar provinsi
- `validateSubmitForVerification()` - Validasi submit ke keuangan
- `getErrors()` - Get semua errors
- `getWarnings()` - Get warnings saja
- `hasErrors()` - Cek ada errors (exclude warnings)
- `clearErrors()` - Clear error array

---

# 📋 FILES YANG MASIH BELUM ADA

## **Views** (Frontend - semua file UI)
- `app/Views/**/*` - Semua view files belum dibuat

## **Public Assets** (Frontend assets)
- `public/assets/css/app.css`
- `public/assets/css/tailwind.css`
- `public/assets/js/app.js`
- `public/assets/js/auth.js`
- `public/assets/js/datatables-init.js`
- `public/assets/js/sppd-wizard.js`
- `public/assets/js/file-upload.js`
- `public/assets/js/notification.js`
- `public/assets/images/logo.png`
- `public/assets/images/default-avatar.png`

## **Configuration Files**
- `app/Config/Database.php` (koneksi database)
- `app/Config/Email.php` (email config)
- `app/Config/Constants.php` (app constants)
- `app/Config/Validation.php` (register custom rules)
- `.env` (environment variables)

## **Upload Directories**
- `public/uploads/surat_tugas/`
- `public/uploads/bukti_perjalanan/`
- `public/uploads/bukti_penginapan/`
- `public/uploads/bukti_taxi/`
- `public/uploads/bukti_tiket/`
- `public/uploads/dokumentasi_kegiatan/`
- `public/uploads/foto_profile/`
- `public/uploads/nota_dinas/`

## **Documentation**
- `README.md` (project documentation)
- `INSTALLATION.md` (instalasi guide)
- `API_DOCUMENTATION.md` (API docs)

## **Testing**
- `tests/Controllers/**/*`
- `tests/Models/**/*`
- `tests/Libraries/**/*`

## **Additional Helpers** (optional tapi recommended)
- `app/Helpers/pdf_helper.php`
- `app/Helpers/excel_helper.php`
- `app/Helpers/validation_helper.php`

## **Middleware/Filters** (optional tambahan)
- `app/Filters/RateLimitFilter.php`
- `app/Filters/CORSFilter.php`

## **Seeder Files** (sample data)
- `app/Database/Seeds/BidangSeeder.php`
- `app/Database/Seeds/UserSeeder.php`
- `app/Database/Seeds/ProgramSeeder.php`

Total files yang masih perlu dibuat: **~150+ files** (mayoritas Views dan Assets)