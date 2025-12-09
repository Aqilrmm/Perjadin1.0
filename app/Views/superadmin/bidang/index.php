<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Kelola Bidang</h1>
    <div>
        <button id="btn-add-bidang" class="bg-blue-600 text-white px-3 py-1 rounded">+ Tambah Bidang</button>
    </div>
</div>

<div class="bg-white p-4 rounded shadow">
    <table id="bidang-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Bidang</th>
                <th>Jumlah Pegawai</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var table = $('#bidang-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('superadmin/bidang/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'kode_bidang'
                },
                {
                    data: 'nama_bidang'
                },
                {
                    data: 'jumlah_pegawai'
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
                [1, 'asc']
            ]
        });

        // Show modal add bidang
        $('#btn-add-bidang').on('click', function() {
            $('#form-bidang')[0].reset();
            $('#form-bidang').data('id', '');
            $('#modal-form-bidang').removeClass('hidden');
        });

        // Delegate edit button
        $('#bidang-table').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.get('<?= site_url('superadmin/bidang/get') ?>/' + id, function(res) {
                if (res.status) {
                    var b = res.data;
                    $('#form-bidang [name="kode_bidang"]').val(b.kode_bidang);
                    $('#form-bidang [name="nama_bidang"]').val(b.nama_bidang);
                    $('#form-bidang').data('id', id);
                    $('#modal-form-bidang').removeClass('hidden');
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat data', 'error');
                }
            }, 'json');
        });

        // Delete handler
        $('#bidang-table').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus bidang?',
                icon: 'warning',
                showCancelButton: true
            }).then(function(res) {
                if (res.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('superadmin/bidang/delete') ?>/' + id,
                        type: 'DELETE',
                        success: function(r) {
                            if (r.status) {
                                Swal.fire('Terhapus', r.message, 'success');
                                $('#bidang-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error', r.message || 'Gagal menghapus', 'error');
                            }
                        },
                        dataType: 'json'
                    });
                }
            });
        });

        // Submit form (create or update)
        $('#form-bidang').on('submit', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = id ? '<?= site_url('superadmin/bidang/update') ?>/' + id : '<?= site_url('superadmin/bidang/create') ?>';
            var data = $(this).serialize();
            var $btn = $(this).find('button[type="submit"]');
            var orig = $btn.data('orig') || $btn.html();
            $btn.data('orig', orig);
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    $btn.prop('disabled', true).text('Mengirim...');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(orig);
                },
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Sukses', res.message, 'success');
                        $('#modal-form-bidang').addClass('hidden');
                        $('#bidang-table').DataTable().ajax.reload();
                    } else {
                        if (res.errors) {
                            var html = '<ul style="text-align:left;margin:0;padding-left:1.2em;">';
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
                    var j = xhr.responseJSON;
                    var msg = j && j.message ? j.message : 'Terjadi error';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // Close modal when clicking outside (optional)
        $('#modal-form-bidang').on('click', function(e) {
            if (e.target.id === 'modal-form-bidang') $(this).addClass('hidden');
        });
    });
</script>

<?= $this->include('superadmin/bidang/modals/form_bidang') ?>

<?= $this->endSection() ?>