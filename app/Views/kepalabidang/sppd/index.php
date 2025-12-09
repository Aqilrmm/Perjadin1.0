<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola SPPD</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola Surat Perjalanan Dinas (SPPD) untuk bidang Anda</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= base_url('kepalabidang/sppd/create') ?>" class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <i class="fas fa-plus mr-2"></i>
                Buat SPPD Baru
            </a>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Menunggu Persetujuan</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Perjalanan</label>
            <select id="filter-tipe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Tipe</option>
                <option value="Dalam Daerah">Dalam Daerah</option>
                <option value="Luar Daerah Dalam Provinsi">Luar Daerah Dalam Provinsi</option>
                <option value="Luar Daerah Luar Provinsi">Luar Daerah Luar Provinsi</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
            <input type="text" id="filter-periode" placeholder="Pilih periode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" readonly>
        </div>
        <div class="flex items-end">
            <button type="button" id="btn-reset-filter" class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-redo mr-2"></i>Reset Filter
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total SPPD</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-total">0</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Menunggu Approval</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1" id="stat-pending">0</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Disetujui</p>
                <p class="text-2xl font-bold text-green-600 mt-1" id="stat-approved">0</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Anggaran</p>
                <p class="text-lg font-bold text-gray-900 mt-1" id="stat-anggaran">Rp 0</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Daftar SPPD</h2>
    </div>
    <div class="p-4 overflow-x-auto">
        <table id="sppd-table" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SPPD</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let table;

    // Initialize Flatpickr for periode filter
    flatpickr('#filter-periode', {
        mode: 'range',
        dateFormat: 'd-m-Y',
        locale: 'id'
    });

    // Initialize DataTable
    function initDataTable() {
        table = $('#sppd-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('kepalabidang/sppd/datatable') ?>',
                type: 'POST',
                data: function(d) {
                    d.filters = {
                        status: $('#filter-status').val(),
                        tipe_perjalanan: $('#filter-tipe').val(),
                        periode: $('#filter-periode').val(),
                        bidang_id: '<?= user_bidang_id() ?>',
                        created_by: '<?= user_id() ?>'
                    };
                }
            },
            columns: [
                { 
                    data: 'no_sppd',
                    render: function(data, type, row) {
                        return data || '<span class="text-gray-400 italic">Draft</span>';
                    }
                },
                { 
                    data: 'nama_program',
                    render: function(data) {
                        return data ? '<div class="max-w-xs truncate" title="' + data + '">' + data + '</div>' : '-';
                    }
                },
                { 
                    data: 'tempat_tujuan',
                    render: function(data, type, row) {
                        return '<div class="flex flex-col">' +
                               '<span class="font-medium text-gray-900">' + data + '</span>' +
                               '<span class="text-xs text-gray-500">' + row.tempat_berangkat + '</span>' +
                               '</div>';
                    }
                },
                { 
                    data: 'tanggal_formatted',
                    render: function(data, type, row) {
                        return '<div class="flex flex-col">' +
                               '<span class="text-sm">' + data + '</span>' +
                               '<span class="text-xs text-gray-500">' + row.lama_perjalanan + ' hari</span>' +
                               '</div>';
                    }
                },
                { 
                    data: 'jumlah_pegawai',
                    className: 'text-center',
                    render: function(data) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' +
                               '<i class="fas fa-users mr-1"></i>' + data + ' pegawai' +
                               '</span>';
                    }
                },
                { 
                    data: 'tipe_badge',
                    className: 'text-center'
                },
                { 
                    data: 'estimasi_biaya_formatted',
                    className: 'text-right font-medium'
                },
                { 
                    data: 'status_badge',
                    className: 'text-center'
                },
                { 
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            order: [[0, 'desc']],
            language: {
                processing: '<div class="flex items-center justify-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</div>',
                emptyTable: '<div class="text-center py-4"><i class="fas fa-inbox text-gray-400 text-3xl mb-2"></i><p class="text-gray-500">Belum ada SPPD</p></div>',
                zeroRecords: '<div class="text-center py-4"><i class="fas fa-search text-gray-400 text-3xl mb-2"></i><p class="text-gray-500">Data tidak ditemukan</p></div>'
            },
            drawCallback: function() {
                loadStatistics();
            }
        });
    }

    initDataTable();

    // Filter change handlers
    $('#filter-status, #filter-tipe, #filter-periode').on('change', function() {
        table.ajax.reload();
    });

    // Reset filter
    $('#btn-reset-filter').on('click', function() {
        $('#filter-status, #filter-tipe').val('');
        $('#filter-periode').val('');
        table.ajax.reload();
    });

    // Load statistics
    function loadStatistics() {
        $.ajax({
            url: '<?= base_url('kepalabidang/dashboard/statistics') ?>',
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    const stats = response.data;
                    $('#stat-total').text(stats.total_sppd || 0);
                    $('#stat-pending').text(stats.pending || 0);
                    $('#stat-approved').text(stats.approved || 0);
                    $('#stat-anggaran').text(formatRupiah(stats.total_anggaran || 0));
                }
            }
        });
    }

    // Event delegation for action buttons
    $('#sppd-table').on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        window.location.href = '<?= base_url('kepalabidang/sppd/detail/') ?>' + id;
    });

    $('#sppd-table').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        window.location.href = '<?= base_url('kepalabidang/sppd/edit/') ?>' + id;
    });

    $('#sppd-table').on('click', '.btn-submit', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Ajukan SPPD?',
            text: 'SPPD akan dikirim ke Kepala Dinas untuk disetujui',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280'
        }).then((result) => {
            if (result.isConfirmed) {
                submitSPPD(id);
            }
        });
    });

    $('#sppd-table').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus SPPD?',
            text: 'Data SPPD akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteSPPD(id);
            }
        });
    });

    $('#sppd-table').on('click', '.btn-nota', function() {
        const id = $(this).data('id');
        window.open('<?= base_url('kepalabidang/sppd/nota-dinas/') ?>' + id, '_blank');
    });

    // Submit SPPD
    function submitSPPD(id) {
        Swal.fire({
            title: 'Mengajukan SPPD...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('kepalabidang/sppd/submit/') ?>' + id,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        table.ajax.reload();
                    });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan saat mengajukan SPPD', 'error');
            }
        });
    }

    // Delete SPPD
    function deleteSPPD(id) {
        Swal.fire({
            title: 'Menghapus SPPD...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('kepalabidang/sppd/delete/') ?>' + id,
            type: 'DELETE',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        table.ajax.reload();
                    });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus SPPD', 'error');
            }
        });
    }

    // Format Rupiah helper
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
});
</script>
<?= $this->endSection() ?>