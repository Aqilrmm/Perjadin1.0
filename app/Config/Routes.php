<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Auth\AuthController::login');

// Authentication Routes
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::processLogin');
    $routes->get('logout', 'Auth\AuthController::logout');
    $routes->post('logout', 'Auth\AuthController::logout');
    $routes->get('check-session', 'Auth\AuthController::checkSession');
    // Profile (my account)
    $routes->get('profile', 'Auth\ProfileController::index');
    $routes->post('profile/update', 'Auth\ProfileController::update');
    $routes->post('profile/change-password', 'Auth\ProfileController::changePassword');
    $routes->post('profile/upload-photo', 'Auth\ProfileController::uploadPhoto');
    $routes->post('profile/delete-photo', 'Auth\ProfileController::deletePhoto');
    $routes->get('profile/activity', 'Auth\ProfileController::activityHistory');
});

// Super Admin Routes
$routes->group('superadmin', ['filter' => 'auth:superadmin'], function ($routes) {
    $routes->get('dashboard', 'SuperAdmin\DashboardController::index');
    $routes->get('dashboard/chart-data', 'SuperAdmin\DashboardController::getChartData');
    $routes->get('dashboard/recent-users', 'SuperAdmin\DashboardController::recentUsers');
    $routes->get('dashboard/recent-activities', 'SuperAdmin\DashboardController::recentActivities');
    $routes->get('dashboard/sppd-by-status', 'SuperAdmin\DashboardController::sppdByStatus');

    // User Management
    $routes->group('users', function ($routes) {
        $routes->get('/', 'SuperAdmin\UserController::index');
        $routes->post('datatable', 'SuperAdmin\UserController::datatable');
        $routes->get('get/(:num)', 'SuperAdmin\UserController::get/$1');
        $routes->post('create', 'SuperAdmin\UserController::create');
        $routes->post('update/(:num)', 'SuperAdmin\UserController::update/$1');
        $routes->delete('delete/(:num)', 'SuperAdmin\UserController::delete/$1');
        $routes->post('block/(:num)', 'SuperAdmin\UserController::block/$1');
        $routes->post('unblock/(:num)', 'SuperAdmin\UserController::unblock/$1');
    });

    // Bidang Management
    $routes->group('bidang', function ($routes) {
        $routes->get('/', 'SuperAdmin\BidangController::index');
        $routes->post('datatable', 'SuperAdmin\BidangController::datatable');
        $routes->get('get/(:num)', 'SuperAdmin\BidangController::get/$1');
        $routes->post('create', 'SuperAdmin\BidangController::create');
        $routes->post('update/(:num)', 'SuperAdmin\BidangController::update/$1');
        $routes->delete('delete/(:num)', 'SuperAdmin\BidangController::delete/$1');
    });

    // Blocked Users (Admin)
    $routes->group('blocked', function ($routes) {
        $routes->get('/', 'SuperAdmin\BlockController::index');
        $routes->post('datatable', 'SuperAdmin\BlockController::datatable');
        $routes->get('detail/(:num)', 'SuperAdmin\BlockController::detail/$1');
        $routes->post('block', 'SuperAdmin\BlockController::block');
        $routes->post('unblock/(:num)', 'SuperAdmin\BlockController::unblock/$1');
        $routes->post('bulk-unblock', 'SuperAdmin\BlockController::bulkUnblock');
        $routes->get('history/(:num)', 'SuperAdmin\BlockController::history/$1');
        $routes->get('statistics', 'SuperAdmin\BlockController::statistics');
    });

    // Logs
    $routes->group('logs', function ($routes) {
        $routes->get('/', 'SuperAdmin\LogController::index');
        $routes->post('datatable', 'SuperAdmin\LogController::datatable');
        $routes->get('detail/(:num)', 'SuperAdmin\LogController::detail/$1');
    });
});

// Kepala Dinas Routes
$routes->group('kepaladinas', ['filter' => 'auth:kepaladinas'], function ($routes) {
    $routes->get('dashboard', 'KepalaDinas\DashboardController::index');

    // Program Approval
    $routes->group('programs', function ($routes) {
        $routes->get('approval', 'KepalaDinas\ApprovalProgramController::index');
        $routes->post('datatable', 'KepalaDinas\ApprovalProgramController::datatable');
        $routes->get('detail/(:num)', 'KepalaDinas\ApprovalProgramController::detail/$1');
        $routes->post('approve/(:num)', 'KepalaDinas\ApprovalProgramController::approve/$1');
        $routes->post('reject/(:num)', 'KepalaDinas\ApprovalProgramController::reject/$1');
    });

    // Kegiatan Approval
    $routes->group('kegiatan', function ($routes) {
        $routes->get('approval', 'KepalaDinas\ApprovalKegiatanController::index');
        $routes->post('datatable', 'KepalaDinas\ApprovalKegiatanController::datatable');
        $routes->get('detail/(:num)', 'KepalaDinas\ApprovalKegiatanController::detail/$1');
        $routes->post('approve/(:num)', 'KepalaDinas\ApprovalKegiatanController::approve/$1');
        $routes->post('reject/(:num)', 'KepalaDinas\ApprovalKegiatanController::reject/$1');
    });

    // Sub Kegiatan Approval
    $routes->group('subkegiatan', function ($routes) {
        $routes->get('approval', 'KepalaDinas\ApprovalSubKegiatanController::index');
        $routes->post('datatable', 'KepalaDinas\ApprovalSubKegiatanController::datatable');
        $routes->get('detail/(:num)', 'KepalaDinas\ApprovalSubKegiatanController::detail/$1');
        $routes->post('approve/(:num)', 'KepalaDinas\ApprovalSubKegiatanController::approve/$1');
        $routes->post('reject/(:num)', 'KepalaDinas\ApprovalSubKegiatanController::reject/$1');
    });

    // SPPD Approval
    $routes->group('sppd', function ($routes) {
        $routes->get('approval', 'KepalaDinas\ApprovalSPPDController::index');
        $routes->post('datatable', 'KepalaDinas\ApprovalSPPDController::datatable');
        $routes->get('detail/(:num)', 'KepalaDinas\ApprovalSPPDController::detail/$1');
        $routes->get('preview/(:num)', 'KepalaDinas\ApprovalSPPDController::preview/$1');
        $routes->get('download-nota-dinas/(:num)', 'KepalaDinas\ApprovalSPPDController::downloadNotaDinas/$1');
        $routes->post('approve/(:num)', 'KepalaDinas\ApprovalSPPDController::approve/$1');
        $routes->post('reject/(:num)', 'KepalaDinas\ApprovalSPPDController::reject/$1');
    });

    // Analytics
    $routes->get('analytics', 'KepalaDinas\AnalyticsController::index');
});

// Kepala Bidang Routes
$routes->group('kepalabidang', ['filter' => 'auth:kepalabidang'], function ($routes) {
    $routes->get('dashboard', 'KepalaBidang\DashboardController::index');


    // Program Management
    $routes->group('programs', function ($routes) {
        $routes->get('/', 'KepalaBidang\ProgramController::index');
        $routes->post('datatable', 'KepalaBidang\ProgramController::datatable');
        $routes->get('get/(:num)', 'KepalaBidang\ProgramController::get/$1');
        $routes->post('create', 'KepalaBidang\ProgramController::create');
        $routes->post('update/(:num)', 'KepalaBidang\ProgramController::update/$1');
        $routes->post('submit/(:num)', 'KepalaBidang\ProgramController::submit/$1');
        $routes->delete('delete/(:num)', 'KepalaBidang\ProgramController::delete/$1');
    });

    // Kegiatan Management
    $routes->group('kegiatan', function ($routes) {
        $routes->get('/', 'KepalaBidang\KegiatanController::index');
        $routes->post('datatable', 'KepalaBidang\KegiatanController::datatable');
        $routes->get('get/(:num)', 'KepalaBidang\KegiatanController::get/$1');
        $routes->post('create', 'KepalaBidang\KegiatanController::create');
        $routes->post('update/(:num)', 'KepalaBidang\KegiatanController::update/$1');
        $routes->post('submit/(:num)', 'KepalaBidang\KegiatanController::submit/$1');
        $routes->delete('delete/(:num)', 'KepalaBidang\KegiatanController::delete/$1');
    });

    // Sub Kegiatan Management
    $routes->group('subkegiatan', function ($routes) {
        $routes->get('/', 'KepalaBidang\SubKegiatanController::index');
        $routes->post('datatable', 'KepalaBidang\SubKegiatanController::datatable');
        $routes->post('create', 'KepalaBidang\SubKegiatanController::create');
        $routes->post('update/(:num)', 'KepalaBidang\SubKegiatanController::update/$1');
        $routes->post('submit/(:num)', 'KepalaBidang\SubKegiatanController::submit/$1');
        $routes->delete('delete/(:num)', 'KepalaBidang\SubKegiatanController::delete/$1');
    });

    // SPPD Management
    $routes->group('sppd', function ($routes) {
        $routes->get('step/(:num)', 'KepalaBidang\SPPDController::loadStep/$1');
        $routes->post('step/(:num)/validate', 'KepalaBidang\SPPDController::validateStepDynamic/$1');

        $routes->get('/', 'KepalaBidang\SPPDController::index');
        $routes->get('create', 'KepalaBidang\SPPDController::create');
        $routes->post('datatable', 'KepalaBidang\SPPDController::datatable');
        $routes->get('detail/(:num)', 'KepalaBidang\SPPDController::detail/$1');
        $routes->post('validate-step1', 'KepalaBidang\SPPDController::validateStep1');
        $routes->post('validate-step2', 'KepalaBidang\SPPDController::validateStep2');
        $routes->post('validate-step3', 'KepalaBidang\SPPDController::validateStep3');
        $routes->post('validate-step4', 'KepalaBidang\SPPDController::validateStep4');
        $routes->post('submit', 'KepalaBidang\SPPDController::submit');
    });

    // Analytics
    $routes->get('analytics', 'KepalaBidang\AnalyticsController::index');
});

// Pegawai Routes
$routes->group('pegawai', ['filter' => 'auth:pegawai'], function ($routes) {
    $routes->get('dashboard', 'Pegawai\DashboardController::index');

    // My SPPD
    $routes->group('sppd', function ($routes) {
        $routes->get('/', 'Pegawai\MySPPDController::index');
        $routes->get('detail/(:num)', 'Pegawai\MySPPDController::detail/$1');
    });

    // LPPD
    $routes->group('lppd', function ($routes) {
        $routes->get('form/(:num)', 'Pegawai\LPPDController::form/$1');
        $routes->post('save/(:num)', 'Pegawai\LPPDController::save/$1');
        $routes->post('submit/(:num)', 'Pegawai\LPPDController::submit/$1');
    });

    // Kwitansi
    $routes->group('kwitansi', function ($routes) {
        $routes->get('form/(:num)', 'Pegawai\KwitansiController::form/$1');
        $routes->post('save/(:num)', 'Pegawai\KwitansiController::save/$1');
        $routes->post('submit/(:num)', 'Pegawai\KwitansiController::submit/$1');
    });
});

// Keuangan Routes
$routes->group('keuangan', ['filter' => 'auth:keuangan'], function ($routes) {
    $routes->get('dashboard', 'Keuangan\DashboardController::index');

    // Verifikasi
    $routes->group('verifikasi', function ($routes) {
        $routes->get('/', 'Keuangan\VerifikasiController::index');
        $routes->post('datatable', 'Keuangan\VerifikasiController::datatable');
        $routes->get('detail/(:num)', 'Keuangan\VerifikasiController::detail/$1');
        $routes->post('approve/(:num)', 'Keuangan\VerifikasiController::approve/$1');
        $routes->post('reject/(:num)', 'Keuangan\VerifikasiController::reject/$1');
    });

    // Laporan
    $routes->group('laporan', function ($routes) {
        $routes->get('/', 'Keuangan\LaporanController::index');
        $routes->post('generate', 'Keuangan\LaporanController::generate');
    });
});

// API Routes
$routes->group('api', function ($routes) {
    // Public API
    $routes->get('bidang/options', 'API\BidangController::options');
    $routes->get('pegawai/search', 'API\PegawaiController::search');
    $routes->get('pegawai/list-all', 'API\PegawaiController::listAll');
    $routes->get('pegawai/get/(:num)', 'API\PegawaiController::get/$1');
    $routes->post('pegawai/get-multiple', 'API\PegawaiController::getMultiple');
    $routes->get('pegawai/count-by-bidang', 'API\PegawaiController::countByBidang');
    $routes->post('pegawai/check-availability', 'API\PegawaiController::checkAvailability');

    // File Upload
    $routes->post('upload/file', 'API\FileUploadController::upload');
    $routes->delete('upload/file', 'API\FileUploadController::delete');

    // Notifications
    $routes->get('notifications', 'API\NotificationController::index');
    $routes->post('notifications/read/(:num)', 'API\NotificationController::markAsRead/$1');
    $routes->post('notifications/read-all', 'API\NotificationController::markAllAsRead');

    // Validation
    $routes->post('validate/nip-nik', 'API\ValidationController::checkNipNik');
    $routes->post('validate/email', 'API\ValidationController::checkEmail');
    $routes->post('validate/kode-program', 'API\ValidationController::checkKodeProgram');

    // DataTable helper endpoints
    $routes->group('datatable', function ($routes) {
        $routes->get('config', 'API\DataTableController::getConfig');
        $routes->get('language', 'API\DataTableController::getLanguage');
        $routes->get('options', 'API\DataTableController::getCommonOptions');
        $routes->post('export', 'API\DataTableController::export');
        $routes->get('filter-options', 'API\DataTableController::getFilterOptions');
        $routes->post('bulk-delete', 'API\DataTableController::bulkDelete');
    });

    // Options for Select2 (programs/kegiatan/subkegiatan)
    $routes->get('programs/options', 'API\OptionsController::program');
    $routes->get('kegiatan/options', 'API\OptionsController::kegiatan');
    $routes->get('subkegiatan/options', 'API\OptionsController::subkegiatan');
});

// Dev / Debug Routes (no auth) - quick helpers for testing layouts and assets
$routes->get('dev/layout-test', 'Dev\DevController::layoutTest');
