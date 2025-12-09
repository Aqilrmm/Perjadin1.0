<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Kegiatan</h1>
<div class="bg-white p-4 rounded shadow">
    <div class="flex justify-between items-center mb-3">
        <div>
            <button id="btn-new-kegiatan" class="bg-blue-600 text-white px-3 py-1 rounded">+ Buat Kegiatan</button>
        </div>
    </div>
    <table id="kegiatan-table" class="min-w-full">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Program</th>
                <th>Anggaran</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->include('kepalabidang/kegiatan/modals/detail_kegiatan') ?>
<?= $this->include('kepalabidang/kegiatan/modals/form_kegiatan') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var table = $('#kegiatan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepalabidang/kegiatan/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'kode_kegiatan'
                },
                {
                    data: 'nama_kegiatan'
                },
                {
                    data: 'nama_program'
                },
                {
                    data: 'anggaran_formatted'
                },
                {
                    data: 'status_badge',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data;
                    }
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data;
                    }
                }
            ],
            order: [
                [0, 'asc']
            ]
        });

        // View detail
        $('#kegiatan-table').on('click', '.btn-view', function() {
            var id = $(this).data('id');
            if (!id) return;

            showLoading('Memuat detail...');
            fetch('<?= site_url('kepalabidang/kegiatan/get') ?>/' + id, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(res) {
                return res.json();
            }).then(function(json) {
                hideLoading();
                if (json.status) {
                    var d = json.data;
                    var html = '<div class="space-y-2">';
                    html += '<p><strong>Kode:</strong> ' + (d.kode_kegiatan || '-') + '</p>';
                    html += '<p><strong>Nama:</strong> ' + (d.nama_kegiatan || '-') + '</p>';
                    html += '<p><strong>Program:</strong> ' + (d.nama_program || '-') + '</p>';
                    html += '<p><strong>Anggaran:</strong> ' + (d.anggaran_kegiatan || '-') + '</p>';
                    html += '<p><strong>Sisa Anggaran:</strong> ' + (d.sisa_anggaran || '-') + '</p>';
                    html += '<p><strong>Status:</strong> ' + (d.status || '-') + '</p>';
                    html += '<p><strong>Deskripsi:</strong><br/>' + (d.deskripsi || '-') + '</p>';
                    html += '</div>';

                    $('#detail-kegiatan-content').html(html);
                    $('#modal-detail-kegiatan').removeClass('hidden');
                } else {
                    showToast(json.message || 'Gagal memuat detail', 'error');
                }
            }).catch(function(err) {
                hideLoading();
                console.error(err);
                showToast('Gagal memuat detail', 'error');
            });
        });

        // Close detail modal
        $(document).on('click', '.btn-close-kegiatan', function() {
            $('#modal-detail-kegiatan').addClass('hidden');
        });

        // Backdrop close
        $(document).on('click', '#modal-detail-kegiatan', function(e) {
            if (e.target.id === 'modal-detail-kegiatan') {
                $('#modal-detail-kegiatan').addClass('hidden');
            }
        });

        // Open create modal
        var approvedPrograms = <?= json_encode($approved_programs ?? []) ?>;

        $('#btn-new-kegiatan').on('click', function() {
            // populate program select
            var $sel = $('#program-select');
            $sel.empty();
            $sel.append('<option value="">-- Pilih Program --</option>');
            approvedPrograms.forEach(function(p) {
                $sel.append('<option value="' + p.id + '">' + p.kode_program + ' - ' + p.nama_program + '</option>');
            });

            // clear previous errors/values
            if ($('#form-kegiatan').length) {
                $('#form-kegiatan')[0].reset();
                $('#form-kegiatan').find('.form-error').text('');
            }

            $('#modal-form-kegiatan').removeClass('hidden');
        });

        // Cancel create
        $(document).on('click', '.btn-cancel-kegiatan', function() {
            $('#modal-form-kegiatan').addClass('hidden');
        });

        // Backdrop close for create modal
        $(document).on('click', '#modal-form-kegiatan', function(e) {
            if (e.target.id === 'modal-form-kegiatan') {
                $('#modal-form-kegiatan').addClass('hidden');
            }
        });

        // Submit create via fetch
        $('#form-kegiatan').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var url = form.action;
            var fd = new FormData(form);

            // Clear errors
            $(form).find('.form-error').text('');

            showLoading('Menyimpan kegiatan...');
            fetch(url, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function(res) {
                hideLoading();
                if (res.status === 422) return res.json().then(function(j) {
                    throw {
                        status: 422,
                        json: j
                    };
                });
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            }).then(function(json) {
                if (json.status) {
                    showToast(json.message || 'Kegiatan berhasil dibuat', 'success');
                    $('#modal-form-kegiatan').addClass('hidden');
                    $('#kegiatan-table').DataTable().ajax.reload(null, false);
                } else {
                    // Show alert and inline errors if provided
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: json.message || 'Gagal menyimpan kegiatan'
                    });
                    var serverErrors = json.errors || json.data || {};
                    if (serverErrors) {
                        Object.keys(serverErrors).forEach(function(field) {
                            var msg = serverErrors[field];
                            if (Array.isArray(msg)) msg = msg.join(', ');
                            $('#form-kegiatan').find('.form-error[data-field="' + field + '"]').text(msg);
                        });
                        var first = Object.keys(serverErrors)[0];
                        if (first) $('#form-kegiatan').find('[name="' + first + '"]').focus();
                    }
                }
            }).catch(function(err) {
                if (err && err.status === 422 && err.json) {
                    var payload = err.json || {};
                    var errors = payload.errors || (payload.data && payload.data.errors) || payload.data || {};

                    // show modal alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi gagal',
                        text: payload.message || 'Periksa input Anda.'
                    });

                    Object.keys(errors).forEach(function(field) {
                        var msg = errors[field];
                        if (Array.isArray(msg)) msg = msg.join(', ');
                        $('#form-kegiatan').find('.form-error[data-field="' + field + '"]').text(msg);
                    });

                    var firstField = Object.keys(errors)[0];
                    if (firstField) {
                        $('#form-kegiatan').find('[name="' + firstField + '"]').focus();
                    }
                    return;
                }
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal menyimpan kegiatan'
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>