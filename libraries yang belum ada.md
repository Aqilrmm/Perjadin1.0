# Overview Libraries yang Belum Ada


## 3. **App/Libraries/Notification/EmailService.php**

### Connected Files:
- Controllers: `AuthController`, `ForgotPasswordController`, approval controllers
- Libraries: `NotificationService`
- Views: `emails/*` templates

### Methods:
- `send($to, $subject, $message)` - Kirim email basic
- `sendTemplate($to, $subject, $template, $data)` - Kirim dengan template
- `sendBulk($recipients, $subject, $message)` - Kirim ke multiple recipients
- `setFrom($email, $name)` - Set sender info
- `addAttachment($filepath)` - Attach file ke email
- `validateEmail($email)` - Validasi format email
- `queue($to, $subject, $message)` - Queue email untuk dikirim later

---

## 4. **App/Libraries/PDF/PDFGenerator.php**

### Connected Files:
- Controllers: `ApprovalSPPDController`, `VerifikasiController`, `LaporanController`
- Libraries: `NotaDinasGenerator`, `SPPDPDFTemplate`
- Models: `SPPDModel`, `LPPDModel`, `KwitansiModel`

### Methods:
- `generate($html)` - Generate PDF dari HTML
- `setHeader($html)` - Set PDF header
- `setFooter($html)` - Set PDF footer
- `setWatermark($text)` - Add watermark
- `output($filename, $mode)` - Output PDF (I/D/F/S)
- `addPage()` - Add new page
- `setPageSize($size)` - Set page size (A4, Letter, etc)
- `setOrientation($orientation)` - Portrait/Landscape
- `setMargins($top, $right, $bottom, $left)` - Set margins

---

## 5. **App/Libraries/PDF/SPPDPDFTemplate.php**

### Connected Files:
- Controllers: `SPPDController`, `MySPPDController`
- Libraries: `PDFGenerator`
- Models: `SPPDModel`, `SPPDPegawaiModel`

### Methods:
- `generateSPPD($sppdId)` - Generate SPPD document
- `generateLPPD($sppdId)` - Generate LPPD document
- `generateKwitansi($sppdId)` - Generate Kwitansi document
- `generatePackage($sppdId)` - Generate semua dokumen dalam satu PDF
- `getTemplate($type)` - Get template HTML untuk tipe dokumen
- `fillTemplate($template, $data)` - Fill template dengan data
- `formatData($sppd)` - Format data SPPD untuk template

---

## 6. **App/Libraries/Logger/ActivityLogger.php**

### Connected Files:
- Controllers: Semua controllers
- Models: `SecurityLogModel`
- Filters: `AuthFilter`, `RoleFilter`

### Methods:
- `log($action, $description, $userId)` - Log activity
- `logCRUD($action, $table, $recordId, $userId)` - Log CRUD operations
- `logLogin($userId, $success)` - Log login attempt
- `logLogout($userId)` - Log logout
- `logAccess($userId, $resource)` - Log resource access
- `logError($error, $userId)` - Log error
- `getIpAddress()` - Get user IP
- `getUserAgent()` - Get user agent

---

## 7. **App/Libraries/SPPD/SPPDGenerator.php** *(Noted: Already implemented as NotaDinasGenerator)*

### Connected Files:
- Controllers: `ApprovalSPPDController`, `SPPDController`
- Models: `SPPDModel`

### Methods:
- `generateNoSPPD($bidangId)` - Generate nomor SPPD otomatis (jika dibutuhkan)
- `suggestNoSPPD($bidangId)` - Suggest nomor SPPD berikutnya
- `validateNoSPPD($noSppd)` - Validasi format nomor SPPD
- `formatNoSPPD($bidangKode, $bulan, $tahun, $urut)` - Format nomor SPPD

---

## Prioritas Implementasi:

### Priority 1 (Critical):
1. **FileUploadService** - Dipakai hampir semua upload functionality
2. **NotificationService** - Core untuk notification system
3. **PDFGenerator** - Essential untuk generate dokumen

### Priority 2 (Important):
4. **SPPDPDFTemplate** - Generate SPPD documents
5. **ActivityLogger** - Audit trail requirement
6. **EmailService** - Notification via email

### Priority 3 (Optional):
7. **SPPDGenerator** - Helper untuk generate nomor (bisa manual input dulu)

---

## Notes:
- Semua library menggunakan **OOP pattern**
- Implement **Dependency Injection** ready
- Semua method harus **return typed values**
- Include **error handling** dan **logging**
- Support **method chaining** where applicable
- Gunakan **helper functions** jika sudah ada