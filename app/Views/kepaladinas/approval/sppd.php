<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Persetujuan SPPD</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="kd-sppd-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>No SPPD</th>
                <th>Bidang</th>
                <th>Tujuan</th>
                <th>Tanggal Berangkat</th>
                <th>Tanggal Kembali</th>
                <th>Jumlah Pegawai</th>
                <th>Estimasi Biaya</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Detail SPPD -->
<div id="modal-detail-sppd" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Detail SPPD</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="detail-sppd-content" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Modal Preview PDF -->
<div id="modal-preview-pdf" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[95vh] flex flex-col">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Preview SPPD</h3>
            <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="preview-pdf-content" class="flex-1 p-6 overflow-auto">
            <iframe id="pdf-frame" class="w-full h-full min-h-[600px] border-0"></iframe>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var table = $('#kd-sppd-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepaladinas/sppd/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'no_sppd',
                    render: function(data) {
                        return data || '<span class="text-gray-400 italic">Belum ada nomor</span>';
                    }
                },
                {
                    data: 'nama_bidang'
                },
                {
                    data: 'tempat_tujuan'
                },
                {
                    data: 'tanggal_berangkat',
                    render: function(data) {
                        if (!data || data === '0000-00-00') return '<span class="text-gray-400">-</span>';
                        return new Date(data).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'tanggal_kembali',
                    render: function(data) {
                        if (!data || data === '0000-00-00') return '<span class="text-gray-400">-</span>';
                        return new Date(data).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'jumlah_pegawai',
                    className: 'text-center'
                },
                {
                    data: 'estimasi_formatted',
                    orderable: false,
                    searchable: false
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
                [4, 'desc']
            ],
            createdRow: function(row, data, dataIndex) {
                var info = table.page.info();
                $('td', row).eq(0).html(dataIndex + 1 + (info.page * info.length));
            }
        });

        // Detail
        $('#kd-sppd-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            if (!id) return;

            showLoading('Memuat detail SPPD...');

            $.get('<?= site_url('kepaladinas/sppd/detail') ?>/' + id, function(res) {
                hideLoading();
                if (res.status) {
                    var d = res.data;
                    var pegawaiList = res.pegawai_list || [];

                    var html = '<div class="space-y-6">';
                    
                    // Informasi Umum
                    html += '<div class="border-b pb-4">';
                    html += '<h4 class="font-semibold text-gray-900 mb-3">Informasi Umum</h4>';
                    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">';
                    html += '<div><span class="font-medium text-gray-700">No SPPD:</span> <span class="text-gray-600">' + (d.no_sppd || '<em>Belum ada nomor</em>') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Bidang:</span> <span class="text-gray-600">' + (d.nama_bidang || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Program:</span> <span class="text-gray-600">' + (d.nama_program || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Tipe Perjalanan:</span> <span class="text-gray-600">' + (d.tipe_perjalanan || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Status:</span> ' + (d.status_badge || '-') + '</div>';
                    html += '</div>';
                    html += '</div>';

                    // Tujuan dan Waktu
                    html += '<div class="border-b pb-4">';
                    html += '<h4 class="font-semibold text-gray-900 mb-3">Tujuan dan Waktu</h4>';
                    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">';
                    html += '<div><span class="font-medium text-gray-700">Tempat Berangkat:</span> <span class="text-gray-600">' + (d.tempat_berangkat || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Tempat Tujuan:</span> <span class="text-gray-600">' + (d.tempat_tujuan || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Maksud Perjalanan:</span> <span class="text-gray-600">' + (d.maksud_perjalanan || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Dasar Surat:</span> <span class="text-gray-600">' + (d.dasar_surat || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Tanggal Berangkat:</span> <span class="text-gray-600">' + (d.tanggal_berangkat && d.tanggal_berangkat !== '0000-00-00' ? new Date(d.tanggal_berangkat).toLocaleDateString('id-ID', {weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'}) : '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Tanggal Kembali:</span> <span class="text-gray-600">' + (d.tanggal_kembali && d.tanggal_kembali !== '0000-00-00' ? new Date(d.tanggal_kembali).toLocaleDateString('id-ID', {weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'}) : '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Lama Perjalanan:</span> <span class="text-gray-600">' + (d.lama_perjalanan || '-') + ' hari</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Alat Angkut:</span> <span class="text-gray-600">' + (d.alat_angkut || '-') + '</span></div>';
                    html += '<div><span class="font-medium text-gray-700">Penanggung Jawab:</span> <span class="text-gray-600">' + (d.penanggung_jawab_nama || '-') + '</span></div>';
                    html += '</div>';
                    html += '</div>';

                    // Biaya
                    html += '<div class="border-b pb-4">';
                    html += '<h4 class="font-semibold text-gray-900 mb-3">Anggaran</h4>';
                    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">';
                    html += '<div><span class="font-medium text-gray-700">Estimasi Biaya:</span> <span class="text-gray-600 font-semibold">' + (d.estimasi_formatted || d.estimasi_biaya || '-') + '</span></div>';
                    html += '</div>';
                    html += '</div>';

                    // Daftar Pegawai
                    html += '<div class="border-b pb-4">';
                    html += '<h4 class="font-semibold text-gray-900 mb-3">Daftar Pegawai (' + pegawaiList.length + ' orang)</h4>';
                    html += '<div class="overflow-x-auto">';
                    html += '<table class="min-w-full divide-y divide-gray-200">';
                    html += '<thead class="bg-gray-50">';
                    html += '<tr>';
                    html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>';
                    html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>';
                    html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>';
                    html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody class="bg-white divide-y divide-gray-200">';
                    
                    if (pegawaiList.length > 0) {
                        pegawaiList.forEach(function(p, idx) {
                            html += '<tr>';
                            html += '<td class="px-4 py-3 text-sm">' + (idx + 1) + '</td>';
                            html += '<td class="px-4 py-3 text-sm">' + (p.nama || '-') + '</td>';
                            html += '<td class="px-4 py-3 text-sm">' + (p.nip || '-') + '</td>';
                            html += '<td class="px-4 py-3 text-sm">' + (p.jabatan || '-') + '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html += '<tr><td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">Tidak ada data pegawai</td></tr>';
                    }
                    
                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';
                    html += '</div>';

                    // Catatan
                    if (d.catatan_kepala_dinas) {
                        html += '<div>';
                        html += '<h4 class="font-semibold text-gray-900 mb-2">Catatan Kepala Dinas</h4>';
                        html += '<p class="text-sm text-gray-600 bg-yellow-50 p-3 rounded border-l-4 border-yellow-400">' + d.catatan_kepala_dinas.replace(/\n/g, '<br>') + '</p>';
                        html += '</div>';
                    }

                    if (d.catatan_keuangan) {
                        html += '<div>';
                        html += '<h4 class="font-semibold text-gray-900 mb-2">Catatan Keuangan</h4>';
                        html += '<p class="text-sm text-gray-600 bg-blue-50 p-3 rounded border-l-4 border-blue-400">' + d.catatan_keuangan.replace(/\n/g, '<br>') + '</p>';
                        html += '</div>';
                    }

                    html += '</div>';

                    $('#detail-sppd-content').html(html);
                    $('#modal-detail-sppd').removeClass('hidden');
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat detail SPPD', 'error');
                }
            }, 'json').fail(function() {
                hideLoading();
                Swal.fire('Error', 'Gagal memuat detail SPPD', 'error');
            });
        });

        // Preview PDF
        $('#kd-sppd-table').on('click', '.btn-preview', function() {
            var id = $(this).data('id');
            if (!id) return;

            // Show modal with iframe
            var previewUrl = '<?= site_url('kepaladinas/sppd/preview') ?>/' + id;
            $('#pdf-frame').attr('src', previewUrl);
            $('#modal-preview-pdf').removeClass('hidden');
        });

        // Approve
        $('#kd-sppd-table').on('click', '.btn-approve', function() {
            var id = $(this).data('id');
            if (!id) return;

            Swal.fire({
                title: 'Setujui SPPD ini?',
                text: 'SPPD akan disetujui dan nomor SPPD akan digenerate otomatis',
                input: 'textarea',
                inputPlaceholder: 'Catatan persetujuan (opsional)',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: (catatan) => {
                    return $.post('<?= site_url('kepaladinas/sppd/approve') ?>/' + id, {
                        catatan: catatan
                    }).then(function(res) {
                        return res;
                    }).catch(function(err) {
                        Swal.showValidationMessage('Request failed: ' + (err.responseJSON?.message || err.statusText));
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(function(result) {
                if (result.isConfirmed && result.value) {
                    if (result.value.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'SPPD berhasil disetujui dengan nomor: ' + (result.value.data?.no_sppd || ''),
                            confirmButtonColor: '#10b981'
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error', result.value.message || 'Gagal menyetujui SPPD', 'error');
                    }
                }
            });
        });

        // Reject
        $('#kd-sppd-table').on('click', '.btn-reject', function() {
            var id = $(this).data('id');
            if (!id) return;

            Swal.fire({
                title: 'Tolak SPPD',
                text: 'Alasan penolakan wajib diisi',
                input: 'textarea',
                inputPlaceholder: 'Masukkan alasan minimal 10 karakter',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Tolak SPPD',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value || value.trim().length < 10) {
                        return 'Catatan penolakan minimal 10 karakter';
                    }
                },
                showLoaderOnConfirm: true,
                preConfirm: (catatan) => {
                    return $.post('<?= site_url('kepaladinas/sppd/reject') ?>/' + id, {
                        catatan: catatan
                    }).then(function(res) {
                        return res;
                    }).catch(function(err) {
                        Swal.showValidationMessage('Request failed: ' + (err.responseJSON?.message || err.statusText));
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(function(result) {
                if (result.isConfirmed && result.value) {
                    if (result.value.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: result.value.message || 'SPPD berhasil ditolak',
                            confirmButtonColor: '#10b981'
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error', result.value.message || 'Gagal menolak SPPD', 'error');
                    }
                }
            });
        });
    });

    // Close Modal Functions
    function closeDetailModal() {
        $('#modal-detail-sppd').addClass('hidden');
    }

    function closePreviewModal() {
        $('#modal-preview-pdf').addClass('hidden');
        $('#pdf-frame').attr('src', '');
    }

    // Close modal when clicking outside
    $('#modal-detail-sppd, #modal-preview-pdf').on('click', function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
            if (this.id === 'modal-preview-pdf') {
                $('#pdf-frame').attr('src', '');
            }
        }
    });
</script>
<?= $this->endSection() ?>