<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="bg-white p-4 rounded shadow max-w-3xl mx-auto">
    <h2 class="text-lg font-semibold mb-4">Profil Saya</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="text-center">
                <?php $foto = $user['foto'] ?? null; ?>
                <img id="profile-photo" src="<?= $foto ? base_url('uploads/foto_profile/' . $foto) : base_url('assets/images/default-avatar.png') ?>" class="w-40 h-40 object-cover rounded-full mx-auto mb-3" alt="Profile Photo">
                <form id="form-upload-photo" method="post" action="<?= site_url('auth/profile/uploadPhoto') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="file" name="foto" id="input-foto" accept="image/*" class="block mx-auto mb-2">
                    <div class="flex justify-center gap-2">
                        <button type="submit" class="btn-upload bg-blue-600 text-white px-3 py-1 rounded">Upload</button>
                        <button type="button" id="btn-delete-photo" class="bg-red-100 px-3 py-1 rounded">Hapus</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-span-2">
            <form id="form-profile" method="post" action="<?= site_url('auth/profile/update') ?>">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input name="nama" value="<?= esc($user['nama'] ?? '') ?>" class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input name="email" type="email" value="<?= esc($user['email'] ?? '') ?>" class="w-full border p-2 rounded">
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <a href="<?= site_url('/') ?>" class="px-3 py-1 border inline-block">Batal</a>
                    <button type="submit" class="btn-save bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
                </div>
            </form>

            <hr class="my-6">

            <h3 class="text-md font-semibold mb-3">Ganti Password</h3>
            <form id="form-change-password" method="post" action="<?= site_url('auth/profile/changePassword') ?>">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Password Lama</label>
                        <input name="current_password" type="password" class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password Baru</label>
                        <input name="new_password" type="password" class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                        <input name="confirm_password" type="password" class="w-full border p-2 rounded">
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-change bg-yellow-500 text-white px-3 py-1 rounded">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Profile update via AJAX
        $('#form-profile').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('.btn-save');
            var orig = $btn.html();
            $btn.prop('disabled', true).html('Menyimpan...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Sukses', res.message, 'success');
                    } else {
                        var msg = res.message || 'Validasi gagal';
                        if (res.errors) {
                            var html = '<ul style="text-align:left;padding-left:1.2em;">';
                            $.each(res.errors, function(k, v) {
                                html += '<li>' + v + '</li>';
                            });
                            html += '</ul>';
                            Swal.fire({
                                title: 'Validasi gagal',
                                html: html,
                                icon: 'error'
                            });
                        } else {
                            Swal.fire('Error', msg, 'error');
                        }
                    }
                },
                error: function(xhr) {
                    var json = xhr.responseJSON;
                    var msg = (json && json.message) ? json.message : 'Terjadi error';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Change password
        $('#form-change-password').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('.btn-change');
            var orig = $btn.html();
            $btn.prop('disabled', true).html('Memproses...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Sukses', res.message, 'success');
                        $('#form-change-password')[0].reset();
                    } else {
                        if (res.errors) {
                            var html = '<ul style="text-align:left;padding-left:1.2em;">';
                            $.each(res.errors, function(k, v) {
                                html += '<li>' + v + '</li>';
                            });
                            html += '</ul>';
                            Swal.fire({
                                title: 'Validasi gagal',
                                html: html,
                                icon: 'error'
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Gagal', 'error');
                        }
                    }
                },
                error: function(xhr) {
                    var json = xhr.responseJSON;
                    var msg = (json && json.message) ? json.message : 'Terjadi error';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Upload photo
        $('#form-upload-photo').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('.btn-upload');
            var orig = $btn.html();
            $btn.prop('disabled', true).html('Mengupload...');

            var fd = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Sukses', res.message, 'success');
                        if (res.data && res.data.foto_url) {
                            $('#profile-photo').attr('src', res.data.foto_url + '?v=' + Date.now());
                        }
                    } else {
                        Swal.fire('Error', res.message || 'Gagal mengupload', 'error');
                    }
                },
                error: function(xhr) {
                    var json = xhr.responseJSON;
                    var msg = (json && json.message) ? json.message : 'Terjadi error';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Delete photo
        $('#btn-delete-photo').on('click', function() {
            Swal.fire({
                title: 'Yakin hapus foto?',
                icon: 'warning',
                showCancelButton: true
            }).then(function(res) {
                if (!res.isConfirmed) return;
                $.ajax({
                        url: '<?= site_url('auth/profile/deletePhoto') ?>',
                        method: 'POST',
                        dataType: 'json'
                    })
                    .done(function(resp) {
                        if (resp.status) {
                            Swal.fire('Terhapus', resp.message, 'success');
                            $('#profile-photo').attr('src', '<?= base_url('assets/images/default-avatar.png') ?>');
                        } else {
                            Swal.fire('Error', resp.message || 'Gagal menghapus', 'error');
                        }
                    }).fail(function() {
                        Swal.fire('Error', 'Gagal menghapus foto', 'error');
                    });
            });
        });
    });
</script>
<?= $this->endSection() ?>