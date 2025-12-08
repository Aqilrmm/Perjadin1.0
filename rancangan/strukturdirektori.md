project-perjadin/
│
├── app/
│   ├── Config/
│   │   ├── Routes.php                 # Routing configuration
│   │   ├── Filters.php                # Filter configuration (Auth, RBAC)
│   │   ├── Database.php               # Database configuration
│   │   └── Constants.php              # App constants (roles, status)
│   │
│   ├── Controllers/
│   │   ├── BaseController.php         # Base controller dengan common methods
│   │   │
│   │   ├── Auth/
│   │   │   ├── AuthController.php     # Login, Logout, Session
│   │   │   └── ProfileController.php  # User profile management
│   │   │
│   │   ├── SuperAdmin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php     # CRUD User
│   │   │   ├── BidangController.php   # CRUD Bidang
│   │   │   ├── LogController.php      # Security logs
│   │   │   └── BlockController.php    # Block/Unblock user
│   │   │
│   │   ├── KepalaDinas/
│   │   │   ├── DashboardController.php
│   │   │   ├── ApprovalProgramController.php
│   │   │   ├── ApprovalKegiatanController.php
│   │   │   ├── ApprovalSubKegiatanController.php
│   │   │   ├── ApprovalSPPDController.php
│   │   │   └── AnalyticsController.php
│   │   │
│   │   ├── KepalaBidang/
│   │   │   ├── DashboardController.php
│   │   │   ├── ProgramController.php
│   │   │   ├── KegiatanController.php
│   │   │   ├── SubKegiatanController.php
│   │   │   ├── SPPDController.php
│   │   │   └── AnalyticsController.php
│   │   │
│   │   ├── Pegawai/
│   │   │   ├── DashboardController.php
│   │   │   ├── MySPPDController.php
│   │   │   ├── LPPDController.php
│   │   │   └── KwitansiController.php
│   │   │
│   │   ├── Keuangan/
│   │   │   ├── DashboardController.php
│   │   │   ├── VerifikasiController.php
│   │   │   └── LaporanController.php
│   │   │
│   │   └── API/                       # API endpoints (untuk AJAX)
│   │       ├── NotificationController.php
│   │       ├── DataTableController.php
│   │       └── FileUploadController.php
│   │
│   ├── Models/
│   │   ├── BaseModel.php              # Extended CI Model dengan common methods
│   │   │
│   │   ├── User/
│   │   │   ├── UserModel.php          # User CRUD
│   │   │   └── UserAuthModel.php      # Authentication logic
│   │   │
│   │   ├── Bidang/
│   │   │   └── BidangModel.php
│   │   │
│   │   ├── Program/
│   │   │   ├── ProgramModel.php
│   │   │   ├── KegiatanModel.php
│   │   │   └── SubKegiatanModel.php
│   │   │
│   │   ├── SPPD/
│   │   │   ├── SPPDModel.php
│   │   │   ├── SPPDPegawaiModel.php   # Relasi SPPD - Pegawai (many-to-many)
│   │   │   ├── LPPDModel.php
│   │   │   └── KwitansiModel.php
│   │   │
│   │   ├── Log/
│   │   │   └── SecurityLogModel.php
│   │   │
│   │   └── Notification/
│   │       └── NotificationModel.php
│   │
│   ├── Libraries/                      # Custom libraries (OOP)
│   │   ├── Auth/
│   │   │   ├── AuthService.php        # Authentication service
│   │   │   └── RBACService.php        # Role-based access control
│   │   │
│   │   ├── SPPD/
│   │   │   ├── SPPDGenerator.php      # Generate nomor SPPD otomatis
│   │   │   ├── NotaDinasGenerator.php # Generate Nota Dinas PDF
│   │   │   └── SPPDValidator.php      # Business rules validation
│   │   │
│   │   ├── Upload/
│   │   │   └── FileUploadService.php  # Handle file upload & validation
│   │   │
│   │   ├── Notification/
│   │   │   ├── NotificationService.php # Send notifications
│   │   │   └── EmailService.php       # Email notifications
│   │   │
│   │   ├── PDF/
│   │   │   ├── PDFGenerator.php       # mPDF wrapper
│   │   │   └── SPPDPDFTemplate.php    # SPPD PDF template
│   │   │
│   │   └── Logger/
│   │       └── ActivityLogger.php     # Log user activities
│   │
│   ├── Helpers/
│   │   ├── auth_helper.php            # Auth helper functions
│   │   ├── format_helper.php          # Format currency, date, etc
│   │   ├── status_helper.php          # Status badge generator
│   │   └── notification_helper.php    # Notification helpers
│   │
│   ├── Filters/
│   │   ├── AuthFilter.php             # Check if user is logged in
│   │   └── RoleFilter.php             # Check user role for access
│   │
│   ├── Validation/
│   │   ├── UserRules.php              # Custom validation rules for User
│   │   ├── SPPDRules.php              # Custom validation rules for SPPD
│   │   └── ProgramRules.php           # Custom validation rules for Program
│   │
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── main.php               # Main layout (sidebar + topbar)
│   │   │   ├── auth.php               # Auth layout (login page)
│   │   │   ├── components/
│   │   │   │   ├── sidebar.php
│   │   │   │   ├── topbar.php
│   │   │   │   ├── footer.php
│   │   │   │   └── modals/
│   │   │   │       ├── confirm_modal.php
│   │   │   │       └── loading_modal.php
│   │   │
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   ├── forgot_password.php
│   │   │   └── profile.php
│   │   │
│   │   ├── superadmin/
│   │   │   ├── dashboard.php
│   │   │   ├── users/
│   │   │   │   ├── index.php
│   │   │   │   └── modals/
│   │   │   │       └── form_user.php
│   │   │   ├── bidang/
│   │   │   │   ├── index.php
│   │   │   │   └── modals/
│   │   │   │       └── form_bidang.php
│   │   │   ├── logs/
│   │   │   │   └── index.php
│   │   │   └── blocked/
│   │   │       └── index.php
│   │   │
│   │   ├── kepaladinas/
│   │   │   ├── dashboard.php
│   │   │   ├── approval/
│   │   │   │   ├── program.php
│   │   │   │   ├── kegiatan.php
│   │   │   │   ├── subkegiatan.php
│   │   │   │   └── sppd.php
│   │   │   ├── analytics/
│   │   │   │   └── index.php
│   │   │   └── modals/
│   │   │       ├── detail_program.php
│   │   │       ├── reject_form.php
│   │   │       └── preview_sppd.php
│   │   │
│   │   ├── kepalabidang/
│   │   │   ├── dashboard.php
│   │   │   ├── program/
│   │   │   │   ├── index.php
│   │   │   │   └── modals/
│   │   │   │       └── form_program.php
│   │   │   ├── kegiatan/
│   │   │   │   ├── index.php
│   │   │   │   └── modals/
│   │   │   │       └── form_kegiatan.php
│   │   │   ├── subkegiatan/
│   │   │   │   ├── index.php
│   │   │   │   └── modals/
│   │   │   │       └── form_subkegiatan.php
│   │   │   ├── sppd/
│   │   │   │   ├── index.php
│   │   │   │   ├── create.php         # Wizard form
│   │   │   │   ├── detail.php
│   │   │   │   └── steps/             # Step components
│   │   │   │       ├── step1_program.php
│   │   │   │       ├── step2_detail.php
│   │   │   │       ├── step3_pegawai.php
│   │   │   │       ├── step4_biaya.php
│   │   │   │       └── step5_review.php
│   │   │   └── analytics/
│   │   │       └── index.php
│   │   │
│   │   ├── pegawai/
│   │   │   ├── dashboard.php
│   │   │   ├── sppd/
│   │   │   │   ├── index.php          # List SPPD (card view)
│   │   │   │   ├── detail.php
│   │   │   │   ├── lppd_form.php
│   │   │   │   └── kwitansi_form.php
│   │   │   └── components/
│   │   │       ├── sppd_card.php
│   │   │       └── upload_bukti.php
│   │   │
│   │   ├── keuangan/
│   │   │   ├── dashboard.php
│   │   │   ├── verifikasi/
│   │   │   │   ├── index.php
│   │   │   │   └── detail.php         # Split screen verification
│   │   │   └── laporan/
│   │   │       └── index.php
│   │   │
│   │   ├── errors/
│   │   │   ├── 404.php
│   │   │   ├── 403.php
│   │   │   └── 500.php
│   │   │
│   │   └── emails/                     # Email templates
│   │       ├── approval_notification.php
│   │       ├── rejection_notification.php
│   │       └── verification_result.php
│   │
│   └── Database/
│       └── Migrations/
│           ├── 2024-01-01_create_users_table.php
│           ├── 2024-01-02_create_bidang_table.php
│           ├── 2024-01-03_create_programs_table.php
│           ├── 2024-01-04_create_kegiatan_table.php
│           ├── 2024-01-05_create_sub_kegiatan_table.php
│           ├── 2024-01-06_create_sppd_table.php
│           ├── 2024-01-07_create_sppd_pegawai_table.php
│           ├── 2024-01-08_create_lppd_table.php
│           ├── 2024-01-09_create_kwitansi_table.php
│           ├── 2024-01-10_create_security_logs_table.php
│           └── 2024-01-11_create_notifications_table.php
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   ├── app.css                # Custom CSS
│   │   │   └── tailwind.css           # Compiled Tailwind CSS
│   │   │
│   │   ├── js/
│   │   │   ├── app.js                 # Main JS
│   │   │   ├── auth.js                # Auth related JS
│   │   │   ├── datatables-init.js     # DataTables initialization
│   │   │   ├── sppd-wizard.js         # SPPD wizard form
│   │   │   ├── file-upload.js         # File upload handler
│   │   │   └── notification.js        # Real-time notifications
│   │   │
│   │   ├── images/
│   │   │   ├── logo.png
│   │   │   └── default-avatar.png
│   │   │
│   │   └── vendors/                    # Third-party libraries
│   │       ├── jquery/
│   │       ├── bootstrap/              # Bootstrap 5
│   │       ├── datatables/
│   │       ├── sweetalert2/
│   │       ├── select2/
│   │       ├── chartjs/
│   │       └── font-awesome/
│   │
│   └── uploads/                        # User uploads (gitignored)
│       ├── surat_tugas/
│       ├── bukti_perjalanan/
│       ├── bukti_penginapan/
│       ├── bukti_taxi/
│       ├── bukti_tiket/
│       ├── dokumentasi_kegiatan/
│       └── foto_profile/
│
├── writable/
│   ├── cache/
│   ├── logs/
│   ├── session/
│   └── uploads/                        # Temporary uploads
│
├── tests/                              # Unit & Integration tests
│   ├── Controllers/
│   ├── Models/
│   └── Libraries/
│
├── .env                                # Environment configuration
├── .env.example
├── composer.json
├── phpunit.xml
└── README.md


==================================================
KETERANGAN STRUKTUR MODULAR & OOP:
==================================================

1. CONTROLLERS (Thin Controllers)
   - Controllers hanya handle HTTP request/response
   - Business logic di-delegate ke Libraries/Services
   - Menggunakan BaseController untuk common methods
   - Separated by role untuk maintainability

2. MODELS (Fat Models)
   - Semua database operations di Models
   - Menggunakan BaseModel untuk CRUD operations
   - Relational methods (hasMany, belongsTo, etc)
   - Query scopes untuk reusable queries

3. LIBRARIES (Business Logic Layer)
   - Service classes untuk complex operations
   - Single Responsibility Principle
   - Dependency Injection ready
   - Testable & reusable

4. HELPERS
   - Stateless functions
   - View helpers (format, badges, etc)
   - Autoloaded jika diperlukan

5. VIEWS (Component-based)
   - Layouts untuk structure
   - Components untuk reusable UI
   - Modals separated
   - Email templates

6. VALIDATION
   - Custom validation rules separated
   - Reusable across controllers
   - Clear error messages

7. FILTERS
   - Authentication check
   - Authorization (RBAC)
   - Request throttling (optional)

8. API
   - Separate controllers untuk AJAX requests
   - Return JSON format
   - RESTful conventions

==================================================
BEST PRACTICES APPLIED:
==================================================

✓ Separation of Concerns (SoC)
✓ Single Responsibility Principle (SRP)
✓ Don't Repeat Yourself (DRY)
✓ Model-View-Controller (MVC)
✓ Service Layer Pattern
✓ Repository Pattern (via Models)
✓ Dependency Injection
✓ PSR-4 Autoloading
✓ RESTful API structure
✓ Modular architecture
✓ Testable code structure