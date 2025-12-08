# API Endpoints Documentation - Aplikasi Perjadin

## Base URL
```
Development: http://localhost/perjadin
Production: https://perjadin.yourdomain.com
```

---

## 🔐 AUTHENTICATION ENDPOINTS

### 1. Login
```
POST /auth/login
```
**Request Body:**
```json
{
  "nip_nik": "199001012020011001",
  "password": "password123",
  "remember_me": true
}
```
**Response (200 OK):**
```json
{
  "status": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "nip_nik": "199001012020011001",
      "nama": "John Doe",
      "role": "kepalabidang",
      "bidang": "IT",
      "foto": "uploads/foto_profile/john.jpg"
    },
    "redirect": "/kepalabidang/dashboard"
  }
}
```

### 2. Logout
```
POST /auth/logout
```
**Response (200 OK):**
```json
{
  "status": true,
  "message": "Logout berhasil"
}
```

### 3. Check Session
```
GET /auth/check-session
```
**Response (200 OK):**
```json
{
  "status": true,
  "authenticated": true,
  "user": {
    "id": 1,
    "nama": "John Doe",
    "role": "kepalabidang"
  }
}
```

---

## 👤 USER MANAGEMENT (Super Admin)

### 1. Get All Users (DataTable)
```
POST /api/superadmin/users/datatable
```
**Request Body:**
```json
{
  "draw": 1,
  "start": 0,
  "length": 10,
  "search": {"value": ""},
  "order": [{"column": 0, "dir": "asc"}],
  "columns": [
    {"data": "nip_nik", "searchable": true},
    {"data": "nama", "searchable": true}
  ],
  "filters": {
    "role": "pegawai",
    "bidang_id": 1,
    "status": "active"
  }
}
```
**Response (200 OK):**
```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 50,
  "data": [
    {
      "id": 1,
      "nip_nik": "199001012020011001",
      "nama": "Dr. John Doe, M.Kom",
      "jabatan": "Kepala Bidang IT",
      "bidang": "Teknologi Informasi",
      "role": "kepalabidang",
      "status": "Active",
      "action": "<button>Edit</button><button>Delete</button>"
    }
  ]
}
```

### 2. Create User
```
POST /api/superadmin/users/create
```
**Request Body (multipart/form-data):**
```json
{
  "nip_nik": "199001012020011002",
  "gelar_depan": "Dr.",
  "nama": "Jane Smith",
  "gelar_belakang": "M.Kom",
  "jenis_pegawai": "ASN",
  "email": "jane@example.com",
  "password": "password123",
  "jabatan": "Staff IT",
  "bidang_id": 1,
  "role": "pegawai",
  "foto": "<file>"
}
```
**Response (201 Created):**
```json
{
  "status": true,
  "message": "User berhasil ditambahkan",
  "data": {
    "id": 2,
    "uuid": "550e8400-e29b-41d4-a716-446655440000"
  }
}
```

### 3. Update User
```
PUT /api/superadmin/users/update/{id}
```
**Request Body:** (Same as Create, except password is optional)

### 4. Delete User
```
DELETE /api/superadmin/users/delete/{id}
```
**Response (200 OK):**
```json
{
  "status": true,
  "message": "User berhasil dihapus"
}
```

### 5. Block/Unblock User
```
POST /api/superadmin/users/block/{id}
```
**Request Body:**
```json
{
  "action": "block",
  "reason": "Melanggar kebijakan"
}
```

---

## 🏢 BIDANG MANAGEMENT

### 1. Get All Bidang (DataTable)
```
POST /api/superadmin/bidang/datatable
```

### 2. Create Bidang
```
POST /api/superadmin/bidang/create
```
**Request Body:**
```json
{
  "kode_bidang": "IT",
  "nama_bidang": "Bidang Teknologi Informasi",
  "keterangan": "Mengelola infrastruktur IT"
}
```

### 3. Get Bidang Options (for Select2)
```
GET /api/bidang/options?search={query}
```
**Response:**
```json
{
  "results": [
    {"id": 1, "text": "Bidang IT"},
    {"id": 2, "text": "Bidang Keuangan"}
  ]
}
```

---

## 📊 PROGRAM MANAGEMENT (Kepala Bidang)

### 1. Get My Programs
```
GET /api/kepalabidang/programs?status={status}&tahun={tahun}
```
**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "kode_program": "PROG-IT-2024-001",
      "nama_program": "Pengembangan Sistem Informasi",
      "tahun_anggaran": 2024,
      "jumlah_anggaran": 500000000,
      "sisa_anggaran": 300000000,
      "status": "approved"
    }
  ]
}
```

### 2. Create Program
```
POST /api/kepalabidang/programs/create
```
**Request Body:**
```json
{
  "nama_program": "Pengembangan Sistem Informasi",
  "tahun_anggaran": 2024,
  "jumlah_anggaran": 500000000,
  "deskripsi": "Program untuk pengembangan SI instansi",
  "save_as_draft": false
}
```

### 3. Submit Program
```
POST /api/kepalabidang/programs/submit/{id}
```

### 4. Get Approved Programs (for dropdown)
```
GET /api/programs/approved?bidang_id={id}
```

---

## 🎯 KEGIATAN MANAGEMENT

### 1. Create Kegiatan
```
POST /api/kepalabidang/kegiatan/create
```
**Request Body:**
```json
{
  "program_id": 1,
  "nama_kegiatan": "Workshop Pengembangan Web",
  "anggaran_kegiatan": 50000000,
  "deskripsi": "Workshop untuk staff IT",
  "save_as_draft": false
}
```
**Validation Response (422):**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "anggaran_kegiatan": ["Anggaran melebihi sisa anggaran program"]
  }
}
```

### 2. Get Approved Kegiatan
```
GET /api/kegiatan/approved?program_id={id}
```

---

## 📑 SUB KEGIATAN MANAGEMENT

Similar structure to Kegiatan endpoints

---

## ✈️ SPPD MANAGEMENT

### 1. Create SPPD (Wizard Steps)

**Step 1: Select Program/Kegiatan/Sub Kegiatan**
```
POST /api/kepalabidang/sppd/validate-step1
```
**Request Body:**
```json
{
  "program_id": 1,
  "kegiatan_id": 1,
  "sub_kegiatan_id": 1
}
```
**Response:**
```json
{
  "status": true,
  "data": {
    "program": {...},
    "kegiatan": {...},
    "sub_kegiatan": {...},
    "sisa_anggaran": 50000000
  }
}
```

**Step 2: Detail Perjalanan**
```
POST /api/kepalabidang/sppd/validate-step2
```
**Request Body:**
```json
{
  "tipe_perjalanan": "Luar Daerah Dalam Provinsi",
  "maksud_perjalanan": "Menghadiri seminar nasional",
  "dasar_surat": "ST/001/IT/2024",
  "file_surat_tugas": "<file>",
  "alat_angkut": "Mobil Pribadi",
  "tempat_berangkat": "Kantor Dinas",
  "tempat_tujuan": "Jakarta",
  "tanggal_berangkat": "2024-02-01",
  "lama_perjalanan": 3
}
```

**Step 3: Select Pegawai**
```
POST /api/kepalabidang/sppd/validate-step3
```
**Request Body:**
```json
{
  "pegawai_ids": [1, 2, 3, 4]
}
```
**Validation Response:**
```json
{
  "status": true,
  "warnings": [
    {
      "pegawai_id": 2,
      "message": "Pegawai ini memiliki SPPD yang overlap pada tanggal tersebut"
    }
  ],
  "conflicts": []
}
```

**Step 4: Estimasi Biaya**
```
POST /api/kepalabidang/sppd/validate-step4
```
**Request Body:**
```json
{
  "biaya_per_pegawai": {
    "perjalanan": 500000,
    "lumsum": 300000,
    "penginapan": 400000
  },
  "jumlah_pegawai": 4
}
```
**Response:**
```json
{
  "status": true,
  "data": {
    "total_estimasi": 4800000,
    "sisa_anggaran": 50000000,
    "can_proceed": true
  }
}
```

**Step 5: Submit SPPD**
```
POST /api/kepalabidang/sppd/submit
```
**Request Body:** (All data from step 1-4)

### 2. Get SPPD List
```
POST /api/sppd/datatable
```
**Query Parameters:**
- `role`: kepalabidang | pegawai | kepaladinas | keuangan
- `bidang_id`: filter by bidang
- `status`: filter by status
- `periode`: date range

### 3. Get SPPD Detail
```
GET /api/sppd/detail/{id}
```
**Response:**
```json
{
  "status": true,
  "data": {
    "sppd": {...},
    "program": {...},
    "kegiatan": {...},
    "sub_kegiatan": {...},
    "pegawai_list": [...],
    "lppd": {...},
    "kwitansi": {...},
    "can_edit_lppd": true,
    "can_edit_kwitansi": true
  }
}
```

### 4. Preview SPPD (PDF)
```
GET /api/sppd/preview/{id}
```
**Response:** PDF Stream

### 5. Download Nota Dinas
```
GET /api/sppd/nota-dinas/{id}
```
**Response:** PDF Stream

---

## 📝 LPPD (Pegawai)

### 1. Save LPPD (Draft)
```
POST /api/pegawai/lppd/save/{sppd_id}
```
**Request Body (multipart/form-data):**
```json
{
  "hasil_kegiatan": "Kegiatan berjalan lancar...",
  "hambatan": "Tidak ada hambatan",
  "saran": "Perlu ditingkatkan...",
  "dokumentasi[]": ["<file1>", "<file2>"]
}
```

### 2. Submit LPPD
```
POST /api/pegawai/lppd/submit/{sppd_id}
```

### 3. Get LPPD
```
GET /api/lppd/{sppd_id}
```

---

## 💰 KWITANSI (Pegawai)

### 1. Save Kwitansi
```
POST /api/pegawai/kwitansi/save/{sppd_id}
```
**Request Body (multipart/form-data):**
```json
{
  "biaya_perjalanan": 500000,
  "bukti_perjalanan": "<file>",
  "keterangan_perjalanan": "Bensin PP Jakarta",
  "biaya_lumsum": 300000,
  "biaya_penginapan": 400000,
  "bukti_penginapan": "<file>",
  "biaya_taxi": 100000,
  "bukti_taxi": "<file>",
  "biaya_tiket": 1000000,
  "bukti_tiket": "<file>"
}
```
**Response:**
```json
{
  "status": true,
  "message": "Kwitansi berhasil disimpan",
  "data": {
    "total_biaya": 2300000,
    "estimasi_biaya": 2400000,
    "selisih": 100000
  }
}
```

### 2. Submit Kwitansi
```
POST /api/pegawai/kwitansi/submit/{sppd_id}
```

---

## ✅ APPROVAL ENDPOINTS (Kepala Dinas)

### 1. Approve Program
```
POST /api/kepaladinas/programs/approve/{id}
```
**Request Body:**
```json
{
  "action": "approve",
  "catatan": "Disetujui untuk dilaksanakan"
}
```

### 2. Reject Program
```
POST /api/kepaladinas/programs/reject/{id}
```
**Request Body:**
```json
{
  "action": "reject",
  "catatan": "Anggaran terlalu besar, perlu revisi"
}
```

### 3. Approve SPPD (Generate Nota Dinas)
```
POST /api/kepaladinas/sppd/approve/{id}
```
**Response:**
```json
{
  "status": true,
  "message": "SPPD berhasil disetujui",
  "data": {
    "no_sppd": "SPPD/IT/XII/2024/001",
    "nota_dinas_url": "/downloads/nota-dinas/xxx.pdf"
  }
}
```

---

## 💵 VERIFIKASI (Keuangan)

### 1. Get SPPD for Verification
```
GET /api/keuangan/verifikasi/pending
```

### 2. Verify SPPD
```
POST /api/keuangan/verifikasi/approve/{id}
```
**Request Body:**
```json
{
  "catatan_verifikasi": "Semua dokumen lengkap dan valid",
  "checklist": {
    "lppd_lengkap": true,
    "kwitansi_lengkap": true,
    "bukti_valid": true,
    "jumlah_sesuai": true,
    "dokumentasi_cukup": true
  }
}
```

### 3. Return/Reject SPPD
```
POST /api/keuangan/verifikasi/reject/{id}
```
**Request Body:**
```json
{
  "catatan_penolakan": "Bukti penginapan tidak jelas, harap upload ulang",
  "return_to": "pegawai"
}
```

---

## 📈 ANALYTICS ENDPOINTS

### 1. Dashboard Stats (Role-based)
```
GET /api/analytics/dashboard-stats?role={role}&bidang_id={id}
```
**Response:**
```json
{
  "status": true,
  "data": {
    "total_sppd": 120,
    "total_anggaran": 500000000,
    "total_terpakai": 350000000,
    "sppd_bulan_ini": 15,
    "pending_approval": 5,
    "charts": {
      "sppd_per_bulan": [...],
      "sppd_per_tipe": [...]
    }
  }
}
```

### 2. Get Chart Data
```
GET /api/analytics/chart/{type}?params
```
**Types:** `sppd_trend`, `sppd_per_bidang`, `budget_realization`, `status_distribution`

---

## 📂 FILE UPLOAD

### 1. Upload File
```
POST /api/upload/file
```
**Request Body (multipart/form-data):**
```json
{
  "file": "<file>",
  "type": "surat_tugas | bukti_perjalanan | bukti_penginapan | dokumentasi",
  "max_size": 2048
}
```
**Response:**
```json
{
  "status": true,
  "message": "File berhasil diupload",
  "data": {
    "filename": "surat_tugas_xxx.pdf",
    "path": "uploads/surat_tugas/surat_tugas_xxx.pdf",
    "size": 1024567,
    "url": "http://localhost/perjadin/uploads/surat_tugas/surat_tugas_xxx.pdf"
  }
}
```

### 2. Delete File
```
DELETE /api/upload/file
```
**Request Body:**
```json
{
  "path": "uploads/surat_tugas/surat_tugas_xxx.pdf"
}
```

---

## 🔔 NOTIFICATIONS

### 1. Get User Notifications
```
GET /api/notifications?limit=10&unread_only=true
```
**Response:**
```json
{
  "status": true,
  "data": {
    "total": 25,
    "unread": 5,
    "notifications": [
      {
        "id": 1,
        "type": "approval",
        "title": "SPPD Disetujui",
        "message": "SPPD Anda dengan nomor SPPD/IT/XII/2024/001 telah disetujui",
        "link": "/pegawai/sppd/1",
        "is_read": false,
        "created_at": "2024-01-01 10:00:00"
      }
    ]
  }
}
```

### 2. Mark as Read
```
POST /api/notifications/read/{id}
```

### 3. Mark All as Read
```
POST /api/notifications/read-all
```

---

## 📊 REPORTS

### 1. Generate Report
```
POST /api/reports/generate
```
**Request Body:**
```json
{
  "type": "keuangan | sppd | program",
  "periode": {
    "start": "2024-01-01",
    "end": "2024-12-31"
  },
  "filters": {
    "bidang_id": [1, 2],
    "status": ["verified"],
    "tipe_perjalanan": ["Luar Daerah Dalam Provinsi"]
  },
  "format": "pdf | excel"
}
```
**Response:**
```json
{
  "status": true,
  "data": {
    "report_url": "/downloads/reports/laporan_xxx.pdf",
    "generated_at": "2024-01-01 10:00:00"
  }
}
```

---

## 🔍 SEARCH & AUTOCOMPLETE

### 1. Global Search
```
GET /api/search?q={query}&type={type}&limit=10
```
**Types:** `users`, `programs`, `sppd`, `all`

### 2. Pegawai Autocomplete
```
GET /api/pegawai/search?q={query}&bidang_id={id}
```
**Response (Select2 format):**
```json
{
  "results": [
    {
      "id": 1,
      "text": "Dr. John Doe, M.Kom - 199001012020011001",
      "jabatan": "Kepala Bidang IT"
    }
  ]
}
```

---

## 🔐 SECURITY

### Authentication
All endpoints (except `/auth/login`) require authentication via session cookie.

### CSRF Protection
All POST/PUT/DELETE requests must include CSRF token:
```
Header: X-CSRF-TOKEN: <token>
or
Body: csrf_token: <token>
```

### Rate Limiting
- Login: 5 attempts per 15 minutes
- API calls: 100 requests per minute per user
- File upload: 10 uploads per minute

### Error Responses

**401 Unauthorized:**
```json
{
  "status": false,
  "message": "Unauthorized",
  "code": 401
}
```

**403 Forbidden:**
```json
{
  "status": false,
  "message": "Anda tidak memiliki akses ke resource ini",
  "code": 403
}
```

**422 Validation Error:**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  },
  "code": 422
}
```

**500 Server Error:**
```json
{
  "status": false,
  "message": "Internal server error",
  "code": 500
}
```