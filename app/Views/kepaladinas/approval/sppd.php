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
<div id="modal-detail-sppd" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Detail SPPD</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="detail-sppd-content" class="p-6"></div>
    </div>
</div>

<!-- Modal Preview PDF - IMPROVED -->
<div id="modal-preview-pdf" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[95vh] flex flex-col" style="height:1000px">
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Preview Nota Dinas</h3>
            <div class="flex gap-2">
                <button onclick="refreshPDF()" class="text-blue-600 hover:text-blue-800" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-hidden relative" style="height: 80vh;">
            <!-- Loading indicator -->
            <div id="pdf-loading" class="absolute inset-0 flex items-center justify-center bg-gray-100">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-2"></i>
                    <p class="text-gray-600">Loading PDF...</p>
                </div>
            </div>
            <!-- PDF Embed -->
            <embed id="pdf-embed" class="w-full h-full hidden" type="application/pdf">
            <!-- Fallback iframe -->
            <iframe id="pdf-frame" class="w-full h-full border-0 hidden"></iframe>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let currentPreviewId = null;

$(function() {
    var table = $('#kd-sppd-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('kepaladinas/sppd/datatable') ?>',
            type: 'POST'
        },
        columns: [
            { data: null, orderable: false, searchable: false },
            { 
                data: 'no_sppd',
                render: function(data) {
                    return data || '<span class="text-gray-400 italic">Belum ada nomor</span>';
                }
            },
            { data: 'nama_bidang' },
            { data: 'tempat_tujuan' },
            { 
                data: 'tanggal_berangkat',
                render: function(data) {
                    if (!data || data === '0000-00-00') return '-';
                    return new Date(data).toLocaleDateString('id-ID');
                }
            },
            { 
                data: 'tanggal_kembali',
                render: function(data) {
                    if (!data || data === '0000-00-00') return '-';
                    return new Date(data).toLocaleDateString('id-ID');
                }
            },
            { data: 'jumlah_pegawai', className: 'text-center' },
            { data: 'estimasi_formatted' },
            { data: 'status_badge' },
            { data: 'action' }
        ],
        order: [[4, 'desc']],
        createdRow: function(row, data, dataIndex) {
            var info = table.page.info();
            $('td', row).eq(0).html(dataIndex + 1 + (info.page * info.length));
        }
    });

    // Detail
    $('#kd-sppd-table').on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        $.get('<?= site_url('kepaladinas/sppd/detail') ?>/' + id, function(res) {
            if (res.status) {
                var sppd = res.data.sppd;
                var pegawai = res.data.pegawai_list;
                
                var html = '<div class="space-y-4">';
                
                // Informasi Umum
                html += '<div class="border-b pb-4">';
                html += '<h4 class="font-semibold mb-3">Informasi Umum</h4>';
                html += '<div class="grid grid-cols-2 gap-3 text-sm">';
                html += '<div><span class="text-gray-600">No SPPD:</span> <span class="font-medium">' + (sppd.no_sppd || '-') + '</span></div>';
                html += '<div><span class="text-gray-600">Bidang:</span> <span class="font-medium">' + (sppd.nama_bidang || '-') + '</span></div>';
                html += '<div><span class="text-gray-600">Program:</span> <span class="font-medium">' + (sppd.nama_program || '-') + '</span></div>';
                html += '<div><span class="text-gray-600">Status:</span> ' + (sppd.status_badge || '-') + '</div>';
                html += '</div>';
                html += '</div>';
                
                // Perjalanan
                html += '<div class="border-b pb-4">';
                html += '<h4 class="font-semibold mb-3">Detail Perjalanan</h4>';
                html += '<div class="grid grid-cols-2 gap-3 text-sm">';
                html += '<div><span class="text-gray-600">Tipe:</span> <span class="font-medium">' + (sppd.tipe_perjalanan || '-') + '</span></div>';
                html += '<div><span class="text-gray-600">Tempat Tujuan:</span> <span class="font-medium">' + (sppd.tempat_tujuan || '-') + '</span></div>';
                html += '<div><span class="text-gray-600">Tanggal Berangkat:</span> <span class="font-medium">' + formatDate(sppd.tanggal_berangkat) + '</span></div>';
                html += '<div><span class="text-gray-600">Tanggal Kembali:</span> <span class="font-medium">' + formatDate(sppd.tanggal_kembali) + '</span></div>';
                html += '<div><span class="text-gray-600">Lama Perjalanan:</span> <span class="font-medium">' + (sppd.lama_perjalanan || '-') + ' hari</span></div>';
                html += '<div><span class="text-gray-600">Alat Angkut:</span> <span class="font-medium">' + (sppd.alat_angkut || '-') + '</span></div>';
                html += '</div>';
                html += '<div class="mt-3"><span class="text-gray-600">Maksud:</span> <p class="mt-1">' + (sppd.maksud_perjalanan || '-') + '</p></div>';
                html += '<div class="mt-2"><span class="text-gray-600">Dasar Surat:</span> <span class="font-medium">' + (sppd.dasar_surat || '-') + '</span></div>';
                html += '</div>';
                
                // Biaya
                html += '<div class="border-b pb-4">';
                html += '<h4 class="font-semibold mb-3">Anggaran</h4>';
                html += '<div class="text-sm">';
                html += '<span class="text-gray-600">Estimasi Biaya:</span> <span class="font-semibold text-lg">' + (sppd.estimasi_formatted || '-') + '</span>';
                html += '</div>';
                html += '</div>';
                
                // Pegawai
                html += '<div>';
                html += '<h4 class="font-semibold mb-3">Daftar Pegawai (' + pegawai.length + ')</h4>';
                html += '<table class="min-w-full text-sm">';
                html += '<thead><tr class="border-b">';
                html += '<th class="text-left py-2">No</th>';
                html += '<th class="text-left py-2">Nama</th>';
                html += '<th class="text-left py-2">NIP/NIK</th>';
                html += '<th class="text-left py-2">Jabatan</th>';
                html += '</tr></thead><tbody>';
                
                pegawai.forEach(function(p, i) {
                    html += '<tr class="border-b">';
                    html += '<td class="py-2">' + (i+1) + '</td>';
                    html += '<td class="py-2">' + (p.nama || '-') + '</td>';
                    html += '<td class="py-2">' + (p.nip_nik || '-') + '</td>';
                    html += '<td class="py-2">' + (p.jabatan || '-') + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                html += '</div>';
                
                // Catatan
                if (sppd.catatan_kepala_dinas) {
                    html += '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-3">';
                    html += '<p class="font-semibold text-sm">Catatan Kepala Dinas:</p>';
                    html += '<p class="text-sm mt-1">' + sppd.catatan_kepala_dinas + '</p>';
                    html += '</div>';
                }
                
                html += '</div>';
                
                $('#detail-sppd-content').html(html);
                $('#modal-detail-sppd').removeClass('hidden');
            } else {
                Swal.fire('Error', res.message || 'Gagal memuat detail', 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat detail SPPD', 'error');
        });
    });

    // Preview PDF - IMPROVED
    $('#kd-sppd-table').on('click', '.btn-preview', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        currentPreviewId = id;
        loadPDF(id);
    });

    // Approve
    $('#kd-sppd-table').on('click', '.btn-approve', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Setujui SPPD?',
            text: 'Nomor SPPD akan digenerate otomatis',
            input: 'textarea',
            inputPlaceholder: 'Catatan (opsional)',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            showLoaderOnConfirm: true,
            preConfirm: (catatan) => {
                return $.post('<?= site_url('kepaladinas/sppd/approve') ?>/' + id, {catatan: catatan})
                    .then(res => res)
                    .catch(err => {
                        Swal.showValidationMessage('Error: ' + (err.responseJSON?.message || err.statusText));
                    });
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value?.status) {
                Swal.fire('Berhasil', 'SPPD disetujui dengan nomor: ' + result.value.data?.no_sppd, 'success');
                table.ajax.reload();
            }
        });
    });

    // Reject
    $('#kd-sppd-table').on('click', '.btn-reject', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Tolak SPPD',
            input: 'textarea',
            inputPlaceholder: 'Alasan penolakan (min 10 karakter)',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            confirmButtonColor: '#ef4444',
            inputValidator: (value) => {
                if (!value || value.length < 10) {
                    return 'Alasan minimal 10 karakter';
                }
            },
            showLoaderOnConfirm: true,
            preConfirm: (catatan) => {
                return $.post('<?= site_url('kepaladinas/sppd/reject') ?>/' + id, {catatan: catatan})
                    .then(res => res)
                    .catch(err => {
                        Swal.showValidationMessage('Error: ' + (err.responseJSON?.message || err.statusText));
                    });
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value?.status) {
                Swal.fire('Berhasil', result.value.message, 'success');
                table.ajax.reload();
            }
        });
    });
});

function loadPDF(id) {
    var previewUrl = '<?= site_url('kepaladinas/sppd/preview') ?>/' + id;
    
    // Show modal with loading
    $('#modal-preview-pdf').removeClass('hidden');
    $('#pdf-loading').removeClass('hidden');
    $('#pdf-embed').addClass('hidden');
    $('#pdf-frame').addClass('hidden');
    
    // Try using embed first (works better in most browsers)
    var embed = $('#pdf-embed');
    embed.attr('src', previewUrl);
    
    // Handle load
    setTimeout(function() {
        $('#pdf-loading').addClass('hidden');
        embed.removeClass('hidden');
    }, 500);
    
    // Fallback to iframe if embed fails
    embed.on('error', function() {
        console.log('Embed failed, trying iframe...');
        embed.addClass('hidden');
        $('#pdf-frame').attr('src', previewUrl).removeClass('hidden');
    });
}

function refreshPDF() {
    if (currentPreviewId) {
        loadPDF(currentPreviewId);
    }
}

function formatDate(date) {
    if (!date || date === '0000-00-00') return '-';
    return new Date(date).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
}

function closeDetailModal() {
    $('#modal-detail-sppd').addClass('hidden');
}

function closePreviewModal() {
    $('#modal-preview-pdf').addClass('hidden');
    $('#pdf-embed').attr('src', '');
    $('#pdf-frame').attr('src', '');
    currentPreviewId = null;
}

// Close on click outside
$('#modal-detail-sppd, #modal-preview-pdf').on('click', function(e) {
    if (e.target === this) {
        if (this.id === 'modal-preview-pdf') {
            closePreviewModal();
        } else {
            closeDetailModal();
        }
    }
});
</script>
<?= $this->endSection() ?>