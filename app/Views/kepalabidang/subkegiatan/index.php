<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Sub Kegiatan</h1>
<div class="bg-white p-4 rounded shadow">
    <div class="flex justify-between items-center mb-3">
        <div>
            <button id="btn-new-subkegiatan" class="bg-blue-600 text-white px-3 py-1 rounded">+ Buat Sub Kegiatan</button>
        </div>
    </div>
    <table id="subkegiatan-table" class="min-w-full">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kegiatan</th>
                <th>Anggaran</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= view('kepalabidang/subkegiatan/modals/form_subkegiatan') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var table = $('#subkegiatan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepalabidang/subkegiatan/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'kode_sub_kegiatan'
                },
                {
                    data: 'nama_sub_kegiatan'
                },
                {
                    data: 'nama_kegiatan'
                },
                {
                    data: 'anggaran_formatted'
                },
                {
                    data: 'status_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'asc']
            ]
        });

        // Include create modal markup
        $('#subkegiatan-table').closest('div').after($('#modal-form-subkegiatan'));

        var approvedKegiatan = <?= json_encode($approved_kegiatan ?? []) ?>;

        // Open create modal
        $('#btn-new-subkegiatan').on('click', function() {
            var $sel = $('#kegiatan-select');
            $sel.empty();
            $sel.append('<option value="">-- Pilih Kegiatan --</option>');
            approvedKegiatan.forEach(function(k) {
                $sel.append('<option value="' + k.id + '">' + k.kode_kegiatan + ' - ' + k.nama_kegiatan + '</option>');
            });

            if ($('#form-subkegiatan').length) {
                $('#form-subkegiatan')[0].reset();
                $('#form-subkegiatan').find('.form-error').text('');
            }

            $('#modal-form-subkegiatan').removeClass('hidden');
        });

        // Cancel
        $(document).on('click', '.btn-cancel-subkegiatan', function() {
            $('#modal-form-subkegiatan').addClass('hidden');
        });

        // Backdrop close
        $(document).on('click', '#modal-form-subkegiatan', function(e) {
            if (e.target.id === 'modal-form-subkegiatan') {
                $('#modal-form-subkegiatan').addClass('hidden');
            }
        });

        // Submit via fetch
        $('#form-subkegiatan').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var url = form.action || '<?= site_url('kepalabidang/subkegiatan/create') ?>';
            var fd = new FormData(form);

            $(form).find('.form-error').text('');
            showLoading('Menyimpan sub kegiatan...');

            fetch(url, {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) {
                    hideLoading();
                    if (res.status === 422) return res.json().then(function(j) {
                        throw {
                            status: 422,
                            json: j
                        };
                    });
                    if (!res.ok) throw new Error('Network response not ok');
                    return res.json();
                }).then(function(json) {
                    if (json.status) {
                        showToast(json.message || 'Sub kegiatan berhasil dibuat', 'success');
                        $('#modal-form-subkegiatan').addClass('hidden');
                        $('#subkegiatan-table').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: json.message || 'Gagal menyimpan'
                        });
                    }
                }).catch(function(err) {
                    if (err && err.status === 422 && err.json) {
                        var payload = err.json || {};
                        var errors = payload.errors || payload.data || {};
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi gagal',
                            text: payload.message || 'Periksa input Anda.'
                        });
                        Object.keys(errors).forEach(function(field) {
                            var msg = errors[field];
                            if (Array.isArray(msg)) msg = msg.join(', ');
                            $('#form-subkegiatan').find('.form-error[data-field="' + field + '"]').text(msg);
                        });
                        var first = Object.keys(errors)[0];
                        if (first) $('#form-subkegiatan').find('[name="' + first + '"]').focus();
                        return;
                    }
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyimpan sub kegiatan'
                    });
                });
        });
    });
</script>
<?= $this->endSection() ?>