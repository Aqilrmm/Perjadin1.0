# Rancangan Halaman Per User - Aplikasi Perjadin

---

## GLOBAL COMPONENTS

### 1. Login Page (`/login`)
**Deskripsi:** Halaman autentikasi user
**Layout:**
- Card center dengan background gradient
- Logo Instansi
- Form: NIP/NIK, Password, Remember Me
- Button: Login dengan loading state
- Link: Forgot Password
- Footer: Copyright

**Fitur:**
- Validation real-time
- Show/Hide password
- CSRF Protection
- reCAPTCHA setelah 2x failed login
- Auto-focus NIP field

**Teknologi:** Tailwind CSS, jQuery Validation, SweetAlert2

---

### 2. Sidebar Navigation
**Deskripsi:** Navigasi utama (collapse-able)
**Komponen:**
- Logo + Nama Instansi
- User Profile: Foto, Nama, Role
- Menu sesuai role (icon + text)
- Logout button
- Toggle collapse button

**Fitur:**
- Active menu highlight
- Tooltips pada collapsed state
- Badge notifikasi (angka) pada menu tertentu

---

### 3. Topbar
**Deskripsi:** Header halaman
**Komponen:**
- Breadcrumb navigation
- Search global (optional)
- Notification icon (dropdown list)
- User dropdown: Profile, Settings, Logout

---

## A. SUPER ADMIN

### SA-1. Dashboard (`/superadmin/dashboard`)
**Deskripsi:** Overview sistem secara keseluruhan

**Widgets:**
1. **Statistics Cards (4 cards horizontal)**
   - Total User (icon users, bg-blue)
   - Total Bidang (icon briefcase, bg-green)
   - Total SPPD Active (icon file-text, bg-yellow)
   - Total Program (icon folder, bg-purple)

2. **Charts Section (2 columns)**
   - Left: Line Chart - SPPD per Bulan (6 bulan terakhir)
   - Right: Pie Chart - SPPD per Bidang

3. **Recent Activities Table**
   - Tabel dengan 10 aktivitas terakhir
   - Kolom: Timestamp, User, Action, Status
   - Badge warna sesuai action type

4. **Security Alerts Panel**
   - Daftar login gagal hari ini
   - User yang ter-block
   - Quick action: Unblock button

**Layout:** Grid 4-4-4 untuk cards, Grid 6-6 untuk charts, Full-width table

---

### SA-2. Kelola User (`/superadmin/users`)
**Deskripsi:** CRUD User

**Komponen:**
1. **Header Section**
   - Title: "Kelola User"
   - Button: "+ Tambah User" (primary, modal trigger)
   - Button: "Export Excel" (secondary)

2. **Filter Section (collapsible)**
   - Filter by: Role, Bidang, Status (Active/Blocked)
   - Search: NIP/Nama
   - Button: Apply Filter, Reset

3. **DataTable**
   - Kolom: No, NIP/NIK, Nama Lengkap, Jabatan, Bidang, Role, Status, Action
   - Action: Edit (icon pencil), Delete (icon trash), Block/Unblock (icon shield)
   - Pagination: Show 10/25/50/100 entries
   - Export: Copy, CSV, Excel, PDF, Print

4. **Modal: Tambah/Edit User**
   - Form Fields:
     - NIP/NIK* (input, readonly saat edit)
     - Gelar Depan (input, optional)
     - Nama Pegawai* (input)
     - Gelar Belakang (input, optional)
     - ASN/Non-ASN* (radio)
     - Email* (input)
     - Jabatan* (input)
     - Bidang* (select dropdown, disable untuk role tertentu)
     - Role* (select: superadmin, kepaladinas, kepalabidang, pegawai, keuangan)
     - Password* (input, show password toggle, hanya saat tambah)
     - Status (switch: Active/Inactive)
   - Button: Simpan (primary), Batal (secondary)
   - Validation: Real-time dengan message di bawah field

**Teknologi:** DataTables, Select2 untuk dropdown, jQuery Validation

---

### SA-3. Kelola Bidang (`/superadmin/bidang`)
**Deskripsi:** CRUD Bidang

**Komponen:**
1. **Header + Button Tambah**

2. **DataTable**
   - Kolom: No, Nama Bidang, Jumlah Pegawai, Status, Action
   - Action: Edit, Delete (dengan konfirmasi)

3. **Modal: Tambah/Edit Bidang**
   - Form: Nama Bidang*, Keterangan (textarea, optional)
   - Button: Simpan, Batal

**Alert:** SweetAlert2 confirmation sebelum delete

---

### SA-4. Logs Keamanan (`/superadmin/logs`)
**Deskripsi:** Monitor aktivitas sistem

**Komponen:**
1. **Filter Advanced**
   - Date Range (date picker)
   - User (select2 searchable)
   - Action Type (select: Login, Logout, Create, Update, Delete, Approve, Reject)
   - IP Address (input)
   - Button: Filter, Reset, Export

2. **DataTable Logs**
   - Kolom: Timestamp, User, Action, Description, IP Address, User Agent
   - Color coding: 
     - Login Success: green
     - Login Failed: red
     - CRUD: blue
     - Approve: teal
     - Reject: orange
   - Pagination server-side
   - Export all formats

**Note:** Read-only, tidak ada action

---

### SA-5. Block User (`/superadmin/blocked-users`)
**Deskripsi:** Manage blocked users

**Komponen:**
1. **Statistics Bar**
   - Total Blocked Today
   - Auto-blocked (3x failed login)
   - Manual Blocked (by admin)

2. **DataTable Blocked Users**
   - Kolom: NIP, Nama, Blocked Reason, Blocked Date, Blocked By, Action
   - Action: Unblock (button primary)

**Fitur:** Auto-refresh setiap 30 detik

---

## B. KEPALA DINAS

### KD-1. Dashboard (`/kepaladinas/dashboard`)
**Deskripsi:** Overview approval & analytics

**Widgets:**
1. **Approval Cards (4 cards)**
   - Pending Program (badge count, bg-yellow)
   - Pending Kegiatan (badge count, bg-orange)
   - Pending Sub Kegiatan (badge count, bg-blue)
   - Pending SPPD (badge count, bg-red)
   - Click to navigate

2. **Charts**
   - Line Chart: Trend SPPD per Bidang per Bulan
   - Bar Chart: Total SPPD per Bidang (Top 10)

3. **Recent Approvals Table**
   - 10 approval terakhir
   - Kolom: Tanggal, Jenis, Nama, Bidang, Status, Action

---

### KD-2. Persetujuan Program (`/kepaladinas/program/approval`)
**Deskripsi:** Approve/Reject program dari Kepala Bidang

**Komponen:**
1. **Tabs**
   - Pending (badge count)
   - Approved
   - Rejected

2. **DataTable per Tab**
   - Kolom: No, Kode Program, Nama Program, Bidang, Tahun Anggaran, Jumlah Anggaran, Tanggal Pengajuan, Action
   - Action: 
     - Detail (icon eye, modal)
     - Approve (icon check, konfirmasi)
     - Reject (icon x, modal dengan catatan)

3. **Modal Detail Program**
   - Display semua info program
   - Info pengaju: Nama Kepala Bidang, Tanggal Pengajuan
   - Button: Approve (green), Reject (red), Close

4. **Modal Reject**
   - Textarea: Catatan Penolakan* (minimal 10 karakter)
   - Button: Kirim Penolakan, Batal

**Fitur:**
- Real-time notification saat ada pengajuan baru
- Filter by Bidang, Tahun Anggaran
- Export approved/rejected programs

---

### KD-3. Persetujuan Kegiatan (`/kepaladinas/kegiatan/approval`)
**Deskripsi:** Approve/Reject kegiatan
**Struktur:** Sama seperti Persetujuan Program

---

### KD-4. Persetujuan Sub Kegiatan (`/kepaladinas/subkegiatan/approval`)
**Deskripsi:** Approve/Reject sub kegiatan
**Struktur:** Sama seperti Persetujuan Program

---

### KD-5. Persetujuan SPPD (`/kepaladinas/sppd/approval`)
**Deskripsi:** Approve/Reject SPPD

**Komponen:**
1. **Tabs:** Pending, Approved, Rejected

2. **DataTable**
   - Kolom: No SPPD, Bidang, Tipe Perjalanan, Tempat Tujuan, Tanggal Berangkat, Jumlah Pegawai, Action
   - Action:
     - Preview PDF (icon file, modal)
     - Detail (icon eye, modal full detail)
     - Approve (icon check)
     - Reject (icon x)

3. **Modal Preview PDF**
   - Embed PDF viewer (PDF.js)
   - Download button
   - Print button
   - Button: Approve dari sini, Reject, Close

4. **Modal Detail SPPD**
   - Section: Info Program, Kegiatan, Sub Kegiatan
   - Section: Detail Perjalanan (semua field SPPD)
   - Section: Daftar Pegawai (table)
   - Section: Anggaran
   - Button: Generate Preview, Approve, Reject

**Fitur:**
- After approve: Sistem otomatis generate Nota Dinas dan update status
- Notifikasi ke Kepala Bidang & Pegawai

---

### KD-6. Analytics SPPD (`/kepaladinas/analytics`)
**Deskripsi:** Dashboard analytics semua SPPD

**Komponen:**
1. **Filter Section**
   - Periode: Date Range Picker
   - Bidang: Multi-select (All by default)
   - Tipe Perjalanan: Multi-select
   - Status: Multi-select
   - Button: Apply, Reset, Export Report

2. **Summary Cards (5 cards)**
   - Total SPPD
   - Total Anggaran Terpakai
   - Rata-rata Biaya per SPPD
   - SPPD Completed
   - SPPD In Progress

3. **Charts Grid (2x2)**
   - Top-Left: Line Chart - Trend per Bulan
   - Top-Right: Pie Chart - SPPD per Tipe Perjalanan
   - Bottom-Left: Bar Chart - SPPD per Bidang
   - Bottom-Right: Donut Chart - Status SPPD

4. **Detailed Table**
   - DataTable dengan semua SPPD sesuai filter
   - Kolom: No SPPD, Bidang, Program, Tujuan, Tanggal, Biaya, Status
   - Export: Excel, PDF

**Teknologi:** Chart.js / Recharts untuk visualisasi

---

## C. KEPALA BIDANG

### KB-1. Dashboard (`/kepalabidang/dashboard`)
**Deskripsi:** Overview bidang dan SPPD

**Widgets:**
1. **Quick Stats (4 cards)**
   - Program Aktif (link ke list)
   - Kegiatan Berjalan
   - SPPD Bulan Ini
   - Sisa Anggaran (highlight jika < 20%)

2. **Chart Analytics Bidang**
   - Line Chart: SPPD Bidang per Bulan
   - Bar Chart: Budget vs Realisasi per Program

3. **My Submissions Table**
   - Program/Kegiatan/Sub Kegiatan yang statusnya Pending
   - Quick action: Cancel (jika masih pending)

4. **SPPD Active Table**
   - SPPD yang sedang berjalan di bidang
   - Status tracking

---

### KB-2. Program (`/kepalabidang/program`)
**Deskripsi:** Kelola Program

**Komponen:**
1. **Header**
   - Button: "+ Ajukan Program Baru"
   - Button: "Export List"

2. **Tabs**
   - Semua Program
   - Draft
   - Pending Approval
   - Approved
   - Rejected

3. **DataTable per Tab**
   - Kolom: Kode Program, Nama Program, Tahun Anggaran, Anggaran, Sisa Anggaran, Status, Action
   - Action: 
     - View (icon eye)
     - Edit (icon pencil, hanya untuk Draft)
     - Submit (icon send, untuk Draft)
     - Delete (icon trash, hanya Draft)

4. **Modal: Ajukan Program**
   - Form:
     - Kode Program (readonly, auto-generate)
     - Nama Program*
     - Bidang (readonly, auto-filled)
     - Tahun Anggaran* (select, default current year)
     - Jumlah Anggaran* (input number, format rupiah)
     - Deskripsi Program (textarea)
   - Button: Simpan sebagai Draft, Simpan & Ajukan
   - Validation dengan jQuery Validate

**Fitur:**
- Badge status dengan color coding
- Rejected programs menampilkan catatan dari Kepala Dinas

---

### KB-3. Kegiatan (`/kepalabidang/kegiatan`)
**Deskripsi:** Kelola Kegiatan

**Komponen:**
1. **Header + Button**

2. **Filter**
   - Filter by Program (dropdown approved programs only)
   - Status

3. **DataTable**
   - Kolom: Kode Kegiatan, Program, Nama Kegiatan, Anggaran, Status, Action

4. **Modal: Buat Kegiatan**
   - Form:
     - Pilih Program* (select dari approved programs)
     - Kode Program (readonly, auto-filled)
     - Kode Kegiatan (readonly, auto-generate)
     - Nama Kegiatan*
     - Anggaran Kegiatan* (max: sisa anggaran program)
     - Deskripsi
   - Validation: Anggaran tidak boleh > sisa anggaran program

**Alert:** Jika tidak ada approved program, tampilkan alert "Belum ada program yang disetujui"

---

### KB-4. Sub Kegiatan (`/kepalabidang/subkegiatan`)
**Deskripsi:** Kelola Sub Kegiatan
**Struktur:** Sama seperti Kegiatan, tapi pilih dari approved Kegiatan

---

### KB-5. Buat SPPD (`/kepalabidang/sppd/create`)
**Deskripsi:** Form pembuatan SPPD

**Layout:** Multi-step form (Wizard)

**Step 1: Pilih Program & Kegiatan**
- Select Program (approved only)
- Select Kegiatan (approved only, filtered by program)
- Select Sub Kegiatan (approved only, filtered by kegiatan)
- Display: Sisa anggaran sub kegiatan
- Button: Next

**Step 2: Detail Perjalanan**
- Form:
  - Tipe Perjalanan* (radio: Dalam Daerah, Luar Daerah Dalam Prov, Luar Daerah Luar Prov)
  - Maksud Perjalanan* (textarea)
  - Dasar Surat* (input nomor surat)
  - Upload Surat Tugas (file upload, PDF)
  - Alat Angkut* (select: Mobil Dinas, Mobil Pribadi, Travel, Bus, Pesawat, Kereta, dll)
  - Tempat Berangkat*
  - Tempat Tujuan*
  - Tanggal Berangkat* (date picker, min: tomorrow)
  - Lama Perjalanan* (input number, suffix: hari)
  - Tanggal Kembali (readonly, auto-calculate)
- Button: Previous, Next

**Step 3: Penanggung Jawab & Pegawai**
- Penanggung Jawab: (auto-filled Kepala Bidang, readonly)
- Daftar Pegawai yang Melakukan Perjalanan:
  - Multi-select searchable (pegawai dari bidang yang sama)
  - Tampilkan yang sudah dipilih dalam tabel
  - Kolom tabel: NIP, Nama, Jabatan, Action (Remove)
  - Max 10 pegawai
- Validation: Check jadwal overlap pegawai
- Button: Previous, Next

**Step 4: Estimasi Biaya**
- Dynamic form berdasarkan Tipe Perjalanan
- Input biaya per jenis (Perjalanan, Lumsum, Penginapan, Taxi, Tiket)
- Jumlah pegawai × biaya per orang
- Total Estimasi Biaya (readonly, auto-sum)
- Warning jika total > sisa anggaran
- Button: Previous, Next

**Step 5: Review & Submit**
- Display semua data yang telah diisi (readonly)
- Checklist: "Saya bertanggung jawab atas kebenaran data ini"
- Button: Submit SPPD, Edit (kembali ke step sebelumnya)

**Fitur:**
- Progress bar di atas form (Step 1/5, 2/5, dst)
- Auto-save draft (local storage) setiap 30 detik
- Konfirmasi SweetAlert2 sebelum submit

---

### KB-6. Lihat SPPD (`/kepalabidang/sppd`)
**Deskripsi:** Daftar semua SPPD di bidang

**Komponen:**
1. **Filter**
   - Program, Kegiatan, Status, Tipe Perjalanan, Periode
   
2. **DataTable**
   - Kolom: No SPPD, Program, Tujuan, Tanggal Berangkat, Penanggung Jawab, Status, Action
   - Action:
     - Detail (icon eye)
     - Lihat SPPD (icon file-pdf)
     - Lihat Nota Dinas (icon file-text, hanya jika approved)
     - Lihat LPPD (icon book, hanya jika ada)
     - Lihat Kwitansi (icon receipt, hanya jika ada)

3. **Modal Detail SPPD**
   - Tab: Info SPPD, Daftar Pegawai, LPPD, Kwitansi, Dokumentasi
   - Button: Download PDF

---

### KB-7. Analytics Bidang (`/kepalabidang/analytics`)
**Deskripsi:** Analytics SPPD bidang
**Komponen:** Mirip dengan Analytics Kepala Dinas, tapi hanya untuk bidangnya

---

## D. PEGAWAI

### PG-1. Dashboard (`/pegawai/dashboard`)
**Deskripsi:** Overview SPPD pegawai

**Widgets:**
1. **My Stats (3 cards)**
   - Total SPPD Saya
   - SPPD Berjalan
   - SPPD Butuh Action (badge: perlu isi LPPD/Kwitansi)

2. **Upcoming Trips**
   - List card SPPD mendatang (upcoming 30 days)
   - Countdown days
   - Button: Lihat Detail

3. **Action Required**
   - List SPPD yang butuh action (isi LPPD/Kwitansi)
   - Button: Isi Sekarang

---

### PG-2. SPPD Saya (`/pegawai/sppd`)
**Deskripsi:** Daftar SPPD yang melibatkan pegawai ini

**Komponen:**
1. **Filter**
   - Status, Periode, Tipe Perjalanan

2. **Card List (bukan table)**
   - Setiap SPPD ditampilkan dalam card:
     - Header: No SPPD, Status Badge
     - Body: Tujuan, Tanggal, Penanggung Jawab
     - Footer: Button Action sesuai status

3. **Card Actions based on Status:**
   - **Pending:** "Menunggu Persetujuan"
   - **Approved:** "Download Nota Dinas", "Download SPPD"
   - **After Travel Date:** "Isi LPPD & Kwitansi"
   - **Submitted:** "Menunggu Verifikasi Keuangan"
   - **Verified:** "Selesai", "Download Semua Dokumen"

---

### PG-3. Isi LPPD (`/pegawai/sppd/{id}/lppd`)
**Deskripsi:** Form LPPD

**Komponen:**
1. **Info SPPD (Top Card)**
   - Display: No SPPD, Tujuan, Tanggal, Maksud Perjalanan

2. **Form LPPD**
   - Hasil Kegiatan* (CKEditor/Textarea rich, min 50 karakter)
   - Hambatan yang Dihadapi (textarea)
   - Saran (textarea)
   - Upload Dokumentasi Foto*:
     - Drag & drop area atau click to upload
     - Preview thumbnail yang sudah diupload
     - Min 2 foto, max 10 foto
     - Format: JPG, JPEG, PNG
     - Max size per foto: 5 MB
     - Button: Remove per foto
   - Tanggal Pengisian (readonly, auto-filled)

3. **Button:**
   - Simpan sebagai Draft (dapat diedit lagi)
   - Submit (tidak dapat diedit, langsung ke kwitansi)

**Validation:**
- Real-time character count
- Preview foto sebelum upload
- Compress foto otomatis jika > 5 MB

---

### PG-4. Isi Kwitansi (`/pegawai/sppd/{id}/kwitansi`)
**Deskripsi:** Form Kwitansi berdasarkan tipe perjalanan

**Komponen:**
1. **Info SPPD (Top Card)**

2. **Dynamic Form based on Tipe**

**A. Dalam Daerah:**
- Biaya Lumsum (readonly, auto-filled sesuai peraturan)
- Keterangan (textarea)

**B. Luar Daerah Dalam Provinsi:**
- Biaya Perjalanan:
  - Nominal* (input number, format rupiah)
  - Upload Bukti* (foto struk/kwitansi)
  - Keterangan
- Biaya Lumsum (readonly)
- Biaya Penginapan:
  - Nominal*
  - Upload Bukti* (foto bill hotel)
  - Keterangan
- Total (readonly, auto-sum)

**C. Luar Daerah Luar Provinsi:**
- Semua field di atas, plus:
- Biaya Taxi:
  - Nominal
  - Upload Bukti (optional)
- Biaya Tiket:
  - Nominal*
  - Upload Bukti* (foto tiket pesawat/kereta)
- Total (readonly, auto-sum)

3. **Upload Bukti Component:**
   - Drag & drop atau click
   - Preview image/PDF
   - Button: Remove, View Full

4. **Summary Section:**
   - Anggaran SPPD: Rp xxx
   - Total Penggunaan: Rp xxx
   - Sisa/Selisih: Rp xxx (color: green jika <, red jika >)

5. **Button:**
   - Simpan sebagai Draft
   - Submit untuk Verifikasi (konfirmasi)

**Validation:**
- Semua bukti wajib diupload sesuai tipe
- Total tidak boleh > anggaran (alert warning)
- Format file: JPG, JPEG, PNG, PDF
- Max size: 2 MB per file

**Fitur:**
- Auto-calculate total saat input
- Preview all bukti dalam modal

---

### PG-5. Detail SPPD Saya (`/pegawai/sppd/{id}`)
**Deskripsi:** Halaman detail lengkap SPPD

**Komponen:**
1. **Tab Navigation:**
   - Info SPPD
   - Nota Dinas
   - LPPD
   - Kwitansi
   - Dokumentasi

2. **Tab Content:**
   - **Info SPPD:** Display all data SPPD (readonly)
   - **Nota Dinas:** Embed PDF, button download/print
   - **LPPD:** Display LPPD, jika belum ada: button "Isi LPPD"
   - **Kwitansi:** Display kwitansi, jika belum ada: button "Isi Kwitansi"
   - **Dokumentasi:** Gallery foto yang diupload

---

## E. KEUANGAN

### KU-1. Dashboard (`/keuangan/dashboard`)
**Deskripsi:** Overview verifikasi & keuangan

**Widgets:**
1. **Verification Stats (4 cards)**
   - Menunggu Verifikasi (badge count)
   - Diverifikasi Bulan Ini
   - Total Pencairan Bulan Ini (rupiah)
   - Ditolak/Return (badge count)

2. **Charts**
   - Line Chart: Trend pencairan per bulan
   - Pie Chart: Verifikasi per bidang

3. **Urgent Table**
   - SPPD yang sudah submit > 3 hari belum diverifikasi
   - Highlight row merah

---

### KU-2. Verifikasi SPPD (`/keuangan/verifikasi`)
**Deskripsi:** Daftar SPPD yang perlu verifikasi

**Komponen:**
1. **Tabs**
   - Menunggu Verifikasi (badge)
   - Diverifikasi
   - Ditolak/Return

2. **DataTable**
   - Kolom: No SPPD, Bidang, Pegawai, Tujuan, Tanggal Submit, Total Biaya, Action
   - Action:
     - Verifikasi (icon check, ke halaman verifikasi)
     - View (icon eye, modal quick view)

---

### KU-3. Halaman Verifikasi SPPD (`/keuangan/verifikasi/{id}`)
**Deskripsi:** Halaman detail verifikasi

**Layout:** Split screen (Left: Documents, Right: Verification Form)

**Left Panel:**
- **Tabs:**
  - SPPD (PDF embed)
  - Nota Dinas (PDF embed)
  - LPPD (readonly display)
  - Kwitansi (table + bukti)
  - Dokumentasi (gallery)

**Right Panel:**
- **Info Card:** No SPPD, Pegawai, Tujuan, Total Biaya

- **Checklist Verification:**
  - [ ] LPPD lengkap dan sesuai
  - [ ] Kwitansi lengkap
  - [ ] Bukti valid dan sesuai
  - [ ] Jumlah biaya sesuai
  - [ ] Dokumentasi mencukupi

- **Form:**
  - Catatan Verifikasi (textarea)
  - Status: Approve / Reject (radio)
  - Jika Reject: Catatan Penolakan* (textarea, min 20 karakter)

- **Button:**
  - Submit Verifikasi (konfirmasi)
  - Return ke Pegawai (modal catatan)
  - Cancel

**Fitur:**
- Zoom in/out untuk bukti foto
- Download semua dokumen (zip)
- Highlight discrepancy (biaya > anggaran)

---

### KU-4. Laporan Keuangan (`/keuangan/laporan`)
**Deskripsi:** Generate laporan keuangan

**Komponen:**
1. **Filter Section**
   - Periode: Date Range
   - Bidang: Multi-select
   - Status: Verified / All
   - Tipe Laporan: Summary / Detail

2. **Button:**
   - Generate Laporan
   - Export Excel
   - Export PDF

3. **Preview Laporan:**
   - **Summary Table:**
     - Total SPPD
     - Total Anggaran
     - Total Pencairan
     - Per Bidang breakdown
   - **Detail Table:**
     - List semua SPPD dengan biaya detail
   - **Charts:**
     - Bar: Pencairan per Bidang
     - Line: Trend per bulan

**Teknologi:** mPDF untuk PDF export, PHPExcel/PhpSpreadsheet untuk Excel

---

## ADDITIONAL PAGES

### Profile Settings (`/profile`)
- Form update profile (Nama, Email, Foto)
- Change password
- Notification settings

### Notification Center (`/notifications`)
- List semua notifikasi
- Mark as read
- Filter by type

### 404 Page
- Friendly message
- Back to dashboard button

---

## RESPONSIVE DESIGN

### Mobile View (< 768px)
- Sidebar collapse jadi hamburger menu
- Cards stack vertical
- DataTable scroll horizontal atau card view
- Form fields full width
- Touch-friendly buttons (min 44px)

### Tablet View (768px - 1024px)
- Sidebar semi-collapse (icon only)
- 2 columns grid
- Optimized spacing

### Desktop View (> 1024px)
- Full layout
- 3-4 columns grid
- Sidebar expanded

---

## UI/UX GUIDELINES

### Color Scheme
- Primary: Blue (#3B82F6)
- Success: Green (#10B981)
- Warning: Yellow (#F59E0B)
- Danger: Red (#EF4444)
- Info: Cyan (#06B6D4)
- Secondary: Gray (#6B7280)

### Typography
- Font: Inter / Poppins
- Heading: Bold, 24-32px
- Body: Regular, 14-16px
- Small: 12px

### Spacing
- Consistent padding: 1rem, 1.5rem, 2rem
- Card shadow: soft drop shadow
- Border radius: 0.5rem

### Feedback
- Loading: Spinner + "Memproses..."
- Success: SweetAlert2 success icon
- Error: SweetAlert2 error dengan message
- Validation: Red text below field

### Animation
- Smooth transitions (300ms)
- Hover effects pada buttons/cards
- Page transitions fade-in