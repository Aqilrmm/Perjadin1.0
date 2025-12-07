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


## Notes:
- Semua library menggunakan **OOP pattern**
- Implement **Dependency Injection** ready
- Semua method harus **return typed values**
- Include **error handling** dan **logging**
- Support **method chaining** where applicable
- Gunakan **helper functions** jika sudah ada