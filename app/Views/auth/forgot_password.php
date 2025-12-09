<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-2xl p-8">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-key text-blue-600 text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Lupa Password?</h1>
        <p class="text-gray-600 mt-2">Masukkan email untuk reset password</p>
    </div>

    <form id="forgotForm" class="space-y-6">
        <?= csrf_field() ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2"></i>Email
            </label>
            <input type="email" 
                   name="email" 
                   id="email"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="alamat@email.com"
                   required>
            <span class="text-red-500 text-sm error-email hidden"></span>
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset
        </button>

        <a href="<?= base_url('login') ?>" class="block text-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
        </a>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#forgotForm').on('submit', function(e) {
    e.preventDefault();
    
    $('.error-email').addClass('hidden').text('');
    
    Swal.fire({
        title: 'Mengirim...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: '<?= base_url('auth/forgot-password') ?>',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: response.message
            }).then(() => {
                window.location.href = '<?= base_url('login') ?>';
            });
        },
        error: function(xhr) {
            Swal.close();
            if (xhr.status === 422) {
                $('.error-email').removeClass('hidden').text(xhr.responseJSON.errors.email);
            } else {
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
            }
        }
    });
});
</script>
<?= $this->endSection() ?>