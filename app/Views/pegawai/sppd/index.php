<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">SPPD Saya</h1>
    <p class="text-gray-600 mt-1">Daftar semua perjalanan dinas yang melibatkan Anda</p>
</div>

<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6" data-aos="fade-up">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="filter-status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="submitted">Submitted</option>
                <option value="verified">Verified</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
            <input type="text" id="filter-periode" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Pilih periode">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
            <input type="text" id="filter-tujuan" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Cari tujuan...">
        </div>
        <div class="flex items-end">
            <button id="btn-reset-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                <i class="fas fa-sync-alt mr-2"></i> Reset
            </button>
        </div>
    </div>
</div>

<!-- SPPD Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="100">
    <div class="overflow-x-auto">
        <table id="sppd-table" class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SPPD</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Data akan di-load via DataTables -->
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize Flatpickr for date range
    flatpickr('#filter-periode', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id'
    });

    // Initialize DataTable
    const table = $('#sppd-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('pegawai/sppd/datatable') ?>',
            type: 'POST',
            data: function(d) {
                d.filters = {
                    status: $('#filter-status').val(),
                    periode: $('#filter-periode').val(),
                    tujuan: $('#filter-tujuan').val()
                };
            }
        },
        columns: [
            { 
                data: 'nomor_sppd',
                render: function(data, type, row) {
                    return `<span class="font-mono text-sm">${data || '-'}</span>`;
                }
            },
            { 
                data: 'tujuan',
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="font-medium text-gray-900">${data}</div>
                            <div class="text-sm text-gray-500">${row.keperluan}</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'tanggal_formatted',
                render: function(data, type, row) {
                    return `
                        <div class="text-sm">
                            <div class="text-gray-900">${data}</div>
                            <div class="text-gray-500">${row.tanggal_kembali ? 's/d ' + moment(row.tanggal_kembali).format('DD MMM YYYY') : ''}</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'lama_perjalanan',
                render: function(data) {
                    return `<span class="text-sm text-gray-900">${data} hari</span>`;
                }
            },
            { 
                data: 'tipe_badge',
                orderable: false
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
        order: [[2, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        responsive: true
    });

    // Filter handlers
    $('#filter-status, #filter-periode, #filter-tujuan').on('change keyup', _.debounce(function() {
        table.ajax.reload();
    }, 500));

    $('#btn-reset-filter').on('click', function() {
        $('#filter-status').val('');
        $('#filter-periode').val('');
        $('#filter-tujuan').val('');
        table.ajax.reload();
    });

    // Detail button handler
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        window.location.href = `<?= base_url('pegawai/sppd/detail/') ?>${id}`;
    });

    // Download Nota Dinas handler
    $(document).on('click', '.btn-nota', function() {
        const id = $(this).data('id');
        window.open(`<?= base_url('kepaladinas/sppd/download-nota-dinas/') ?>${id}`, '_blank');
    });

    // LPPD button handler
    $(document).on('click', '.btn-lppd', function() {
        const id = $(this).data('id');
        window.location.href = `<?= base_url('pegawai/lppd/form/') ?>${id}`;
    });
});
</script>
<?= $this->endSection() ?>