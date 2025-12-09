<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Perjadin - Sistem Manajemen Perjalanan Dinas">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

    <title><?= esc($title ?? 'Dashboard') ?> - Aplikasi Perjadin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/favicon.ico') ?>">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Sidebar Transition */
        #sidebar {
            transition: transform 0.3s ease;
        }

        /* Loading Spinner */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            width: 24px;
            height: 24px;
            animation: spin 0.6s linear infinite;
        }

        /* Card Hover Effect */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Button Loading State */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        /* Dropdown Animation */
        .dropdown-menu {
            transform-origin: top right;
            animation: dropdownFade 0.2s ease-out;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Modal Backdrop */
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            height: 42px;
            padding: 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-gray-50 antialiased">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="h-8 w-8" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2232%22 height=%2232%22 viewBox=%220 0 24 24%22%3E%3Cpath fill=%22%233b82f6%22 d=%22M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5%22/%3E%3C/svg%3E'">
                <span class="text-xl font-bold text-gray-800">Perjadin</span>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <img src="<?= get_user_avatar() ?>" alt="Avatar" class="w-12 h-12 rounded-full object-cover" onerror="this.src='<?= base_url('assets/images/default-avatar.png') ?>'">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate"><?= esc(user_name() ?? 'Guest') ?></p>
                    <p class="text-xs text-gray-500"><?= esc(get_role_name(user_role() ?? '')) ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4">
            <?php
            $currentUrl = uri_string();
            $role = user_role();

            // Define menu items based on role
            $menuItems = [];

            if ($role === 'superadmin') {
                $menuItems = [
                    ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => 'superadmin/dashboard'],
                    ['icon' => 'fa-users', 'label' => 'Kelola User', 'url' => 'superadmin/users'],
                    ['icon' => 'fa-building', 'label' => 'Kelola Bidang', 'url' => 'superadmin/bidang'],
                    ['icon' => 'fa-history', 'label' => 'Security Logs', 'url' => 'superadmin/logs'],
                    ['icon' => 'fa-ban', 'label' => 'User Diblokir', 'url' => 'superadmin/blocked'],
                ];
            } elseif ($role === 'kepaladinas') {
                $menuItems = [
                    ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => 'kepaladinas/dashboard'],
                    ['icon' => 'fa-tasks', 'label' => 'Approval Program', 'url' => 'kepaladinas/programs/approval'],
                    ['icon' => 'fa-list', 'label' => 'Approval Kegiatan', 'url' => 'kepaladinas/kegiatan/approval'],
                    ['icon' => 'fa-clipboard-list', 'label' => 'Approval Sub Kegiatan', 'url' => 'kepaladinas/subkegiatan/approval'],
                    ['icon' => 'fa-plane', 'label' => 'Approval SPPD', 'url' => 'kepaladinas/sppd/approval'],
                    ['icon' => 'fa-chart-bar', 'label' => 'Analytics', 'url' => 'kepaladinas/analytics'],
                ];
            } elseif ($role === 'kepalabidang') {
                $menuItems = [
                    ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => 'kepalabidang/dashboard'],
                    ['icon' => 'fa-tasks', 'label' => 'Program', 'url' => 'kepalabidang/programs'],
                    ['icon' => 'fa-list', 'label' => 'Kegiatan', 'url' => 'kepalabidang/kegiatan'],
                    ['icon' => 'fa-clipboard-list', 'label' => 'Sub Kegiatan', 'url' => 'kepalabidang/subkegiatan'],
                    ['icon' => 'fa-plane', 'label' => 'SPPD', 'url' => 'kepalabidang/sppd'],
                    ['icon' => 'fa-chart-line', 'label' => 'Analytics', 'url' => 'kepalabidang/analytics'],
                ];
            } elseif ($role === 'pegawai') {
                $menuItems = [
                    ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => 'pegawai/dashboard'],
                    ['icon' => 'fa-plane', 'label' => 'SPPD Saya', 'url' => 'pegawai/sppd'],
                ];
            } elseif ($role === 'keuangan') {
                $menuItems = [
                    ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => 'keuangan/dashboard'],
                    ['icon' => 'fa-check-circle', 'label' => 'Verifikasi SPPD', 'url' => 'keuangan/verifikasi'],
                    ['icon' => 'fa-file-alt', 'label' => 'Laporan', 'url' => 'keuangan/laporan'],
                ];
            }
            ?>

            <ul class="space-y-1">
                <?php foreach ($menuItems as $item): ?>
                    <?php $isActive = strpos($currentUrl, $item['url']) === 0; ?>
                    <li>
                        <a href="<?= base_url($item['url']) ?>" class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors <?= $isActive ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' ?>">
                            <i class="fas <?= $item['icon'] ?> w-5 text-center"></i>
                            <span><?= $item['label'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>

                <!-- Divider -->
                <li class="pt-4 mt-4 border-t border-gray-200"></li>

                <!-- Profile Link -->
                <li>
                    <a href="<?= base_url('auth/profile') ?>" class="flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-circle w-5 text-center"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>

                <!-- Logout -->
                <li>
                    <button onclick="handleLogout()" class="w-full flex items-center gap-3 px-4 py-3 text-sm rounded-lg transition-colors text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span>Logout</span>
                    </button>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:ml-64 min-h-screen flex flex-col">

        <!-- Topbar -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                <!-- Left Section -->
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Toggle -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Breadcrumb -->
                    <nav class="hidden md:flex items-center text-sm text-gray-600">
                        <span><?= esc($breadcrumb ?? ucfirst(user_role() ?? '')) ?></span>
                    </nav>
                </div>

                <!-- Right Section -->
                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    <div class="relative">
                        <button id="notif-btn" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg focus:outline-none">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notif-badge" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full hidden"></span>
                        </button>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-btn" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg focus:outline-none">
                            <img src="<?= get_user_avatar() ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover" onerror="this.src='<?= base_url('assets/images/default-avatar.png') ?>'">
                            <span class="hidden md:inline text-sm font-medium text-gray-700"><?= esc(user_name() ?? 'Guest') ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 dropdown-menu">
                            <div class="py-1">
                                <a href="<?= base_url('auth/profile') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user w-4"></i>
                                    <span>Profil Saya</span>
                                </a>
                                <hr class="my-1">
                                <button onclick="handleLogout()" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-4"></i>
                                    <span>Logout</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-6 lg:p-8">

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm animate-slide-down" data-aos="fade-down">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-green-800 font-medium">
                                <?= session()->getFlashdata('success') ?>
                            </p>
                        </div>
                        <button type="button" class="ml-auto flex-shrink-0 text-green-500 hover:text-green-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm animate-slide-down" data-aos="fade-down">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-red-800 font-medium">
                                <?= session()->getFlashdata('error') ?>
                            </p>
                        </div>
                        <button type="button" class="ml-auto flex-shrink-0 text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg shadow-sm animate-slide-down" data-aos="fade-down">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-yellow-800 font-medium">
                                <?= session()->getFlashdata('warning') ?>
                            </p>
                        </div>
                        <button type="button" class="ml-auto flex-shrink-0 text-yellow-500 hover:text-yellow-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm animate-slide-down" data-aos="fade-down">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-blue-800 font-medium">
                                <?= session()->getFlashdata('info') ?>
                            </p>
                        </div>
                        <button type="button" class="ml-auto flex-shrink-0 text-blue-500 hover:text-blue-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content Section -->
            <?= $this->renderSection('content') ?>

        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-6">
            <div class="text-center text-sm text-gray-600">
                &copy; <?= date('Y') ?> Aplikasi Perjadin. All rights reserved.
            </div>
        </footer>

    </div>

    <!-- Loading Modal -->
    <div id="loading-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
        <div class="bg-white rounded-lg shadow-xl p-6 flex items-center gap-4">
            <div class="spinner"></div>
            <span id="loading-message" class="text-gray-700 font-medium">Memproses...</span>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 id="confirm-title" class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi</h3>
                <p id="confirm-message" class="text-gray-600 mb-6">Apakah Anda yakin?</p>
                <div class="flex justify-end gap-3">
                    <button id="cancel-btn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button id="confirm-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize
        AOS.init({
            duration: 600,
            once: true
        });

        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        // Ensure CSRF token is included in POST/PUT/DELETE request bodies for jQuery ajax
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = csrfToken;

        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            const method = (options.type || '').toUpperCase();
            if (!method || method === 'GET' || method === 'HEAD') return;

            // If data is FormData, append directly
            if (options.data instanceof FormData) {
                options.data.append(csrfName, csrfHash);
                return;
            }

            // If data is a function (DataTables can provide a function), wrap it
            if (typeof options.data === 'function') {
                const origFn = options.data;
                options.data = function(d) {
                    const result = origFn(d) || {};
                    if (result instanceof FormData) {
                        result.append(csrfName, csrfHash);
                        return result;
                    }
                    if (typeof result === 'string') {
                        return result + (result ? '&' : '') + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
                    }
                    // assume object
                    result[csrfName] = csrfHash;
                    return result;
                };
                return;
            }

            // If data is an object, add property
            if (options.data && typeof options.data === 'object') {
                options.data[csrfName] = csrfHash;
                return;
            }

            // If data is a string or undefined, append as query string
            if (typeof options.data === 'string') {
                options.data = options.data + (options.data ? '&' : '') + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
            } else {
                options.data = encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
            }
        });

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // User Menu Dropdown
        document.getElementById('user-menu-btn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        document.addEventListener('click', function() {
            document.getElementById('user-menu')?.classList.add('hidden');
        });

        // Loading Modal
        function showLoading(message = 'Memproses...') {
            document.getElementById('loading-message').textContent = message;
            document.getElementById('loading-modal').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-modal').classList.add('hidden');
        }

        // Confirm Modal
        function showConfirm(options = {}) {
            return new Promise((resolve) => {
                const modal = document.getElementById('confirm-modal');
                document.getElementById('confirm-title').textContent = options.title || 'Konfirmasi';
                document.getElementById('confirm-message').textContent = options.message || 'Apakah Anda yakin?';
                document.getElementById('confirm-btn').textContent = options.confirmText || 'Ya, Lanjutkan';
                document.getElementById('cancel-btn').textContent = options.cancelText || 'Batal';

                modal.classList.remove('hidden');

                const handleConfirm = () => {
                    modal.classList.add('hidden');
                    cleanup();
                    resolve(true);
                };

                const handleCancel = () => {
                    modal.classList.add('hidden');
                    cleanup();
                    resolve(false);
                };

                const cleanup = () => {
                    document.getElementById('confirm-btn').removeEventListener('click', handleConfirm);
                    document.getElementById('cancel-btn').removeEventListener('click', handleCancel);
                };

                document.getElementById('confirm-btn').addEventListener('click', handleConfirm);
                document.getElementById('cancel-btn').addEventListener('click', handleCancel);
            });
        }

        // Toast Notification
        function showToast(message, type = 'success') {
            const backgrounds = {
                success: 'linear-gradient(to right, #10b981, #34d399)',
                error: 'linear-gradient(to right, #ef4444, #f87171)',
                warning: 'linear-gradient(to right, #f59e0b, #fbbf24)',
                info: 'linear-gradient(to right, #3b82f6, #60a5fa)'
            };

            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: backgrounds[type] || backgrounds.success,
                    borderRadius: "8px",
                    boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)"
                }
            }).showToast();
        }

        // Handle Logout
        async function handleLogout() {
            const confirmed = await showConfirm({
                title: 'Logout',
                message: 'Apakah Anda yakin ingin keluar?',
                confirmText: 'Ya, Logout'
            });

            if (confirmed) {
                showLoading('Logging out...');
                window.location.href = '<?= base_url('auth/logout') ?>';
            }
        }

        // Format Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('.animate-slide-down').forEach(el => {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>