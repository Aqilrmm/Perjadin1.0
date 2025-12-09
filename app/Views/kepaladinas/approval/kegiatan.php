<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Persetujuan Kegiatan</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="kd-kegiatan-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kegiatan</th>
                <th>Program</th>
                <th>Anggaran</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->include('kepaladinas/modals/detail_kegiatan') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var table = $('#kd-kegiatan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepaladinas/kegiatan/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kode_kegiatan'
                },
                {
                    data: 'nama_kegiatan'
                },
                {
                    data: 'nama_program'
                },
                {
                    data: 'anggaran_kegiatan_formatted',
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
            ],
            createdRow: function(row, data, dataIndex) {
                var info = table.page.info();
                $('td', row).eq(0).html(dataIndex + 1 + (info.page * info.length));
            }
        });

        // Detail
        $('#kd-kegiatan-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            if (!id) return;

            $.get('<?= site_url('kepaladinas/kegiatan/detail') ?>/' + id, function(res) {
                if (res.status) {
                    var d = res.data;
                    var html = '<div class="space-y-2 text-sm text-left">';
                    html += '<p><strong>Kode:</strong> ' + (d.kode_kegiatan || '-') + '</p>';
                    html += '<p><strong>Nama:</strong> ' + (d.nama_kegiatan || '-') + '</p>';
                    html += '<p><strong>Program:</strong> ' + (d.nama_program || '-') + '</p>';
                    html += '<p><strong>Anggaran:</strong> ' + (d.anggaran_kegiatan_formatted || d.anggaran_kegiatan || '-') + '</p>';
                    html += '<p><strong>Sisa Anggaran:</strong> ' + (d.sisa_anggaran ? d.sisa_anggaran : '-') + '</p>';
                    html += '<p><strong>Deskripsi:</strong><br/>' + (d.deskripsi || '-') + '</p>';
                    html += '</div>';

                    $('#detail-kegiatan-content').html(html);
                    $('#modal-detail-kegiatan').removeClass('hidden');
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat detail kegiatan', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Gagal memuat detail kegiatan', 'error');
            });
        });

        // Approve
        $('#kd-kegiatan-table').on('click', '.btn-approve', function() {
            var id = $(this).data('id');
            if (!id) return;

            Swal.fire({
                title: 'Yakin menyetujui kegiatan ini?',
                input: 'text',
                inputPlaceholder: 'Catatan (opsional)',
                showCancelButton: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.post('<?= site_url('kepaladinas/kegiatan/approve') ?>/' + id, {
                        catatan: result.value
                    }, function(res) {
                        if (res.status) {
                            Swal.fire('Sukses', res.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message || 'Gagal menyetujui kegiatan', 'error');
                        }
                    }, 'json').fail(function() {
                        Swal.fire('Error', 'Gagal menyetujui kegiatan', 'error');
                    });
                }
            });
        });

        // Reject
        $('#kd-kegiatan-table').on('click', '.btn-reject', function() {
            var id = $(this).data('id');
            if (!id) return;

            Swal.fire({
                title: 'Alasan penolakan',
                input: 'textarea',
                inputPlaceholder: 'Masukkan alasan minimal 10 karakter',
                showCancelButton: true,
                preConfirm: (value) => {
                    if (!value || value.trim().length < 10) {
                        Swal.showValidationMessage('Catatan minimal 10 karakter');
                    }
                    return value;
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.post('<?= site_url('kepaladinas/kegiatan/reject') ?>/' + id, {
                        catatan: result.value
                    }, function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message || 'Gagal menolak kegiatan', 'error');
                        }
                    }, 'json').fail(function() {
                        Swal.fire('Error', 'Gagal menolak kegiatan', 'error');
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>