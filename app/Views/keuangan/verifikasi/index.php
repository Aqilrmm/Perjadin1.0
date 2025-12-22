<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Verifikasi SPPD</h1>
        <p class="text-gray-600 mt-1">Verifikasi LPPD dan Kwitansi yang telah disubmit pegawai</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <button id="btn-refresh" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
        </button>
        <button id="btn-export" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-download mr-2"></i> Export
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="filter-status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="submitted" selected>Submitted</option>
                <option value="verified">Verified</option>
                <option value="need_revision">Need Revision</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bidang</label>
            <select id="filter-bidang" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Bidang</option>
                <!-- Will be populated via AJAX -->
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
            <input type="text" id="filter-periode" placeholder="Pilih Periode" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="flex items-end">
            <button id="btn-reset-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-redo mr-2"></i> Reset Filter
            </button>
        </div>
    </div>
</div>

<!-- Statistics Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600">Menunggu Verifikasi</p>
                <h3 id="stat-submitted" class="text-2xl font-bold text-gray-800">-</h3>
            </div>
            <i class="fas fa-clock text-2xl text-yellow-500"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600">Diverifikasi</p>
                <h3 id="stat-verified" class="text-2xl font-bold text-gray-800">-</h3>
            </div>
            <i class="fas fa-check-circle text-2xl text-green-500"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600">Perlu Revisi</p>
                <h3 id="stat-revision" class="text-2xl font-bold text-gray-800">-</h3>
            </div>
            <i class="fas fa-undo text-2xl text-red-500"></i>
        </div>
    </div>
</div>

<!-- DataTable -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table id="verifikasi-table" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SPPD</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Submit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Biaya</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Data will be loaded via DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize Flatpickr for date range
    flatpickr("#filter-periode", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: "id"
    });

    // Initialize Select2 for bidang filter
    $('#filter-bidang').select2({
        placeholder: 'Semua Bidang',
        allowClear: true,
        ajax: {
            url: '<?= base_url('api/bidang/options') ?>',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.id,
                        text: item.nama_bidang
                    }))
                };
            }
        }
    });

    // Initialize DataTable
    const table = $('#verifikasi-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('keuangan/verifikasi/datatable') ?>',
            type: 'POST',
            data: function(d) {
                d.filters = {
                    status: $('#filter-status').val(),
                    bidang_id: $('#filter-bidang').val(),
                    periode: $('#filter-periode').val()
                };
            }
        },
        columns: [
            { 
                data: 'no_sppd',
                render: function(data, type, row) {
                    return '<span class="font-medium text-gray-900">' + data + '</span>';
                }
            },
            { 
                data: 'nama_bidang',
                render: function(data) {
                    return '<span class="text-sm text-gray-600">' + data + '</span>';
                }
            },
            { 
                data: 'jumlah_pegawai',
                render: function(data) {
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' +
                           '<i class="fas fa-users mr-1"></i> ' + data + ' Orang' +
                           '</span>';
                }
            },
            { 
                data: 'submitted_at_formatted',
                render: function(data) {
                    return '<span class="text-sm text-gray-600">' + data + '</span>';
                }
            },
            { 
                data: 'estimasi_biaya_formatted',
                render: function(data) {
                    return '<span class="text-sm font-medium text-gray-900">' + data + '</span>';
                }
            },
            { 
                data: 'status_badge',
                orderable: false
            },
            { 
                data: 'action',
                orderable: false,
                searchable: false
            }
        ],
        order: [[3, 'desc']], // Sort by submit date descending
        language: {
            processing: '<i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>',
            emptyTable: 'Tidak ada data SPPD untuk diverifikasi',
            zeroRecords: 'Tidak ada data yang sesuai dengan filter'
        },
        drawCallback: function() {
            // Update statistics
            updateStatistics();
        }
    });

    // Refresh button
    $('#btn-refresh').on('click', function() {
        table.ajax.reload();
        showToast('Data berhasil direfresh', 'success');
    });

    // Reset filter
    $('#btn-reset-filter').on('click', function() {
        $('#filter-status').val('submitted');
        $('#filter-bidang').val(null).trigger('change');
        $('#filter-periode').val('');
        table.ajax.reload();
        showToast('Filter berhasil direset', 'info');
    });

    // Apply filters
    $('#filter-status, #filter-bidang, #filter-periode').on('change', function() {
        table.ajax.reload();
    });

    // Export button
    $('#btn-export').on('click', function() {
        const filters = {
            status: $('#filter-status').val(),
            bidang_id: $('#filter-bidang').val(),
            periode: $('#filter-periode').val()
        };
        
        window.location.href = '<?= base_url('api/datatable/export') ?>?type=verifikasi&format=excel&' + $.param(filters);
    });

    // Handle detail button
    $(document).on('click', '.btn-detail', function() {
        const sppdId = $(this).data('id');
        window.location.href = '<?= base_url('keuangan/verifikasi/detail/') ?>' + sppdId;
    });

    // Handle verify button
    $(document).on('click', '.btn-verify', function(e) {
        e.preventDefault();
        const sppdId = $(this).data('id');
        window.location.href = '<?= base_url('keuangan/verifikasi/detail/') ?>' + sppdId;
    });

    // Handle reject button
    $(document).on('click', '.btn-reject', function(e) {
        e.preventDefault();
        const sppdId = $(this).data('id');
        
        Swal.fire({
            title: 'Return SPPD untuk Revisi',
            html: '<textarea id="catatan-reject" class="swal2-textarea" placeholder="Catatan penolakan (minimal 20 karakter)" style="width: 100%; height: 120px;"></textarea>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Return',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const catatan = document.getElementById('catatan-reject').value;
                if (!catatan || catatan.length < 20) {
                    Swal.showValidationMessage('Catatan penolakan minimal 20 karakter');
                    return false;
                }
                return catatan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading('Memproses...');
                
                axios.post('<?= base_url('keuangan/verifikasi/reject/') ?>' + sppdId, {
                    catatan_penolakan: result.value
                })
                .then(response => {
                    hideLoading();
                    if (response.data.success) {
                        showToast(response.data.message, 'success');
                        table.ajax.reload();
                    } else {
                        showToast(response.data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast(error.response?.data?.message || 'Terjadi kesalahan', 'error');
                });
            }
        });
    });

    // Update statistics
    function updateStatistics() {
        axios.get('<?= base_url('api/datatable/filter-options') ?>?type=verifikasi_stats')
            .then(response => {
                if (response.data.success) {
                    const stats = response.data.data;
                    $('#stat-submitted').text(stats.submitted || 0);
                    $('#stat-verified').text(stats.verified || 0);
                    $('#stat-revision').text(stats.need_revision || 0);
                }
            });
    }

    // Initial statistics load
    updateStatistics();
});
</script>
<?= $this->endSection() ?>