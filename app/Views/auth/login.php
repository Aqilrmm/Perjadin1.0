<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Login</h2>
    <form method="post" action="<?= site_url('auth/login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>NIP / NIK</label>
            <input name="nip_nik" class="w-full border p-2 rounded" autofocus>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input name="password" type="password" class="w-full border p-2 rounded">
        </div>
        <div class="flex items-center justify-between">
            <div><label><input type="checkbox" name="remember"> Remember me</label></div>
            <a href="<?= site_url('auth/forgot-password') ?>" class="text-sm text-blue-600">Forgot Password?</a>
        </div>
        <div class="mt-4">
            <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>Login - Aplikasi Perjadin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">

    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-center">
                    <div class="w-24 h-24 bg-white rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-plane-departure text-5xl text-blue-600"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Aplikasi Perjadin</h1>
                    <p class="text-blue-100">Sistem Perjalanan Dinas</p>
                </div>

                <!-- Form -->
                <div class="p-8">
                    <form id="loginForm">
                        <!-- NIP/NIK -->
                        <div class="mb-6">
                            <label for="nip_nik" class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-id-card text-blue-600 mr-2"></i>NIP/NIK
                            </label>
                            <input
                                type="text"
                                id="nip_nik"
                                name="nip_nik"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Masukkan NIP/NIK"
                                required
                                minlength="16"
                                maxlength="18">
                            <span class="text-red-500 text-sm mt-1 hidden" id="error_nip_nik"></span>
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock text-blue-600 mr-2"></i>Password
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-12"
                                    placeholder="Masukkan password"
                                    required
                                    minlength="8">
                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <span class="text-red-500 text-sm mt-1 hidden" id="error_password"></span>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-6 flex items-center justify-between">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="remember_me"
                                    name="remember_me"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Ingat saya</span>
                            </label>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                Lupa password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            id="btnLogin"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span id="btnText">Masuk</span>
                            <i class="fas fa-spinner fa-spin ml-2 hidden" id="btnSpinner"></i>
                        </button>
                    </form>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 p-4 text-center text-sm text-gray-600">
                    <p>&copy; 2024 Aplikasi Perjadin. All rights reserved.</p>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-lg p-4 text-white text-center">
                <p class="text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Untuk bantuan, hubungi administrator sistem
                </p>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            const baseUrl = window.location.origin + '/perjadin';

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIcon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Auto-focus NIP field
            $('#nip_nik').focus();

            // Form submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.text-red-500').addClass('hidden');
                $('input').removeClass('border-red-500');

                // Get form data
                const formData = {
                    nip_nik: $('#nip_nik').val(),
                    password: $('#password').val(),
                    remember_me: $('#remember_me').is(':checked')
                };

                // Disable button and show spinner
                $('#btnLogin').prop('disabled', true);
                $('#btnText').text('Memproses...');
                $('#btnSpinner').removeClass('hidden');

                // AJAX request
                $.ajax({
                    url: baseUrl + '/auth/login',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = baseUrl + response.data.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Gagal',
                                text: response.message
                            });

                            // Re-enable button
                            $('#btnLogin').prop('disabled', false);
                            $('#btnText').text('Masuk');
                            $('#btnSpinner').addClass('hidden');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        if (response && response.errors) {
                            // Display validation errors
                            $.each(response.errors, function(field, message) {
                                $('#error_' + field).text(message).removeClass('hidden');
                                $('#' + field).addClass('border-red-500');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response?.message || 'Terjadi kesalahan pada server'
                            });
                        }

                        // Re-enable button
                        $('#btnLogin').prop('disabled', false);
                        $('#btnText').text('Masuk');
                        $('#btnSpinner').addClass('hidden');
                    }
                });
            });

            // Enter key handler
            $('input').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#loginForm').submit();
                }
            });
        });
    </script>
</body>

</html>