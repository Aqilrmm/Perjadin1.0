<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Keuangan</h1>
    <p class="text-gray-600 mt-1">Monitoring verifikasi dan pencairan SPPD</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Pending Verification -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Menunggu Verifikasi</p>
                <h3 class="text-3xl font-bold text-gray-800"><?= $statistics['pending_verification'] ?? 0 ?></h3>
                <p class="text-xs text-gray-500 mt-2">
                    <a href="<?= base_url('keuangan/verifikasi') ?>" class="text-yellow-600 hover:text-yellow-700 font-medium">
                        Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-4">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <!-- Verified This Month -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Diverifikasi Bulan Ini</p>
                <h3 class="text-3xl font-bold text-gray-800"><?= $statistics['verified_this_month'] ?? 0 ?></h3>
                <p class="text-xs text-gray-500 mt-2">Bulan <?= date('F Y') ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Pencairan -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Pencairan Bulan Ini</p>
                <h3 class="text-2xl font-bold text-gray-800"><?= format_rupiah($statistics['total_pencairan_month'] ?? 0) ?></h3>
                <p class="text-xs text-gray-500 mt-2">Bulan <?= date('F Y') ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-money-bill-wave text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Need Revision -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Perlu Revisi</p>
                <h3 class="text-3xl font-bold text-gray-800"><?= $statistics['need_revision'] ?? 0 ?></h3>
                <p class="text-xs text-gray-500 mt-2">Dikembalikan untuk perbaikan</p>
            </div>
            <div class="bg-red-100 rounded-full p-4">
                <i class="fas fa-undo text-2xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Urgent SPPD Section -->
<?php if (!empty($urgent_sppd)): ?>
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            SPPD Urgent (Submit > 3 Hari)
        </h2>
        <span class="bg-red-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full">
            <?= count($urgent_sppd) ?> SPPD
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SPPD</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Submit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Tertunda</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($urgent_sppd as $sppd): ?>
                    <?php 
                        $submitted = new DateTime($sppd->submitted_at);
                        $now = new DateTime();
                        $diff = $now->diff($submitted);
                        $days = $diff->days;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900"><?= esc($sppd->no_sppd) ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm text-gray-600"><?= esc($sppd->nama_bidang) ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm text-gray-600"><?= format_tanggal_waktu($sppd->submitted_at) ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded">
                                <?= $days ?> hari
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="<?= base_url('keuangan/verifikasi/detail/' . $sppd->id) ?>" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Verifikasi <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
        <div>
            <h3 class="text-green-800 font-semibold">Tidak Ada SPPD Urgent</h3>
            <p class="text-green-600 text-sm">Semua SPPD diproses dengan baik</p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Verifikasi SPPD -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-semibold mb-2">Verifikasi SPPD</h3>
                <p class="text-blue-100 text-sm">Verifikasi LPPD dan Kwitansi yang sudah disubmit pegawai</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-check-double text-3xl"></i>
            </div>
        </div>
        <a href="<?= base_url('keuangan/verifikasi') ?>" 
           class="inline-flex items-center bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition">
            Mulai Verifikasi <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- Generate Laporan -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-semibold mb-2">Laporan Keuangan</h3>
                <p class="text-green-100 text-sm">Generate laporan pencairan dan realisasi anggaran</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-file-alt text-3xl"></i>
            </div>
        </div>
        <a href="<?= base_url('keuangan/laporan') ?>" 
           class="inline-flex items-center bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-green-50 transition">
            Buat Laporan <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto-refresh pending count every 30 seconds
    setInterval(function() {
        axios.get('<?= base_url('keuangan/verifikasi/getPendingCount') ?>')
            .then(response => {
                if (response.data.success) {
                    // Update badge if exists
                    const badge = document.querySelector('#notif-badge');
                    if (badge && response.data.data.count > 0) {
                        badge.classList.remove('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Failed to fetch pending count:', error);
            });
    }, 30000);
</script>
<?= $this->endSection() ?>