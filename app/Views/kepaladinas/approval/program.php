<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Persetujuan Program</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="kd-programs-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Program</th>
                <th>Bidang</th>
                <th>Tahun</th>
                <th>Anggaran</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->include('kepaladinas/modals/detail_program') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var table = $('#kd-programs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepaladinas/programs/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kode_program'
                },
                {
                    data: 'nama_program'
                },
                {
                    data: 'nama_bidang'
                },
                {
                    data: 'tahun_anggaran'
                },
                {
                    data: 'jumlah_anggaran_formatted',
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
        $('#kd-programs-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            if (!id) return;

            $.get('<?= site_url('kepaladinas/programs/detail') ?>/' + id, function(res) {
                if (res.status) {
                    var d = res.data;
                    var html = '<div class="space-y-2 text-sm text-left">';
                    html += '<p><strong>Kode:</strong> ' + (d.kode_program || d.kode || '-') + '</p>';
                    html += '<p><strong>Nama:</strong> ' + (d.nama_program || d.nama || '-') + '</p>';
                    html += '<p><strong>Bidang:</strong> ' + (d.nama_bidang || '-') + '</p>';
                    html += '<p><strong>Tahun:</strong> ' + (d.tahun || '-') + '</p>';
                    html += '<p><strong>Anggaran:</strong> ' + (d.jumlah_anggaran_formatted || d.jumlah_anggaran || '-') + '</p>';
                    html += '<p><strong>Status:</strong> ' + (d.status || '-') + '</p>';
                    html += '<p><strong>Deskripsi:</strong><br/>' + (d.deskripsi || '-') + '</p>';
                    html += '</div>';

                    $('#detail-program-content').html(html);
                    $('#modal-detail-program').removeClass('hidden');
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat detail program', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Gagal memuat detail program', 'error');
            });
        });

        // Approve
        $('#kd-programs-table').on('click', '.btn-approve', function() {
            var id = $(this).data('id');
            if (!id) return;

            Swal.fire({
                title: 'Yakin menyetujui program ini?',
                input: 'text',
                inputPlaceholder: 'Catatan (opsional)',
                showCancelButton: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.post('<?= site_url('kepaladinas/programs/approve') ?>/' + id, {
                        catatan: result.value
                    }, function(res) {
                        if (res.status) {
                            Swal.fire('Sukses', res.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message || 'Gagal menyetujui program', 'error');
                        }
                    }, 'json').fail(function() {
                        Swal.fire('Error', 'Gagal menyetujui program', 'error');
                    });
                }
            });
        });

        // Reject
        $('#kd-programs-table').on('click', '.btn-reject', function() {
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
                    $.post('<?= site_url('kepaladinas/programs/reject') ?>/' + id, {
                        catatan: result.value
                    }, function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message || 'Gagal menolak program', 'error');
                        }
                    }, 'json').fail(function() {
                        Swal.fire('Error', 'Gagal menolak program', 'error');
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>