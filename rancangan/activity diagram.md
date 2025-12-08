graph TD
    Start([Login]) --> CheckRole{Check Role}
    
    CheckRole -->|SuperAdmin| SA[Dashboard SuperAdmin]
    CheckRole -->|KepalaDinas| KD[Dashboard Kepala Dinas]
    CheckRole -->|KepalaBidang| KB[Dashboard Kepala Bidang]
    CheckRole -->|Pegawai| PG[Dashboard Pegawai]
    CheckRole -->|Keuangan| KU[Dashboard Keuangan]
    
    KB --> KB1[Ajukan Program]
    KB1 --> KB2{Valid?}
    KB2 -->|Yes| KB3[Submit ke Kepala Dinas]
    KB2 -->|No| KB1
    
    KB3 --> KD1[Kepala Dinas Review]
    KD1 --> KD2{Approve?}
    KD2 -->|Yes| KB4[Program Approved]
    KD2 -->|No| KB5[Program Rejected]
    
    KB4 --> KB6[Buat Kegiatan]
    KB6 --> KB7[Submit Kegiatan]
    KB7 --> KD3[Review Kegiatan]
    KD3 --> KD4{Approve?}
    KD4 -->|Yes| KB8[Kegiatan Approved]
    KD4 -->|No| KB9[Kegiatan Rejected]
    
    KB8 --> KB10[Buat Sub Kegiatan]
    KB10 --> KB11[Submit Sub Kegiatan]
    KB11 --> KD5[Review Sub Kegiatan]
    KD5 --> KD6{Approve?}
    KD6 -->|Yes| KB12[Sub Kegiatan Approved]
    KD6 -->|No| KB13[Sub Kegiatan Rejected]
    
    KB12 --> KB14[Buat SPPD]
    KB14 --> KB15[Isi Detail SPPD]
    KB15 --> KB16[Submit SPPD]
    KB16 --> KD7[Review SPPD]
    KD7 --> KD8{Approve?}
    KD8 -->|Yes| SYS1[System Generate Nota Dinas]
    KD8 -->|No| KB17[SPPD Rejected]
    
    SYS1 --> PG1[Pegawai Terima SPPD]
    PG1 --> PG2[Lakukan Perjalanan Dinas]
    PG2 --> PG3[Upload Bukti Kegiatan]
    PG3 --> PG4[Isi LPPD]
    PG4 --> PG5[Isi Kwitansi]
    PG5 --> PG6{Lengkap?}
    PG6 -->|No| PG3
    PG6 -->|Yes| PG7[Submit untuk Verifikasi]
    
    PG7 --> KU1[Keuangan Review]
    KU1 --> KU2[Verifikasi Dokumen]
    KU2 --> KU3{Valid?}
    KU3 -->|Yes| KU4[Approve Final]
    KU3 -->|No| KU5[Return ke Pegawai]
    KU5 --> PG3
    
    KU4 --> End([Selesai])
    KB5 --> End
    KB9 --> End
    KB13 --> End
    KB17 --> End