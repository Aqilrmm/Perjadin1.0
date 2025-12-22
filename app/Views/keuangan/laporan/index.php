<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>
    <p class="text-gray-600 mt-1">Generate laporan pencairan dan realisasi anggaran SPPD</p>
</div>

<!-- Filter & Generate Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left: Filter Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Parameter Laporan</h2>
            
            <form id="form-generate-laporan" method="post" action="<?= base_url('keuangan/laporan/generate') ?>">
                <?= csrf_field() ?>
                
                <!-- Periode -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="periode_start" 
                               id="periode_start"
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="periode_end" 
                               id="periode_end"
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Quick Periode Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Cepat Periode</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <button type="button" class="btn-quick-periode px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" data-periode="this_month">
                            Bulan Ini
                        </button>
                        <button type="button" class="btn-quick-periode px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" data-periode="last_month">
                            Bulan Lalu
                        </button>
                        <button type="button" class="btn-quick-periode px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" data-periode="this_quarter">
                            Kuartal Ini
                        </button>
                        <button type="button" class="btn-quick-periode px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" data-periode="this_year">
                            Tahun Ini
                        </button>
                    </div>
                </div>

                <!-- Bidang Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Bidang (Opsional - Kosongkan untuk semua bidang)
                    </label>
                    <select name="bidang_ids[]" 
                            id="bidang_ids" 
                            multiple
                            class="w-full border border-gray-300 rounded-lg">
                        <?php foreach ($bidang_list as $bidang): ?>
                            <option value="<?= $bidang['id'] ?>"><?= esc($bidang['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih satu atau lebih bidang, atau kosongkan untuk semua bidang</p>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status SPPD <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="verified">Verified (Sudah Diverifikasi)</option>
                        <option value="submitted">Submitted (Menunggu Verifikasi)</option>
                        <option value="approved">Approved (Disetujui Kepala Dinas)</option>
                        <option value="all">Semua Status</option>
                    </select>
                </div>

                <!-- Format Export -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Format Laporan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="format" value="pdf" checked class="mr-3 w-4 h-4 text-blue-600">
                            <div>
                                <i class="fas fa-file-pdf text-2xl text-red-600 mb-1"></i>
                                <p class="font-medium text-gray-900">PDF</p>
                                <p class="text-xs text-gray-500">Format dokumen</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="format" value="excel" class="mr-3 w-4 h-4 text-blue-600">
                            <div>
                                <i class="fas fa-file-excel text-2xl text-green-600 mb-1"></i>
                                <p class="font-medium text-gray-900">Excel</p>
                                <p class="text-xs text-gray-500">Format spreadsheet</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3">
                    <button type="button" 
                            id="btn-preview"
                            class="flex-1 px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 font-medium transition">
                        <i class="fas fa-eye mr-2"></i> Preview
                    </button>
                    <button type="submit" 
                            id="btn-generate"
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                        <i class="fas fa-download mr-2"></i> Generate & Download
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Information & History -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Informasi Laporan</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Laporan berdasarkan tanggal berangkat SPPD</li>
                        <li>• Status "Verified" untuk laporan pencairan</li>
                        <li>• Format PDF untuk dokumen formal</li>
                        <li>• Format Excel untuk analisis data</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Statistik Bulan Ini</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm text-gray-600">Total SPPD</span>
                    <span class="font-semibold text-gray-900" id="stat-total">-</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm text-gray-600">Diverifikasi</span>
                    <span class="font-semibold text-green-600" id="stat-verified">-</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm text-gray-600">Pending</span>
                    <span class="font-semibold text-yellow-600" id="stat-pending">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Pencairan</span>
                    <span class="font-semibold text-blue-600" id="stat-amount">-</span>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Laporan Terakhir</h3>
            <div class="space-y-3" id="recent-reports">
                <p class="text-sm text-gray-500 text-center py-4">Belum ada riwayat laporan</p>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Preview Laporan</h3>
            <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="preview-content" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <!-- Preview content will be loaded here -->
        </div>
        <div class="flex justify-end gap-3 p-4 border-t bg-gray-50">
            <button onclick="closePreview()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                Tutup
            </button>
            <button onclick="downloadFromPreview()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i> Download
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize Select2 for bidang
    $('#bidang_ids').select2({
        placeholder: 'Pilih bidang (kosongkan untuk semua)',
        allowClear: true,
        width: '100%'
    });

    // Quick periode selection
    $('.btn-quick-periode').on('click', function() {
        const periode = $(this).data('periode');
        const today = new Date();
        let start, end;

        switch(periode) {
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'this_quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                start = new Date(today.getFullYear(), quarter * 3, 1);
                end = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                break;
            case 'this_year':
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date(today.getFullYear(), 11, 31);
                break;
        }

        $('#periode_start').val(formatDate(start));
        $('#periode_end').val(formatDate(end));
    });

    // Form validation
    $('#form-generate-laporan').on('submit', function(e) {
        e.preventDefault();
        
        const start = new Date($('#periode_start').val());
        const end = new Date($('#periode_end').val());
        
        if (start > end) {
            showToast('Tanggal mulai tidak boleh lebih besar dari tanggal selesai', 'error');
            return;
        }
        
        // Show loading
        showLoading('Generating laporan...');
        
        // Submit form
        this.submit();
        
        // Note: hideLoading will be called when download starts
        setTimeout(() => hideLoading(), 3000);
    });

    // Preview button
    $('#btn-preview').on('click', function() {
        const formData = $('#form-generate-laporan').serialize();
        
        showLoading('Loading preview...');
        
        axios.post('<?= base_url('keuangan/laporan/preview') ?>', formData)
            .then(response => {
                hideLoading();
                if (response.data.success) {
                    $('#preview-content').html(response.data.data.html);
                    $('#preview-modal').removeClass('hidden');
                } else {
                    showToast(response.data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                showToast(error.response?.data?.message || 'Gagal load preview', 'error');
            });
    });

    // Load statistics
    loadStatistics();

    function loadStatistics() {
        axios.get('<?= base_url('keuangan/dashboard/statistics') ?>')
            .then(response => {
                if (response.data.success) {
                    const stats = response.data.data;
                    $('#stat-total').text(stats.total || 0);
                    $('#stat-verified').text(stats.verified || 0);
                    $('#stat-pending').text(stats.pending || 0);
                    $('#stat-amount').text(formatRupiah(stats.total_amount || 0));
                }
            })
            .catch(error => {
                console.error('Failed to load statistics:', error);
            });
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
});

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

function downloadFromPreview() {
    document.getElementById('form-generate-laporan').submit();
    closePreview();
}
</script>
<?= $this->endSection() ?>