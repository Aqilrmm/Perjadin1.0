<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-2xl p-8 animate-fade-in">
    <!-- Logo & Title -->
    <div class="text-center mb-8">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="h-16 mx-auto mb-4">
        <h1 class="text-3xl font-bold text-gray-800">Sistem Perjadin</h1>
        <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
    </div>

    <!-- Login Form -->
    <form id="loginForm" class="space-y-6">
        <?= csrf_field() ?>
        
        <!-- NIP/NIK Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-id-card mr-2"></i>NIP/NIK
            </label>
            <input type="text" 
                   name="nip_nik" 
                   id="nip_nik"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                   placeholder="Masukkan NIP/NIK"
                   required
                   minlength="16"
                   maxlength="18">
            <span class="text-red-500 text-sm error-nip_nik hidden"></span>
        </div>

        <!-- Password Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2"></i>Password
            </label>
            <div class="relative">
                <input type="password" 
                       name="password" 
                       id="password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="Masukkan password"
                       required
                       minlength="8">
                <button type="button" 
                        id="togglePassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            <span class="text-red-500 text-sm error-password hidden"></span>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember_me" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Ingat saya</span>
            </label>
            <a href="<?= base_url('auth/forgot-password') ?>" class="text-sm text-blue-600 hover:text-blue-800 transition">
                Lupa password?
            </a>
        </div>

        <!-- Login Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition duration-200 shadow-lg">
            <i class="fas fa-sign-in-alt mr-2"></i>Login
        </button>
    </form>

    <!-- Footer -->
    <div class="mt-6 text-center text-sm text-gray-600">
        <p>&copy; <?= date('Y') ?> Aplikasi Perjadin. All rights reserved.</p>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordInput = $('#password');
        const eyeIcon = $('#eyeIcon');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Form validation and submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.error-nip_nik, .error-password').addClass('hidden').text('');
        
        const formData = {
            nip_nik: $('#nip_nik').val(),
            password: $('#password').val(),
            remember_me: $('input[name="remember_me"]').is(':checked')
        };

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit login
        $.ajax({
            url: '<?= base_url('auth/login') ?>',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                Swal.close();
                
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = response.data.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`.error-${field}`).removeClass('hidden').text(errors[field]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Silakan coba lagi'
                    });
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>