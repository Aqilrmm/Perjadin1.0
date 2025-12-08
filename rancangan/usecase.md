# Use Case Diagram - Aplikasi Perjadin

## 1. Super Admin
```
Actor: Super Admin
├── UC-SA-01: Kelola User (CRUD)
├── UC-SA-02: Kelola Bidang (CRUD)
├── UC-SA-03: Monitor Logs Keamanan
├── UC-SA-04: Block/Unblock User
├── UC-SA-05: Lihat Dashboard Super Admin
├── UC-SA-06: Kelola Program (View All)
├── UC-SA-07: Kelola SPPD (View All)
├── UC-SA-08: Kelola Kegiatan (View All)
└── UC-SA-09: Kelola Sub Kegiatan (View All)
```

## 2. Kepala Dinas
```
Actor: Kepala Dinas
├── UC-KD-01: Lihat Dashboard Kepala Dinas
├── UC-KD-02: Approve/Reject Program
├── UC-KD-03: Approve/Reject Kegiatan
├── UC-KD-04: Approve/Reject Sub Kegiatan
├── UC-KD-05: Approve/Reject SPPD
├── UC-KD-06: Preview SPPD (PDF)
└── UC-KD-07: Lihat Analytics Semua SPPD
```

## 3. Kepala Bidang
```
Actor: Kepala Bidang
├── UC-KB-01: Lihat Dashboard Kepala Bidang
├── UC-KB-02: Ajukan Program
├── UC-KB-03: Buat Kegiatan (dari Program Approved)
├── UC-KB-04: Buat Sub Kegiatan (dari Kegiatan Approved)
├── UC-KB-05: Buat SPPD
├── UC-KB-06: Lihat SPPD Bidang
├── UC-KB-07: Lihat Nota Dinas SPPD
├── UC-KB-08: Lihat LPPD SPPD
├── UC-KB-09: Lihat Kwitansi SPPD
└── UC-KB-10: Lihat Analytics SPPD Bidang
```

## 4. Pegawai
```
Actor: Pegawai
├── UC-PG-01: Lihat Dashboard Pegawai
├── UC-PG-02: Lihat SPPD Milik Sendiri
├── UC-PG-03: Lihat Nota Dinas SPPD
├── UC-PG-04: Buat/Edit LPPD
├── UC-PG-05: Isi Kwitansi SPPD
├── UC-PG-06: Upload Bukti Perjalanan
├── UC-PG-07: Upload Bukti Penginapan
├── UC-PG-08: Upload Bukti Taxi
├── UC-PG-09: Upload Bukti Tiket
└── UC-PG-10: Submit SPPD untuk Verifikasi Keuangan
```

## 5. Keuangan
```
Actor: Keuangan
├── UC-KU-01: Lihat Dashboard Keuangan
├── UC-KU-02: Verifikasi SPPD yang Sudah Submit
├── UC-KU-03: Approve/Reject SPPD Final
├── UC-KU-04: Verifikasi Kwitansi & Bukti
└── UC-KU-05: Generate Laporan Keuangan
```

## 6. System (Auto-Generate)
```
Actor: System
├── UC-SYS-01: Generate Nomor SPPD Otomatis
├── UC-SYS-02: Generate Nota Dinas (setelah approve SPPD)
├── UC-SYS-03: Generate PDF SPPD
├── UC-SYS-04: Generate PDF Nota Dinas
├── UC-SYS-05: Update Status SPPD Otomatis
├── UC-SYS-06: Logging Aktivitas User
└── UC-SYS-07: Notifikasi Email/Browser
```

## Relationship Matrix

| Use Case | Super Admin | Kepala Dinas | Kepala Bidang | Pegawai | Keuangan |
|----------|-------------|--------------|---------------|---------|----------|
| Kelola User | ✓ | - | - | - | - |
| Kelola Bidang | ✓ | - | - | - | - |
| Approve Program | - | ✓ | - | - | - |
| Ajukan Program | - | - | ✓ | - | - |
| Buat SPPD | - | - | ✓ | - | - |
| Lihat SPPD | ✓ | ✓ | ✓ | ✓ (own) | ✓ |
| Isi LPPD | - | - | - | ✓ | - |
| Verifikasi Final | - | - | - | - | ✓ |

## Use Case Prioritas (MVP)

### Phase 1 (Critical)
1. UC-SA-01: Kelola User
2. UC-SA-02: Kelola Bidang
3. UC-KB-02: Ajukan Program
4. UC-KD-02: Approve Program

### Phase 2 (High)
5. UC-KB-03: Buat Kegiatan
6. UC-KB-04: Buat Sub Kegiatan
7. UC-KB-05: Buat SPPD
8. UC-KD-05: Approve SPPD

### Phase 3 (Medium)
9. UC-PG-04: Buat LPPD
10. UC-PG-05: Isi Kwitansi
11. UC-KU-02: Verifikasi SPPD

### Phase 4 (Enhancement)
12. Analytics & Reporting
13. Logs & Security
14. Notifikasi System