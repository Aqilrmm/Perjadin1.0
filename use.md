# Panduan Penggunaan Library di Aplikasi Perjadin

Dokumen ini menjelaskan cara menggunakan dan mengubah library utama yang dipakai di aplikasi Perjadin, baik untuk frontend (tampilan) maupun backend (proses server). Ditujukan agar siapapun, termasuk pemula, bisa memahami dan mengembangkan aplikasi ini.

---

## 1. Struktur File

- Semua library frontend di-load otomatis melalui file `app/Views/layouts/main.php` (halaman utama) dan `app/Views/layouts/auth.php` (halaman login).
- Untuk menambah, menghapus, atau mengubah library, cukup edit file layout tersebut.

---

## 2. Cara Menambah/Mengubah Library

### a. Menambah Library Baru

1. Cari link CDN (contoh: https://cdn.jsdelivr.net/npm/sweetalert2@11) dari dokumentasi library.
2. Tambahkan kode berikut di `<head>` atau sebelum `</body>` pada file layout:
   ```html
   <!-- Contoh: SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   ```
3. Simpan file. Library siap digunakan di semua halaman.

### b. Menggunakan Library di Halaman

- Untuk library yang butuh inisialisasi (misal DataTables, Select2, Flatpickr, Chart.js), tambahkan script JS di file view terkait.
- Contoh:

  ```javascript
  // DataTables
  $(document).ready(function () {
    $("#tabel-user").DataTable();
  });

  // Select2
  $("#select-bidang").select2();

  // SweetAlert2
  Swal.fire({ title: "Sukses!", text: "Data tersimpan", icon: "success" });
  ```

### c. Validasi Form

- Pastikan form memiliki ID/class yang konsisten.
- Inisialisasi jQuery Validation:
  ```javascript
  $("#form-user").validate({
    rules: { email: { required: true, email: true } },
  });
  ```

### d. Input Tanggal

- Inisialisasi Flatpickr:
  ```javascript
  flatpickr("#tanggal-berangkat", { minDate: "today" });
  ```

### e. Chart.js

- Buat elemen `<canvas id="chart-analytics"></canvas>` di view.
- Inisialisasi Chart.js:
  ```javascript
  new Chart(document.getElementById('chart-analytics'), { type: 'bar', data: {...} });
  ```

### f. Toastify

- Tampilkan notifikasi:
  ```javascript
  Toastify({ text: "Berhasil!", duration: 3000 }).showToast();
  ```

### g. AOS (Animasi Scroll)

- Tambahkan atribut `data-aos` pada elemen yang ingin dianimasikan.
- Inisialisasi di JS:
  ```javascript
  AOS.init();
  ```

### h. Axios (AJAX)

- Untuk request API:
  ```javascript
  axios.get('/api/data').then(res => { ... });
  ```

---

## 3. Library Backend (mPDF, PHPMailer, PhpSpreadsheet)

- Digunakan di Controller/Library, bukan di views.
- Contoh mPDF:
  ```php
  $mpdf = new \Mpdf\Mpdf();
  $mpdf->WriteHTML($html);
  $mpdf->Output();
  ```
- Contoh PHPMailer:
  ```php
  $mail = new PHPMailer\PHPMailer\PHPMailer();
  $mail->addAddress($email);
  $mail->send();
  ```
- Contoh PhpSpreadsheet:
  ```php
  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
  $writer->save('file.xlsx');
  ```

---

## 4. Menambah Library Baru

- Tambahkan CDN di layout.
- Inisialisasi di JS view terkait.
- Pastikan ID/class konsisten agar mudah refactor.

---

## 5. Daftar File Layout Utama

- `app/Views/layouts/main.php` (halaman utama, dashboard, tabel, form, dsb)
- `app/Views/layouts/auth.php` (halaman login, reset password)
- Untuk halaman khusus, cek file di folder `app/Views/[role]/`.

---

## 6. Tips Pengembangan

- Untuk mengubah tampilan/fitur library, cukup edit script di layout atau JS di view terkait.
- Untuk menambah fitur baru, copy pola inisialisasi dari contoh di atas.
- Untuk backend, tambahkan kode di Controller/Library sesuai dokumentasi masing-masing library.

---

## 7. Troubleshooting

- Jika library tidak berjalan, pastikan link CDN sudah benar dan tidak ada typo.
- Cek console browser untuk error JS.
- Pastikan ID/class pada elemen sesuai dengan yang diinisialisasi di JS.

---

## 8. Referensi

- Dokumentasi resmi masing-masing library (lihat link CDN di layout)
- File layout utama (`main.php`, `auth.php`) untuk contoh penggunaan
- File view per halaman untuk inisialisasi spesifik

---

**Dokumen ini dibuat agar siapapun bisa mengembangkan aplikasi Perjadin dengan mudah, tanpa perlu pengalaman coding yang mendalam.**
