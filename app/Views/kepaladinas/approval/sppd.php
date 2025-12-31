<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Persetujuan SPPD</h1>

<!-- Filter Status (Optional) -->
<div class="bg-white p-4 rounded shadow mb-4">
    <div class="flex gap-4 items-center">
        <label class="text-sm font-medium">Filter Status:</label>
        <select id="filter_status" class="border rounded px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu Persetujuan</option>
            <option value="approved">Disetujui</option>
            <option value="rejected">Ditolak</option>
        </select>
    </div>
</div>

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
        
        <!-- Download Section for Approved SPPD -->
        <div id="detail-download-section" class="hidden border-t border-gray-200 px-6 py-4 bg-gray-50">
            <h4 class="font-semibold mb-3">Dokumen SPPD</h4>
            <div class="grid grid-cols-3 gap-3">
                <a id="download-nota-dinas" href="#" target="_blank" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-file-pdf"></i>
                    <span>Nota Dinas</span>
                </a>
                <a id="download-surat-tugas" href="#" target="_blank" class="flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <i class="fas fa-file-pdf"></i>
                    <span>Surat Tugas</span>
                </a>
                <a id="download-spd" href="#" target="_blank" class="flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    <i class="fas fa-file-pdf"></i>
                    <span>SPD</span>
                </a>
            </div>
            <div class="mt-3">
                <a id="download-all" href="#" class="flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                    <i class="fas fa-file-archive"></i>
                    <span>Download Semua (ZIP)</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview PDF -->
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
let currentDetailId = null;

$(function() {
    var table = $('#kd-sppd-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('kepaladinas/sppd/datatable') ?>',
            type: 'POST',
            data: function(d) {
                d.filters = {
                    status: $('#filter_status').val()
                };
            }
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
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']],
        createdRow: function(row, data, dataIndex) {
            var info = table.page.info();
            $('td', row).eq(0).html(dataIndex + 1 + (info.page * info.length));
        }
    });

    // Filter Status
    $('#filter_status').on('change', function() {
        table.ajax.reload();
    });

    // Detail
    $('#kd-sppd-table').on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        currentDetailId = id;
        
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
                
                // Show download section if approved
                if (sppd.status === 'approved') {
                    $('#download-nota-dinas').attr('href', '<?= site_url('kepaladinas/sppd/download-nota-dinas') ?>/' + id);
                    $('#download-surat-tugas').attr('href', '<?= site_url('kepaladinas/sppd/download-surat-tugas') ?>/' + id);
                    $('#download-spd').attr('href', '<?= site_url('kepaladinas/sppd/download-spd') ?>/' + id);
                    $('#download-all').attr('href', '<?= site_url('kepaladinas/sppd/download-all') ?>/' + id);
                    $('#detail-download-section').removeClass('hidden');
                } else {
                    $('#detail-download-section').addClass('hidden');
                }
                
                $('#modal-detail-sppd').removeClass('hidden');
            } else {
                Swal.fire('Error', res.message || 'Gagal memuat detail', 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat detail SPPD', 'error');
        });
    });

    // Preview PDF
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
            html: '<p class="mb-2">Dengan menyetujui, sistem akan otomatis menggenerate:</p>' +
                  '<ul class="list-disc list-inside text-left text-sm">' +
                  '<li>Nomor SPPD</li>' +
                  '<li>Nota Dinas</li>' +
                  '<li>Surat Tugas</li>' +
                  '<li>Surat Perjalanan Dinas</li>' +
                  '</ul>',
            input: 'textarea',
            inputPlaceholder: 'Catatan (opsional)',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
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
                var docs = result.value.data?.documents;
                var message = 'SPPD disetujui dengan nomor: ' + result.value.data?.no_sppd;
                
                if (docs) {
                    message += '<br><br><small class="text-sm">Dokumen berhasil digenerate:</small>';
                    message += '<ul class="list-disc list-inside text-left text-sm mt-2">';
                    if (docs.nota_dinas) message += '<li>Nota Dinas</li>';
                    if (docs.surat_tugas) message += '<li>Surat Tugas</li>';
                    if (docs.spd) message += '<li>Surat Perjalanan Dinas</li>';
                    message += '</ul>';
                }
                
                Swal.fire({
                    title: 'Berhasil!',
                    html: message,
                    icon: 'success'
                });
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
            cancelButtonText: 'Batal',
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

    // Regenerate Documents
    $('#kd-sppd-table').on('click', '.btn-regenerate', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Regenerate Dokumen?',
            text: 'Dokumen lama akan ditimpa dengan dokumen baru',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Regenerate',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f59e0b',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.post('<?= site_url('kepaladinas/sppd/regenerate') ?>/' + id)
                    .then(res => res)
                    .catch(err => {
                        Swal.showValidationMessage('Error: ' + (err.responseJSON?.message || err.statusText));
                    });
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value?.status) {
                Swal.fire('Berhasil', 'Dokumen berhasil digenerate ulang', 'success');
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
    currentDetailId = null;
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

// Dropdown toggle function (for inline download menu in table)
function toggleDropdown(id) {
    // Close all other dropdowns
    $('[id^="dropdown-"]').not('#dropdown-' + id).addClass('hidden');
    
    // Toggle current dropdown
    var dropdown = $('#dropdown-' + id);
    dropdown.toggleClass('hidden');
    
    // Close dropdown when clicking outside
    if (!dropdown.hasClass('hidden')) {
        $(document).one('click', function(e) {
            if (!$(e.target).closest('#dropdown-' + id).length && 
                !$(e.target).closest('button[onclick*="toggleDropdown(' + id + ')"]').length) {
                dropdown.addClass('hidden');
            }
        });
    }
}
</script>
<?= $this->endSection() ?>