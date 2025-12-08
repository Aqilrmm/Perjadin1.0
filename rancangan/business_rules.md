# Business Rules - Aplikasi Perjadin (Updated)

## 1. AUTHENTICATION & AUTHORIZATION

### BR-AUTH-001: Login Rules
- User harus memiliki NIP/NIK dan Password yang valid
- Password minimal 8 karakter
- Maksimal 3x percobaan login gagal, kemudian akun ter-block sementara (15 menit)
- Session timeout setelah 60 menit tidak ada aktivitas
- Setiap login dicatat dalam security logs

### BR-AUTH-002: Role-Based Access Control (RBAC)
- Setiap user hanya memiliki 1 role
- Role: superadmin, kepaladinas, kepalabidang, pegawai, keuangan
- Akses menu/fitur disesuaikan dengan role
- Super Admin dapat mengakses semua fitur

### BR-AUTH-003: Block User
- Hanya Super Admin yang dapat block/unblock user
- User yang di-block tidak dapat login
- Block otomatis setelah 3x percobaan login gagal berturut-turut

---

## 2. SUPER ADMIN

### BR-SA-001: Kelola User
- NIP/NIK harus unique
- NIP untuk ASN (18 digit), NIK untuk Non-ASN (16 digit)
- Email harus unique dan valid format
- Bidang wajib dipilih (kecuali untuk Super Admin, Kepala Dinas, Keuangan)
- UUID di-generate otomatis oleh sistem
- Password default: "password123" (harus diganti saat first login)

### BR-SA-002: Kelola Bidang
- Nama Bidang harus unique
- Kode Bidang harus unique (input manual)
- UUID di-generate otomatis
- Bidang tidak dapat dihapus jika masih ada user atau program terkait
- Dapat di-soft delete (is_active = 0)

### BR-SA-003: Logs Keamanan
- Sistem mencatat semua aktivitas: Login, Logout, CRUD, Approve, Reject
- Log minimal berisi: timestamp, user_id, action, ip_address, user_agent
- Log disimpan permanent (tidak dapat dihapus)
- Filter log berdasarkan: tanggal, user, action type

---

## 3. KEPALA DINAS

### BR-KD-001: Persetujuan Program
- Hanya dapat menyetujui program dengan status "pending"
- Dapat approve atau reject dengan catatan
- Catatan approve optional
- Catatan reject wajib diisi minimal 10 karakter
- Setelah approve, status berubah "approved" dan program dapat digunakan untuk kegiatan
- Notifikasi otomatis ke Kepala Bidang pengaju

### BR-KD-002: Persetujuan Kegiatan
- Hanya kegiatan dari program yang sudah approved
- Status: pending, approved, rejected
- Catatan approve optional
- Catatan reject wajib diisi minimal 10 karakter

### BR-KD-003: Persetujuan Sub Kegiatan
- Hanya sub kegiatan dari kegiatan yang sudah approved
- Catatan approve optional
- **Catatan reject wajib diisi minimal 10 karakter** ✅ (Updated)
- Rules sama dengan persetujuan kegiatan

### BR-KD-004: Persetujuan SPPD
- SPPD hanya dapat disetujui jika Sub Kegiatan sudah approved
- Setelah approve, sistem otomatis generate:
  - Nota Dinas (PDF)
  - Update status SPPD menjadi "approved"
- Preview SPPD dapat dilihat sebelum approve
- Tanggal berangkat minimal H+1 dari tanggal pengajuan
- Catatan approve optional
- Catatan reject wajib diisi minimal 10 karakter

### BR-KD-005: Analytics
- Dapat melihat semua SPPD dari semua bidang
- Filter berdasarkan: Bidang, Periode, Status, Tipe Perjalanan
- Export to Excel/PDF

---

## 4. KEPALA BIDANG

### BR-KB-001: Pengajuan Program
- **Kode Program input manual** ✅ (Updated - bukan auto-generate)
- Format Kode Program bebas (disarankan: PROG-BIDANG-TAHUN-URUT)
- Kode Program harus unique
- Nama Program wajib diisi minimal 10 karakter
- Bidang otomatis terisi sesuai bidang Kepala Bidang (readonly)
- Tahun Anggaran otomatis terisi tahun saat ini (dapat diubah)
- Jumlah Anggaran minimal Rp 1.000.000
- Deskripsi program optional
- Status default: "pending"
- Dapat disimpan sebagai draft (status: "draft")
- Draft dapat diedit, pending tidak dapat diedit
- Satu program dapat digunakan untuk banyak kegiatan

### BR-KB-002: Buat Kegiatan
- Hanya dapat dibuat dari Program yang statusnya "approved"
- **Kode Kegiatan input manual** ✅ (Updated - bukan auto-generate)
- Format Kode Kegiatan bebas (disarankan: KEG-KODE_PROGRAM-URUT)
- Kode Kegiatan harus unique
- Nama Kegiatan wajib diisi minimal 10 karakter
- Anggaran Kegiatan tidak boleh melebihi sisa anggaran Program
- Deskripsi optional
- Satu kegiatan dapat memiliki banyak sub kegiatan
- Status default: "pending"
- Dapat disimpan sebagai draft

### BR-KB-003: Buat Sub Kegiatan
- Hanya dapat dibuat dari Kegiatan yang statusnya "approved"
- **Kode Sub Kegiatan input manual** ✅ (Updated - bukan auto-generate)
- Format Kode Sub Kegiatan bebas (disarankan: SUBKEG-KODE_KEGIATAN-URUT)
- Kode Sub Kegiatan harus unique
- Nama Sub Kegiatan wajib diisi minimal 10 karakter
- Anggaran Sub Kegiatan tidak boleh melebihi sisa anggaran Kegiatan
- Status default: "pending"
- Dapat disimpan sebagai draft

### BR-KB-004: Buat SPPD
**Mandatory Fields:**
- Nomor SPPD (input manual, unique) ✅ (Updated - bukan auto-generate)
- Program, Kegiatan, Sub Kegiatan (harus yang sudah approved)
- Tipe Perjalanan: "Dalam Daerah", "Luar Daerah Dalam Provinsi", "Luar Daerah Luar Provinsi"
- Maksud Perjalanan (minimal 20 karakter)
- Dasar Surat (nomor surat tugas)
- Alat Angkut (Mobil Dinas, Pribadi, Travel, Pesawat, dll)
- Tempat Berangkat
- Tempat Tujuan
- Lama Perjalanan (dalam hari, minimal 1)
- Tanggal Berangkat (minimal H+1 dari hari pengajuan)
- Tanggal Kembali (auto-calculate dari tanggal berangkat + lama perjalanan)
- Penanggung Jawab (default: Kepala Bidang yang membuat, dapat diganti)
- **Pegawai yang melakukan perjalanan (minimal 1, tanpa batas maksimal)** ✅ (Updated)
- **Pegawai boleh dari bidang lain** ✅ (Updated)

**Optional Fields:**
- File Surat Tugas (PDF, max 2MB)

**Validation:**
- Nomor SPPD harus unique di seluruh sistem
- Pegawai tidak boleh memiliki SPPD dengan tanggal yang overlap
- Warning (bukan block) jika pegawai memiliki jadwal overlap
- Penanggung jawab otomatis termasuk dalam daftar pegawai jika belum ditambahkan
- Total estimasi biaya tidak boleh melebihi sisa anggaran Sub Kegiatan
- Sistem memberikan warning jika estimasi biaya > 80% sisa anggaran

### BR-KB-005: Lihat SPPD Bidang
- **Kepala Bidang dapat melihat semua SPPD yang dibuatnya** ✅ (Updated)
- Tidak dapat melihat SPPD yang dibuat oleh Kepala Bidang lain
- Dapat melihat: SPPD, Nota Dinas, LPPD, Kwitansi
- Dapat mencetak dokumen dalam format PDF
- Dapat download semua dokumen dalam ZIP

### BR-KB-006: Analytics SPPD Bidang
- Menampilkan statistik SPPD di bidangnya
- Data: Total SPPD, Total Anggaran, Per Status, Per Tipe Perjalanan
- Chart: Line Chart (trend per bulan), Pie Chart (per tipe)
- Filter periode: bulan, quarter, tahun

---

## 5. PEGAWAI

### BR-PG-001: Lihat SPPD
- Pegawai hanya dapat melihat SPPD dimana dia termasuk dalam daftar pegawai
- Dapat download SPPD dan Nota Dinas (PDF)
- Dapat melihat status perjalanan dinas
- Notifikasi saat SPPD disetujui/ditolak

### BR-PG-002: Buat/Edit LPPD (Laporan Pelaksanaan Perjalanan Dinas)
- LPPD hanya dapat diisi setelah tanggal kembali
- LPPD dapat diisi sampai H+7 setelah tanggal kembali
- Setelah H+7, sistem memberikan warning namun tetap dapat diisi
- Fields LPPD:
  - Hasil Kegiatan (textarea, wajib, minimal 50 karakter)
  - Hambatan (textarea, optional)
  - Saran (textarea, optional)
  - **Dokumentasi Foto (upload, wajib, minimal 1 foto, maksimal 10 foto)** ✅ (Updated)
  - Format foto: JPG, JPEG, PNG
  - Ukuran maksimal per foto: 5 MB
  - Sistem auto-compress foto jika > 5 MB
- Tanggal Pengisian (auto-filled, readonly)
- LPPD dapat di-edit sebelum di-submit
- Setelah submit bersamaan dengan Kwitansi, LPPD tidak dapat di-edit kecuali di-return oleh Keuangan

### BR-PG-003: Isi Kwitansi
**A. Dalam Daerah:**
- Biaya Lumsum (readonly, sesuai peraturan daerah)
- Total otomatis = Lumsum × Lama Perjalanan
- Keterangan (optional)
- Tidak perlu upload bukti

**B. Luar Daerah Dalam Provinsi:**
- Biaya Perjalanan (input manual, wajib)
  - Upload Bukti Perjalanan wajib (foto struk/kwitansi)
  - Keterangan (optional)
- Biaya Lumsum (readonly, sesuai peraturan)
- Biaya Penginapan (input manual, wajib)
  - Upload Bukti Penginapan wajib (foto bill hotel)
  - Keterangan (optional)
- Total otomatis = Perjalanan + Lumsum + Penginapan

**C. Luar Daerah Luar Provinsi:**
- Biaya Perjalanan (input manual, wajib)
  - Upload Bukti wajib
- Biaya Lumsum (readonly, sesuai peraturan)
- Biaya Penginapan (input manual, wajib)
  - Upload Bukti wajib
- Biaya Taxi (input manual, optional)
  - Upload Bukti wajib jika ada input nominal
  - Keterangan (optional)
- Biaya Tiket (input manual, wajib)
  - Upload Bukti Tiket wajib (tiket pesawat/kereta)
  - Keterangan (optional)
- Total otomatis = Perjalanan + Lumsum + Penginapan + Taxi + Tiket

**Validation:**
- Format file bukti: JPG, JPEG, PNG, PDF
- Ukuran maksimal per file: 2 MB
- Total biaya tidak boleh melebihi estimasi biaya SPPD
- Warning (bukan block) jika total > 90% estimasi
- Error jika total > 100% estimasi
- Semua bukti wajib di-upload sesuai tipe perjalanan sebelum submit

### BR-PG-004: Upload Bukti di Tempat Tujuan
- **Wajib upload minimal 1 foto dokumentasi kegiatan** ✅ (Updated)
- Maksimal 10 foto dokumentasi
- Foto sebaiknya memiliki metadata lokasi (GPS) atau keterangan tempat
- Format: JPG, JPEG, PNG
- Maksimal ukuran per foto: 5 MB
- Sistem auto-compress jika > 5 MB
- Dapat menambahkan caption/keterangan per foto

### BR-PG-005: Submit untuk Verifikasi
- Pegawai hanya dapat submit jika:
  - LPPD sudah terisi lengkap
  - Minimal 1 foto dokumentasi sudah di-upload
  - Kwitansi sudah terisi lengkap sesuai tipe perjalanan
  - Semua bukti pengeluaran sudah di-upload
- Setelah submit, status SPPD berubah "submitted" dan menunggu verifikasi Keuangan
- Notifikasi otomatis ke bagian Keuangan
- Pegawai tidak dapat edit LPPD & Kwitansi setelah submit

---

## 6. KEUANGAN

### BR-KU-001: Verifikasi SPPD
- Hanya SPPD dengan status "submitted" yang dapat diverifikasi
- Keuangan memeriksa:
  - Kelengkapan LPPD (minimal 50 karakter hasil kegiatan)
  - Kelengkapan dokumentasi foto (minimal 1 foto)
  - Kelengkapan dan keabsahan bukti pengeluaran
  - Kesesuaian jumlah dengan peraturan
  - Kesesuaian total dengan estimasi biaya
  - Format dan kualitas file bukti

### BR-KU-002: Approval/Rejection
**Approve:**
- Jika semua dokumen valid dan lengkap
- Catatan verifikasi optional
- Status SPPD berubah "verified"
- SPPD siap untuk proses pencairan
- Notifikasi ke Pegawai & Kepala Bidang
- Status final, tidak dapat diubah lagi

**Reject/Return:**
- Jika ada dokumen tidak valid atau tidak lengkap
- Catatan rejection wajib diisi (minimal 20 karakter)
- Harus dijelaskan detail masalah yang ditemukan
- Status SPPD kembali "need_revision"
- Pegawai dapat revisi dan submit ulang
- Notifikasi ke Pegawai dengan catatan detail
- History revision disimpan (tracking)

### BR-KU-003: Laporan Keuangan
- Generate laporan per periode
- Data: Total SPPD, Total Pencairan, Per Bidang, Per Program
- Filter: Tanggal, Bidang, Tipe Perjalanan, Status
- Export to Excel/PDF
- Laporan dapat di-approve oleh Kepala Dinas (optional)
- Include grafik trend pencairan

---

## 7. SYSTEM RULES

### BR-SYS-001: Nomor SPPD
- **Nomor SPPD input manual oleh Kepala Bidang** ✅ (Updated)
- Format bebas (disarankan: SPPD/BIDANG/BULAN/TAHUN/URUT)
- Contoh: SPPD/IT/XII/2024/001
- Nomor harus unique di seluruh sistem
- Validasi real-time saat input (AJAX check duplicate)
- Nomor tidak dapat diubah setelah SPPD di-submit
- Sistem memberikan saran nomor berdasarkan last SPPD (optional helper)

### BR-SYS-002: Nota Dinas
- Generate otomatis setelah Kepala Dinas approve SPPD
- Format PDF dengan template resmi instansi
- Berisi: Nomor SPPD, Tanggal, Perihal, Dasar, Tujuan, Pegawai, Biaya
- Include tanda tangan digital/placeholder
- Nomor Nota Dinas auto-generate (ND/BIDANG/BULAN/TAHUN/URUT)
- Template dapat dikustomisasi oleh Super Admin

### BR-SYS-003: Status Flow SPPD
```
draft → pending → approved → (perjalanan selesai) → submitted → need_revision/verified → closed
                    ↓
                rejected
```

**Status Details:**
- **draft**: SPPD belum di-submit, dapat diedit/dihapus
- **pending**: Menunggu approval Kepala Dinas, tidak dapat diedit
- **approved**: Disetujui, Nota Dinas sudah di-generate, siap berangkat
- **rejected**: Ditolak, harus buat SPPD baru
- **submitted**: Pegawai sudah submit LPPD & Kwitansi, menunggu verifikasi
- **need_revision**: Dikembalikan Keuangan untuk revisi
- **verified**: Diverifikasi Keuangan, siap pencairan
- **closed**: Selesai, sudah dicairkan (optional status)

### BR-SYS-004: Notification Rules
**Email notification untuk:**
- Approval/Rejection Program, Kegiatan, Sub Kegiatan
- Approval/Rejection SPPD
- Submit SPPD untuk verifikasi
- Hasil verifikasi Keuangan (Approve/Return)
- Reminder H-3 sebelum keberangkatan
- Reminder H+7 jika belum submit LPPD

**Browser notification real-time (jika online):**
- Approval/Rejection realtime
- Chat/comment notification
- System announcement

**Notification Settings:**
- User dapat mengatur preferensi notifikasi
- On/Off per jenis notifikasi
- Email frequency (instant, daily digest)

### BR-SYS-005: Data Retention
- Logs: Permanent (tidak dihapus)
- SPPD & Dokumen: Minimal 5 tahun (sesuai regulasi)
- Bukti pengeluaran: Minimal 5 tahun
- Soft delete: Data yang dihapus masih tersimpan (deleted_at)
- Archive otomatis data > 5 tahun (optional)

### BR-SYS-006: Backup & Security
- Database backup harian (automated, 3AM)
- File upload backup mingguan
- Retention backup: 30 hari terakhir
- HTTPS wajib untuk production
- Password hashing menggunakan bcrypt (cost 10)
- Protect dari SQL Injection (Query Builder)
- Protect dari XSS (esc() function)
- CSRF token validation
- Rate limiting: 100 requests/minute per user

---

## 8. ANGGARAN RULES

### BR-ANG-001: Perhitungan Anggaran
- Total anggaran Program = Manual input
- Total anggaran Kegiatan = Manual input (tidak boleh > sisa Program)
- Total anggaran Sub Kegiatan = Manual input (tidak boleh > sisa Kegiatan)
- Total estimasi SPPD = Manual input (tidak boleh > sisa Sub Kegiatan)
- Sistem menghitung sisa anggaran real-time
- Menggunakan Views untuk query efisien

### BR-ANG-002: Sisa Anggaran
- Sistem menampilkan sisa anggaran real-time pada form
- Warning jika sisa anggaran < 20% (badge kuning)
- Error jika anggaran tidak cukup (badge merah)
- Block pembuatan SPPD baru jika sisa anggaran < estimasi biaya
- Dashboard menampilkan grafik budget vs realisasi
- Notifikasi ke Kepala Bidang jika anggaran < 10%

### BR-ANG-003: Validasi Anggaran Bertingkat
```
Program (500 juta)
  └─ Kegiatan 1 (200 juta) ✓ Valid (< 500 juta)
      └─ Sub Kegiatan 1.1 (100 juta) ✓ Valid (< 200 juta)
          └─ SPPD 1 (50 juta) ✓ Valid (< 100 juta)
```

- Validasi cascade dari atas ke bawah
- Tidak boleh ada over-budget di setiap level
- Update anggaran atas mempengaruhi bawah (warning)

---

## 9. PERIODE & TAHUN ANGGARAN

### BR-TA-001: Tahun Anggaran
- Tahun anggaran mengikuti tahun kalender (Januari - Desember)
- SPPD dapat dibuat untuk tahun anggaran yang sedang berjalan
- SPPD dapat dibuat untuk tahun anggaran berikutnya (Q4 current year)
- Program dari tahun lalu dapat di-carry over dengan re-approval
- Anggaran tidak dapat di-carry over otomatis

### BR-TA-002: Cut-off Date
- Pengajuan SPPD baru ditutup per **15 Desember**
- Submit LPPD & Kwitansi ditutup per **20 Desember**
- Verifikasi Keuangan ditutup per **25 Desember**
- Semua SPPD harus selesai diverifikasi per **31 Desember**
- Extension cut-off dapat dilakukan oleh Super Admin (maksimal 7 hari)

### BR-TA-003: Periode Overlap
- Tahun anggaran baru mulai 1 Januari
- Program untuk tahun baru dapat dibuat mulai 1 Oktober tahun sebelumnya
- Approval program tahun baru mulai 1 November
- SPPD untuk tahun baru dapat dibuat mulai 1 Desember

---

## 10. VALIDATION MATRIX

| Field | Required | Min Length | Max Length | Format | Unique | Notes |
|-------|----------|------------|------------|--------|--------|-------|
| NIP/NIK | Yes | 16 | 18 | Numeric | Yes | 18 ASN, 16 Non-ASN |
| Email | Yes | 5 | 100 | Email | Yes | Valid email format |
| Password | Yes | 8 | 255 | - | No | Min 1 uppercase, 1 number |
| Nama Bidang | Yes | 3 | 100 | - | Yes | - |
| Kode Bidang | Yes | 2 | 10 | Alphanumeric | Yes | Uppercase recommended |
| Kode Program | Yes | 5 | 50 | - | Yes | **Manual input** |
| Nama Program | Yes | 10 | 255 | - | No | - |
| Jumlah Anggaran | Yes | 1000000 | 999999999999 | Numeric | No | Min 1 juta |
| Kode Kegiatan | Yes | 5 | 50 | - | Yes | **Manual input** |
| Kode Sub Kegiatan | Yes | 5 | 50 | - | Yes | **Manual input** |
| Nomor SPPD | Yes | 5 | 100 | - | Yes | **Manual input** |
| Maksud Perjalanan | Yes | 20 | 500 | - | No | - |
| Dasar Surat | Yes | 5 | 255 | - | No | Nomor surat |
| Lama Perjalanan | Yes | 1 | 365 | Numeric | No | Dalam hari |
| LPPD Hasil | Yes | 50 | 2000 | - | No | Minimal 50 karakter |
| Dokumentasi Foto | Yes | 1 | 10 | Image | No | **Min 1 foto** |
| Catatan Reject | Yes* | 10 | 500 | - | No | *Wajib saat reject |

---

## 11. FILE UPLOAD RULES

### BR-FILE-001: Surat Tugas
- Format: PDF only
- Size: Max 2 MB
- Naming: surat_tugas_{sppd_id}_{timestamp}.pdf
- Optional (dapat di-upload kemudian)

### BR-FILE-002: Bukti Perjalanan/Penginapan/Taxi/Tiket
- Format: JPG, JPEG, PNG, PDF
- Size: Max 2 MB per file
- Naming: bukti_{type}_{sppd_id}_{timestamp}.{ext}
- Mandatory sesuai tipe perjalanan

### BR-FILE-003: Dokumentasi Foto
- Format: JPG, JPEG, PNG only
- Size: Max 5 MB per file (auto-compress)
- Naming: dokumentasi_{sppd_id}_{sequence}_{timestamp}.{ext}
- Min 1 foto, Max 10 foto
- Caption/keterangan per foto

### BR-FILE-004: Foto Profile User
- Format: JPG, JPEG, PNG only
- Size: Max 2 MB (auto-resize to 300x300)
- Naming: profile_{user_id}_{timestamp}.{ext}
- Optional (default avatar jika tidak ada)

---

## 12. SECURITY RULES

### BR-SEC-001: Password Policy
- Minimal 8 karakter
- Harus mengandung: 1 huruf besar, 1 huruf kecil, 1 angka
- Tidak boleh sama dengan 3 password terakhir
- Expired setiap 90 hari (optional, untuk production)
- Force change password saat first login

### BR-SEC-002: Session Security
- Session timeout: 60 menit inaktif
- Auto-logout jika terdeteksi aktivitas mencurigakan
- Prevent concurrent login dari device berbeda (optional)
- Logging semua aktivitas session

### BR-SEC-003: API Rate Limiting
- Login: 5 attempts per 15 menit per IP
- API calls: 100 requests per menit per user
- File upload: 10 uploads per menit per user
- Exceeded limit: HTTP 429 (Too Many Requests)

---

## 13. ACCESSIBILITY RULES

### BR-ACC-001: Responsive Design
- Mobile-first approach
- Support: Mobile (320px+), Tablet (768px+), Desktop (1024px+)
- Touch-friendly buttons (min 44px)
- Readable font size (min 14px mobile, 16px desktop)

### BR-ACC-002: Browser Support
- Chrome 90+ (primary)
- Firefox 88+
- Safari 14+
- Edge 90+
- No IE support

---

## 14. AUDIT & COMPLIANCE

### BR-AUD-001: Audit Trail
- Semua perubahan data dicatat (who, what, when)
- History approval/rejection tersimpan
- Revision history untuk LPPD & Kwitansi
- Cannot delete audit logs

### BR-AUD-002: Data Privacy
- Data pribadi pegawai protected (GDPR-like)
- Akses data sesuai role (least privilege)
- Enkripsi data sensitif (password)
- Hak akses view/edit/delete terpisah

---

## 15. BUSINESS CONTINUITY

### BR-BC-001: Disaster Recovery
- Database backup harian
- File backup mingguan
- Recovery Point Objective (RPO): 24 jam
- Recovery Time Objective (RTO): 4 jam
- Backup tested monthly

### BR-BC-002: Maintenance Window
- Scheduled maintenance: Minggu, 00:00 - 04:00
- Emergency maintenance: Notifikasi H-1
- System unavailable message saat maintenance

---

## SUMMARY PERUBAHAN DARI VERSI SEBELUMNYA

### ✅ Updated Rules:
1. **BR-KB-001**: Kode Program → Manual input (bukan auto)
2. **BR-KB-002**: Kode Kegiatan → Manual input (bukan auto)
3. **BR-KB-003**: Kode Sub Kegiatan → Manual input (bukan auto)
4. **BR-SYS-001**: Nomor SPPD → Manual input (bukan auto)
5. **BR-KB-004**: Pegawai tanpa batas maksimal, boleh lintas bidang
6. **BR-KB-005**: Kepala Bidang hanya lihat SPPD yang dibuatnya
7. **BR-PG-002**: Dokumentasi foto minimal 1 (bukan 2)
8. **BR-PG-004**: Upload dokumentasi minimal 1 (bukan 2)
9. **BR-KD-003**: Catatan reject wajib minimal 10 karakter

### 📋 New Rules:
- BR-ANG-003: Validasi anggaran bertingkat
- BR-TA-003: Periode overlap tahun anggaran
- BR-FILE-001 s/d BR-FILE-004: File upload rules detail
- BR-SEC-001 s/d BR-SEC-003: Security rules detail
- BR-ACC-001 s/d BR-ACC-002: Accessibility rules
- BR-AUD-001 s/d BR-AUD-002: Audit & compliance
- BR-BC-001 s/d BR-BC-002: Business continuity