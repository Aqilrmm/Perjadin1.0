graph TD
    A[Start - User Access System] --> B{Authenticated?}
    B -->|No| C[Login Page]
    C --> D{Valid Credentials?}
    D -->|No| E{Attempts < 3?}
    E -->|Yes| C
    E -->|No| F[Block Account 15 min]
    F --> G[Log Security Event]
    D -->|Yes| H[Create Session]
    
    B -->|Yes| I{Check Role}
    
    I -->|SuperAdmin| J[SuperAdmin Dashboard]
    J --> J1[Kelola User]
    J --> J2[Kelola Bidang]
    J --> J3[View Logs]
    J --> J4[Block User]
    J --> J5[View All Features]
    
    I -->|KepalaDinas| K[KepalaDinas Dashboard]
    K --> K1{Select Action}
    K1 --> K2[Review Program]
    K2 --> K3{Approve?}
    K3 -->|Yes| K4[Set Status Approved]
    K3 -->|No| K5[Set Status Rejected]
    K4 --> K6[Notify Kepala Bidang]
    K5 --> K6
    
    K1 --> K7[Review SPPD]
    K7 --> K8{Approve?}
    K8 -->|Yes| K9[Generate Nota Dinas]
    K9 --> K10[Generate PDF]
    K10 --> K11[Set Status Approved]
    K8 -->|No| K12[Set Status Rejected]
    K11 --> K13[Notify Kepala Bidang]
    K12 --> K13
    
    I -->|KepalaBidang| L[KepalaBidang Dashboard]
    L --> L1{Select Action}
    L1 --> L2[Buat Program]
    L2 --> L3[Fill Form Program]
    L3 --> L4{Valid?}
    L4 -->|No| L3
    L4 -->|Yes| L5[Submit Program]
    L5 --> L6[Status: Pending]
    L6 --> L7[Wait Approval]
    
    L1 --> L8[Buat Kegiatan]
    L8 --> L9{Program Approved?}
    L9 -->|No| L10[Show Error Message]
    L9 -->|Yes| L11[Select Program]
    L11 --> L12[Fill Form Kegiatan]
    L12 --> L13{Valid?}
    L13 -->|No| L12
    L13 -->|Yes| L14[Submit Kegiatan]
    
    L1 --> L15[Buat Sub Kegiatan]
    L15 --> L16{Kegiatan Approved?}
    L16 -->|No| L17[Show Error]
    L16 -->|Yes| L18[Fill Form]
    L18 --> L19[Submit]
    
    L1 --> L20[Buat SPPD]
    L20 --> L21{Sub Kegiatan Approved?}
    L21 -->|No| L22[Show Error]
    L21 -->|Yes| L23[Select Sub Kegiatan]
    L23 --> L24[Fill SPPD Form]
    L24 --> L25[Select Tipe Perjalanan]
    L25 --> L26[Input Detail]
    L26 --> L27[Select Pegawai]
    L27 --> L28{Valid?}
    L28 -->|No| L24
    L28 -->|Yes| L29[Submit SPPD]
    L29 --> L30[Status: Pending]
    
    I -->|Pegawai| M[Pegawai Dashboard]
    M --> M1[View My SPPD]
    M1 --> M2{SPPD Status?}
    M2 -->|Approved| M3[Download Nota Dinas]
    M2 -->|Approved| M4{After Travel Date?}
    M4 -->|Yes| M5[Fill LPPD]
    M5 --> M6[Upload Dokumentasi]
    M6 --> M7[Fill Kwitansi]
    
    M7 --> M8{Tipe Perjalanan?}
    M8 -->|Dalam Daerah| M9[Input Lumsum Only]
    M8 -->|Luar Daerah Dalam Prov| M10[Input Perjalanan + Lumsum + Penginapan]
    M10 --> M11[Upload Bukti]
    M8 -->|Luar Daerah Luar Prov| M12[Input All Costs]
    M12 --> M13[Upload All Bukti]
    
    M9 --> M14{Complete?}
    M11 --> M14
    M13 --> M14
    M14 -->|No| M5
    M14 -->|Yes| M15[Submit for Verification]
    M15 --> M16[Status: Submitted]
    M16 --> M17[Notify Keuangan]
    
    I -->|Keuangan| N[Keuangan Dashboard]
    N --> N1[View Submitted SPPD]
    N1 --> N2[Select SPPD]
    N2 --> N3[Review LPPD]
    N3 --> N4[Check Kwitansi]
    N4 --> N5[Verify Bukti]
    N5 --> N6{All Valid?}
    N6 -->|Yes| N7[Approve Final]
    N7 --> N8[Status: Verified]
    N8 --> N9[Ready for Payment]
    N6 -->|No| N10[Reject with Notes]
    N10 --> N11[Status: Need Revision]
    N11 --> N12[Notify Pegawai]
    N12 --> M5
    
    N --> N13[Generate Laporan Keuangan]
    N13 --> N14[Select Period]
    N14 --> N15[Generate Report]
    N15 --> N16[Export PDF/Excel]
    
    J5 --> Z[Logout]
    K13 --> Z
    L30 --> Z
    M17 --> Z
    N9 --> Z
    Z --> AA[End Session]
    AA --> AB[Log Activity]