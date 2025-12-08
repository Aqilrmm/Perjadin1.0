# Teknologi & Library - Aplikasi Perjadin (Updated)

## 🎯 CORE TECHNOLOGIES

### Backend Framework
```
CodeIgniter 4.5.x
```
**Digunakan Untuk:**
- Core framework aplikasi (MVC Pattern)
- Routing management untuk semua halaman
- Database operations (ORM/Query Builder)
- Session & Authentication management
- RESTful API endpoints
- CSRF & XSS protection
- File upload handling
- Email service

**Fitur yang Dipakai:**
- `Controllers`: Auth, Dashboard, CRUD operations
- `Models`: User, Bidang, Program, Kegiatan, SPPD, LPPD, Kwitansi
- `Filters`: AuthFilter, RoleFilter (RBAC)
- `Helpers`: Custom helpers (auth, format, status)
- `Migrations`: Database schema management
- `Validation`: Form & API validation rules

---

### Database
```
MySQL 8.0+ / MariaDB 10.6+
```
**Digunakan Untuk:**
- Menyimpan semua data aplikasi
- Relasi antar tabel (Foreign Keys)
- Transaction support untuk operasi critical
- Full-text search untuk pencarian data
- Views untuk query complex (sisa anggaran)
- Triggers untuk auto-generate UUID
- Stored procedures untuk reporting

**Tabel Utama:**
- users, bidang, programs, kegiatan, sub_kegiatan
- sppd, sppd_pegawai, lppd, kwitansi
- security_logs, notifications, system_settings

---

### Web Server
```
Apache 2.4+ / Nginx 1.18+
```
**Digunakan Untuk:**
- Serve aplikasi PHP
- URL rewriting (pretty URLs)
- SSL/TLS termination (HTTPS)
- Static file serving (CSS, JS, images)
- Load balancing (production)
- Gzip compression

---

## 🎨 FRONTEND TECHNOLOGIES

### CSS Framework
```
Tailwind CSS 3.4.x
```
**Digunakan Untuk:**
- Utility-first styling (rapid development)
- Responsive design (mobile-first)
- Custom components (buttons, cards, forms)
- Dark mode support (optional)
- Consistent spacing & typography

**Implementasi di Project:**
- Layout: Sidebar, Topbar, Cards
- Forms: Input, Select, Textarea, File upload
- Tables: DataTables wrapper
- Modals: SweetAlert2 integration
- Buttons: Primary, Secondary, Danger, Success
- Badges: Status indicators
- Loading states: Spinners, skeletons

---

### JavaScript Libraries

#### 1. jQuery 3.7.x
```html
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
```
**Digunakan Untuk:**
- DOM manipulation
- AJAX requests untuk API calls
- Event handling (click, change, submit)
- Form serialization
- Element selection & traversal

**Implementasi di Project:**
- Submit form via AJAX
- Load data dynamically
- Show/hide elements
- Trigger modals
- File upload preview

---

#### 2. SweetAlert2 11.x
```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```
**Digunakan Untuk:**
- Alert messages (success, error, warning)
- Confirmation dialogs (delete, approve, reject)
- Toast notifications (auto-dismiss)
- Loading indicators
- Custom input forms in modal

**Implementasi di Project:**
- Konfirmasi delete user/bidang/program
- Konfirmasi approve/reject SPPD
- Success message setelah save data
- Error message saat validation gagal
- Toast untuk real-time notifications
- Loading saat submit form

**Contoh Penggunaan:**
```javascript
// Konfirmasi delete
Swal.fire({
  title: 'Yakin hapus data?',
  text: "Data tidak dapat dikembalikan!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Ya, hapus!',
  cancelButtonText: 'Batal'
}).then((result) => {
  if (result.isConfirmed) {
    // Delete action
  }
});
```

---

#### 3. DataTables 1.13.x
```html
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
```
**Digunakan Untuk:**
- Display data dalam tabel interaktif
- Server-side processing untuk data besar
- Sorting, filtering, pagination
- Export data (Excel, CSV, PDF, Print)
- Responsive table design
- Custom column rendering

**Implementasi di Project:**
- Tabel User (Super Admin)
- Tabel Bidang (Super Admin)
- Tabel Program/Kegiatan/Sub Kegiatan (Kepala Bidang)
- Tabel SPPD (semua role)
- Tabel Logs (Super Admin)
- Tabel Verifikasi (Keuangan)

**Extensions yang Dipakai:**
- Buttons: Export functionality
- Responsive: Mobile-friendly tables
- FixedHeader: Sticky header saat scroll

---

#### 4. Select2 4.1.x
```html
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```
**Digunakan Untuk:**
- Enhanced select dropdown dengan search
- Multi-select pegawai untuk SPPD
- AJAX loading untuk data besar
- Custom templates (dengan foto, NIP)
- Tag mode untuk multiple selection

**Implementasi di Project:**
- Pilih Bidang (form user)
- Pilih Program/Kegiatan/Sub Kegiatan (form SPPD)
- Multi-select Pegawai (form SPPD)
- Filter dropdown (semua tabel)
- Autocomplete NIP/Nama Pegawai

---

#### 5. Chart.js 4.4.x
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
```
**Digunakan Untuk:**
- Data visualization (charts & graphs)
- Line chart: Trend SPPD per bulan
- Bar chart: SPPD per Bidang
- Pie chart: SPPD per Tipe Perjalanan
- Donut chart: Status SPPD distribution
- Doughnut chart: Budget vs Realisasi

**Implementasi di Project:**
- Dashboard Kepala Dinas: Analytics semua SPPD
- Dashboard Kepala Bidang: Analytics SPPD bidang
- Dashboard Keuangan: Trend pencairan
- Laporan: Visualisasi data

---

#### 6. jQuery Validation 1.19.x
```html
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
```
**Digunakan Untuk:**
- Client-side form validation
- Real-time validation feedback
- Remote validation (check duplicate)
- Custom validation rules
- Error message customization

**Implementasi di Project:**
- Form User (NIP unique, email valid)
- Form Program (anggaran minimal)
- Form SPPD (tanggal valid, pegawai tidak overlap)
- Form LPPD (minimal karakter)
- Form Kwitansi (total tidak melebihi anggaran)

---

#### 7. Moment.js 2.30.x
```html
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/locale/id.js"></script>
```
**Digunakan Untuk:**
- Date/time manipulation & formatting
- Calculate date difference (lama perjalanan)
- Date validation (tanggal kembali > berangkat)
- Relative time ("2 hari lagi", "3 jam yang lalu")
- Timezone conversion

**Implementasi di Project:**
- Format tanggal Indonesia (Senin, 01 Januari 2024)
- Calculate lama perjalanan otomatis
- Validasi tanggal berangkat minimal H+1
- Display countdown untuk SPPD mendatang
- Format timestamp di logs

---

#### 8. Flatpickr 4.6.x
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
```
**Digunakan Untuk:**
- Date picker (calendar input)
- Date range picker (filter laporan)
- Min/max date restriction
- Disable specific dates
- Mobile-friendly date selection

**Implementasi di Project:**
- Input tanggal berangkat (min: tomorrow)
- Input tanggal kembali (auto-calculate)
- Filter periode laporan (date range)
- Input tanggal pengisian LPPD
- Filter analytics per tanggal

---

#### 9. Dropzone.js 6.0.x
```html
<link rel="stylesheet" href="https://unpkg.com/dropzone@6/dist/dropzone.css" />
<script src="https://unpkg.com/dropzone@6"></script>
```
**Digunakan Untuk:**
- Drag & drop file upload
- Multiple file upload
- Image preview before upload
- File validation (type, size)
- Upload progress indicator
- Remove uploaded files

**Implementasi di Project:**
- Upload Surat Tugas (SPPD)
- Upload Bukti Perjalanan (Kwitansi)
- Upload Bukti Penginapan (Kwitansi)
- Upload Bukti Taxi (Kwitansi)
- Upload Bukti Tiket (Kwitansi)
- Upload Dokumentasi Foto (LPPD)

---

## 📄 PDF GENERATION

### mPDF 8.x
```bash
composer require mpdf/mpdf
```
**Digunakan Untuk:**
- Generate PDF documents
- SPPD form (setelah approve)
- Nota Dinas (auto-generate)
- Laporan LPPD
- Laporan Kwitansi
- Laporan Keuangan (PDF export)

**Implementasi di Project:**
- Template SPPD dengan header instansi
- Template Nota Dinas dengan format resmi
- Watermark pada PDF draft
- Digital signature placeholder
- Page numbering & footer

---

## 📊 EXCEL GENERATION

### PhpSpreadsheet 1.29.x
```bash
composer require phpoffice/phpspreadsheet
```
**Digunakan Untuk:**
- Generate Excel files (.xlsx)
- Export data tabel ke Excel
- Format cells (currency, date)
- Formula support
- Multiple sheets
- Styling (colors, borders)

**Implementasi di Project:**
- Export data User (Super Admin)
- Export data Program/Kegiatan/SPPD (Kepala Bidang)
- Export Laporan Keuangan (Keuangan)
- Template import data (bulk insert)

---

## 📧 EMAIL & NOTIFICATIONS

### PHPMailer 6.9.x
```bash
composer require phpmailer/phpmailer
```
**Digunakan Untuk:**
- Send email notifications
- SMTP configuration
- HTML email templates
- Attachments (PDF SPPD, Nota Dinas)
- Queue email (delayed send)

**Implementasi di Project:**
- Notifikasi approval Program/Kegiatan/Sub Kegiatan
- Notifikasi approval/rejection SPPD
- Notifikasi submit LPPD & Kwitansi
- Notifikasi verifikasi Keuangan
- Notifikasi return untuk revisi
- Password reset email

---

## 🔐 AUTHENTICATION & SECURITY

### PHP Password Hashing
```php
password_hash($password, PASSWORD_BCRYPT);
password_verify($inputPassword, $hashedPassword);
```
**Digunakan Untuk:**
- Hash password user
- Verify password saat login
- Password strength validation

### CSRF Protection (Built-in CI4)
**Digunakan Untuk:**
- Protect form dari CSRF attack
- Auto-generate CSRF token
- Validate token pada submit

### Session Management (CI4)
**Digunakan Untuk:**
- Store user data after login
- Track user activity
- Session timeout (60 menit)
- Remember me functionality

---

## 🎨 ICONS & FONTS

### Font Awesome 6.5.x
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
```
**Digunakan Untuk:**
- Icons untuk menu sidebar
- Icons untuk buttons (edit, delete, view)
- Icons untuk status badges
- Icons untuk notifications
- Icons untuk file types

**Implementasi di Project:**
- fa-user: User management
- fa-briefcase: Bidang
- fa-folder: Program
- fa-plane: SPPD
- fa-file-pdf: PDF download
- fa-check: Approve
- fa-times: Reject
- fa-bell: Notifications

---

### Google Fonts (Inter / Poppins)
```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```
**Digunakan Untuk:**
- Typography consistency
- Modern font style
- Multiple weights untuk heading & body

---

## 🛠️ ADDITIONAL LIBRARIES (UNTUK EFISIENSI CODING)

### 1. Lodash 4.17.x
```html
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
```
**Digunakan Untuk:**
- Array/Object manipulation
- Deep clone objects
- Debounce/Throttle functions
- Template rendering
- Data grouping & filtering

**Implementasi di Project:**
```javascript
// Group SPPD by bidang
_.groupBy(sppdData, 'bidang_id');

// Debounce search input
_.debounce(searchFunction, 300);

// Deep clone form data
_.cloneDeep(formData);

// Filter unique values
_.uniq(pegawaiIds);
```

---

### 2. Axios 1.6.x (Alternative AJAX)
```html
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
```
**Digunakan Untuk:**
- HTTP client (alternative jQuery.ajax)
- Promise-based requests
- Interceptors (auto add CSRF token)
- Upload progress tracking
- Request/Response transformation

**Implementasi di Project:**
```javascript
// Interceptor untuk auto add CSRF token
axios.interceptors.request.use(config => {
  config.headers['X-CSRF-TOKEN'] = csrfToken;
  return config;
});

// Upload with progress
axios.post('/upload', formData, {
  onUploadProgress: progressEvent => {
    let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
    updateProgressBar(percentCompleted);
  }
});
```

---

### 3. Day.js 1.11.x (Alternative Moment.js - Lebih Ringan)
```html
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.10/dayjs.min.js"></script>
```
**Digunakan Untuk:**
- Date manipulation (2KB vs 67KB Moment.js)
- Format tanggal
- Date calculation
- Plugin support (relativeTime, customParseFormat)

**Implementasi di Project:**
```javascript
// Format tanggal
dayjs(tanggal).format('DD MMMM YYYY');

// Calculate difference
dayjs(tanggal_kembali).diff(tanggal_berangkat, 'day');

// Relative time
dayjs(tanggal).fromNow(); // "2 hari lagi"
```

---

### 4. Cleave.js 1.6.x
```html
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
```
**Digunakan Untuk:**
- Input formatting (currency, date, credit card)
- Auto-format rupiah
- Auto-format NIP/NIK (16-18 digit)
- Phone number formatting

**Implementasi di Project:**
```javascript
// Format input rupiah
new Cleave('#anggaran', {
  numeral: true,
  numeralThousandsGroupStyle: 'thousand',
  prefix: 'Rp ',
  rawValueTrimPrefix: true
});

// Format NIP
new Cleave('#nip', {
  blocks: [8, 6, 1, 3],
  delimiter: ' ',
  numericOnly: true
});
```

---

### 5. Parsley.js 2.9.x (Alternative jQuery Validation)
```html
<script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>
```
**Digunakan Untuk:**
- Declarative form validation (HTML attributes)
- Custom validators
- Multiple error messages
- Field dependency validation

**Implementasi di Project:**
```html
<input type="text" 
  data-parsley-required="true"
  data-parsley-minlength="16"
  data-parsley-maxlength="18"
  data-parsley-type="digits"
  data-parsley-error-message="NIP harus 16-18 digit angka">
```

---

### 6. SortableJS 1.15.x
```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
```
**Digunakan Untuk:**
- Drag & drop untuk reorder items
- Sortable lists
- Drag between multiple lists

**Implementasi di Project:**
- Reorder pegawai dalam SPPD (priority)
- Reorder menu items (admin)
- Drag foto dokumentasi untuk urutan

---

### 7. Toastify JS 1.12.x
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
```
**Digunakan Untuk:**
- Lightweight toast notifications (alternative SweetAlert toast)
- Auto-dismiss notifications
- Custom positioning
- Animation support

**Implementasi di Project:**
```javascript
Toastify({
  text: "SPPD berhasil dibuat!",
  duration: 3000,
  gravity: "top",
  position: "right",
  backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
}).showToast();
```

---

### 8. Typed.js 2.1.x
```html
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.1.0"></script>
```
**Digunakan Untuk:**
- Typing animation untuk welcome message
- Dynamic text display
- Auto-delete & loop

**Implementasi di Project:**
```javascript
// Dashboard welcome message
new Typed('#typed', {
  strings: ['Selamat datang di Aplikasi Perjadin', 'Kelola perjalanan dinas dengan mudah'],
  typeSpeed: 50,
  backSpeed: 30,
  loop: true
});
```

---

### 9. AOS (Animate On Scroll) 2.3.x
```html
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
```
**Digunakan Untuk:**
- Scroll animations
- Element fade-in on scroll
- Smooth page transitions

**Implementasi di Project:**
```html
<div data-aos="fade-up" data-aos-duration="1000">
  <div class="card">...</div>
</div>
```

---

### 10. Intro.js 7.2.x
```html
<link href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>
```
**Digunakan Untuk:**
- User onboarding tours
- Feature introduction
- Step-by-step guide
- Highlight elements

**Implementasi di Project:**
```javascript
// Tutorial untuk first-time user
introJs().setOptions({
  steps: [
    {
      element: '#sidebar',
      intro: 'Ini adalah menu navigasi utama'
    },
    {
      element: '#btn-create-sppd',
      intro: 'Klik di sini untuk membuat SPPD baru'
    }
  ]
}).start();
```

---

## 🔧 BACKEND HELPER LIBRARIES

### 1. Intervention Image 2.7.x
```bash
composer require intervention/image
```
**Digunakan Untuk:**
- Image manipulation (resize, crop, compress)
- Thumbnail generation
- Watermark pada foto
- Format conversion (PNG to JPG)

**Implementasi di Project:**
- Compress foto dokumentasi (max 5MB)
- Generate thumbnail foto profile
- Resize uploaded images (max 800x600)
- Add watermark pada bukti perjalanan

---

### 2. Carbon 2.x (Included in CI4)
```php
use CodeIgniter\I18n\Time;
```
**Digunakan Untuk:**
- Date/time operations
- Format tanggal Indonesia
- Calculate date difference
- Timezone handling

---

### 3. GuzzleHTTP 7.x (Optional)
```bash
composer require guzzlehttp/guzzle
```
**Digunakan Untuk:**
- HTTP client untuk external API
- Webhook notifications
- Third-party integrations
- API testing

**Implementasi di Project:**
- Integrasi WhatsApp notification (optional)
- Webhook ke sistem lain (optional)
- API to Bank system (optional)

---

### 4. Dotenv (Included in CI4)
**Digunakan Untuk:**
- Environment configuration
- Secure credential storage
- Different config per environment (dev, staging, prod)

---

### 5. Faker (Development Only)
```bash
composer require --dev fakerphp/faker
```
**Digunakan Untuk:**
- Generate dummy data untuk testing
- Seed database dengan realistic data
- Development & demo purposes

---

## 📱 RESPONSIVE DESIGN

### Tailwind CSS Responsive Breakpoints
```css
sm: 640px   /* Small devices (landscape phones) */
md: 768px   /* Medium devices (tablets) */
lg: 1024px  /* Large devices (desktops) */
xl: 1280px  /* Extra large devices */
2xl: 1536px /* 2X Extra large devices */
```

**Implementasi di Project:**
- Mobile: Stack layout, hamburger menu
- Tablet: 2-column grid, semi-collapsed sidebar
- Desktop: 3-4 column grid, full sidebar

---

## 🚀 PERFORMANCE OPTIMIZATION

### 1. CSS Minification
```bash
npx tailwindcss -i ./src/input.css -o ./public/assets/css/tailwind.css --minify
```

### 2. JS Minification
```bash
npm install -g uglify-js
uglifyjs public/assets/js/app.js -o public/assets/js/app.min.js
```

### 3. Image Optimization
- WebP format untuk images
- Lazy loading: `loading="lazy"`
- Compress dengan TinyPNG/ImageOptim

### 4. Database Query Optimization
- Indexing pada foreign keys
- Query caching (Redis optional)
- Pagination untuk large datasets

### 5. Caching Strategy
```php
$cache = \Config\Services::cache();
$cache->save('key', $data, 3600); // Cache 1 hour
```

---

## 🧪 TESTING

### PHPUnit (Unit Testing)
```bash
composer require --dev phpunit/phpunit
```
**Digunakan Untuk:**
- Unit testing untuk Models & Libraries
- Integration testing untuk Controllers
- Test coverage reporting

---

## 📦 DEPLOYMENT REQUIREMENTS

### Production Server Checklist
- ✅ PHP 8.1+
- ✅ MySQL 8.0+ / MariaDB 10.6+
- ✅ Apache/Nginx dengan mod_rewrite
- ✅ SSL Certificate (HTTPS)
- ✅ Composer installed
- ✅ Node.js & NPM
- ✅ PHP Extensions:
  - intl, mbstring, mysqli, pdo_mysql
  - gd/imagick, curl, zip, xml

---

## 🔗 CDN RESOURCES SUMMARY

```html
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@6/dist/dropzone.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.10/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://unpkg.com/dropzone@6"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
```

---

## ⚡ LIBRARY
1. ✅ CodeIgniter 4
2. ✅ Tailwind CSS
3. ✅ jQuery
4. ✅ SweetAlert2
5. ✅ DataTables
6. ✅ Select2
7. ✅ jQuery Validation
8. ✅ Flatpickr
9. ✅ mPDF
10. ✅ PHPMailer
11. ✅ Chart.js (Analytics)
12. ✅ Dropzone.js (File upload)
13. ✅ PhpSpreadsheet (Excel export)
14. ✅ Lodash (Helper functions)
15. ✅ Cleave.js (Input formatting)
16. ✅ Axios (Better AJAX)
17. ✅ Day.js (Lighter date library)
18. ✅ Toastify (Lightweight toast)
19. ✅ AOS (Scroll animations)
20. ✅ Intro.js (User onboarding)

---

## 📚 KESIMPULAN LIBRARY SELECTION

**Kenapa Library-Library Ini Dipilih:**

1. **Produktivitas**: Tailwind, jQuery, SweetAlert2 → Rapid development
2. **User Experience**: DataTables, Select2, Dropzone → Interactive & intuitive
3. **Data Visualization**: Chart.js → Clear insights untuk decision makers
4. **Validation**: jQuery Validation, Parsley → Reduce server load, instant feedback
5. **File Handling**: mPDF, PhpSpreadsheet → Professional document generation
6. **Performance**: Day.js, Lodash → Optimize client-side operations
7. **Maintainability**: Cleave.js, Axios → Clean & readable code
8. **Security**: Built-in CI4 features → CSRF, XSS protection
9. **Scalability**: Server-side DataTables, Caching → Handle large data
10. **Cost**: All FREE & open-source libraries

**Total Library Size (Minified):**
- Critical (MVP): ~500 KB
- Enhancement: ~300 KB
- Optional: ~200 KB
- **Total: ~1 MB** (acceptable untuk modern web app)

**Load Time Optimization:**
- Use CDN untuk library populer (browser cache)
- Lazy load library yang tidak critical (Chart.js, Intro.js)
- Minify custom JS & CSS
- Enable Gzip compression
- Use HTTP/2 untuk parallel loading