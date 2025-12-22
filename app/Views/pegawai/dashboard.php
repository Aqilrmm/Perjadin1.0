<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Pegawai</h1>
    <p class="text-gray-600 mt-1">Selamat datang, <?= esc(user_name()) ?>! Kelola perjalanan dinas Anda di sini.</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total SPPD -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="0">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total SPPD</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistics['total_sppd'] ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-plane text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- SPPD Berjalan -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">SPPD Berjalan</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2"><?= $statistics['sppd_berjalan'] ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Butuh Action -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Butuh Action</p>
                <p class="text-3xl font-bold text-red-600 mt-2"><?= $statistics['need_action'] ?? 0 ?></p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Upcoming Trips -->
    <div class="bg-white rounded-lg shadow-sm" data-aos="fade-up">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-blue-600"></i>
                Perjalanan Mendatang
            </h2>
            <p class="text-sm text-gray-600 mt-1">SPPD dalam 30 hari ke depan</p>
        </div>
        <div class="p-6">
            <?php if (empty($upcoming_trips)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">Tidak ada perjalanan mendatang</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($upcoming_trips as $trip): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer" onclick="window.location.href='<?= base_url('pegawai/sppd/detail/' . $trip['id']) ?>'">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900"><?= esc($trip['tujuan']) ?></h3>
                                    <p class="text-sm text-gray-600 mt-1"><?= esc($trip['keperluan']) ?></p>
                                    <div class="flex items-center gap-4 mt-3">
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="far fa-calendar"></i>
                                            <?= date('d M Y', strtotime($trip['tanggal_berangkat'])) ?>
                                        </span>
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            <?= $trip['lama_perjalanan'] ?> hari
                                        </span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Approved
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Required -->
    <div class="bg-white rounded-lg shadow-sm" data-aos="fade-up" data-aos-delay="100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tasks text-red-600"></i>
                Butuh Action
            </h2>
            <p class="text-sm text-gray-600 mt-1">SPPD yang memerlukan tindak lanjut</p>
        </div>
        <div class="p-6">
            <?php if (empty($action_required)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">Semua SPPD sudah selesai!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($action_required as $sppd): ?>
                        <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900"><?= esc($sppd['tujuan']) ?></h3>
                                    <p class="text-sm text-gray-600 mt-1"><?= esc($sppd['keperluan']) ?></p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs text-gray-500">
                                            Kembali: <?= date('d M Y', strtotime($sppd['tanggal_kembali'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                    Butuh LPPD
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?= base_url('pegawai/lppd/form/' . $sppd['id']) ?>" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 text-center">
                                    <i class="fas fa-edit mr-1"></i> Isi LPPD
                                </a>
                                <a href="<?= base_url('pegawai/sppd/detail/' . $sppd['id']) ?>" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Quick Actions -->
<div class="mt-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white" data-aos="fade-up">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold">Lihat Semua SPPD Anda</h3>
            <p class="text-blue-100 mt-1">Kelola dan pantau status perjalanan dinas Anda</p>
        </div>
        <a href="<?= base_url('pegawai/sppd') ?>" class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
            Lihat SPPD <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<?= $this->endSection() ?>