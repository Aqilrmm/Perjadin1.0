<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Back Button -->
<div class="mb-4">
    <a href="<?= base_url('pegawai/sppd') ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar SPPD
    </a>
</div>

<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Detail SPPD</h1>
        <p class="text-gray-600 mt-1">Nomor: <span class="font-mono"><?= esc($sppd['nomor_sppd'] ?? '-') ?></span></p>
    </div>
    <div class="flex gap-2">
        <?php if ($sppd['status'] == 'approved'): ?>
            <a href="<?= base_url('kepaladinas/sppd/download-nota-dinas/' . $sppd['id']) ?>" target="_blank" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-file-pdf mr-2"></i> Nota Dinas
            </a>
        <?php endif; ?>
        
        <?php if ($sppd['status'] == 'approved' && strtotime(date('Y-m-d')) >= strtotime($sppd['tanggal_kembali'])): ?>
            <?php if (!$lppd || !$lppd['is_submitted']): ?>
                <a href="<?= base_url('pegawai/lppd/form/' . $sppd['id']) ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i> Isi LPPD
                </a>
            <?php endif; ?>
            
            <?php if ($lppd && $lppd['is_submitted'] && (!$kwitansi || !$kwitansi['is_submitted'])): ?>
                <a href="<?= base_url('pegawai/kwitansi/form/' . $sppd['id']) ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-receipt mr-2"></i> Isi Kwitansi
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Status Alert -->
<?php if ($sppd['status'] == 'rejected'): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg" data-aos="fade-down">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5 mr-3"></i>
            <div>
                <p class="font-semibold text-red-800">SPPD Ditolak</p>
                <p class="text-sm text-red-700 mt-1"><?= esc($sppd['alasan_reject'] ?? 'Tidak ada keterangan') ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column - SPPD Info -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Informasi Dasar -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Informasi Dasar
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Bidang</label>
                        <p class="text-gray-900 mt-1"><?= esc($sppd['nama_bidang'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1"><?= get_sppd_status_badge($sppd['status']) ?></div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tujuan Perjalanan</label>
                        <p class="text-gray-900 mt-1"><?= esc($sppd['tujuan']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tipe Perjalanan</label>
                        <div class="mt-1"><?= get_tipe_perjalanan_badge($sppd['tipe_perjalanan']) ?></div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">Keperluan</label>
                        <p class="text-gray-900 mt-1"><?= esc($sppd['keperluan']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Perjalanan -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Jadwal Perjalanan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tanggal Berangkat</label>
                        <p class="text-gray-900 mt-1 flex items-center gap-2">
                            <i class="fas fa-plane-departure text-green-600"></i>
                            <?= format_tanggal($sppd['tanggal_berangkat']) ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tanggal Kembali</label>
                        <p class="text-gray-900 mt-1 flex items-center gap-2">
                            <i class="fas fa-plane-arrival text-blue-600"></i>
                            <?= format_tanggal($sppd['tanggal_kembali']) ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Lama Perjalanan</label>
                        <p class="text-gray-900 mt-1 flex items-center gap-2">
                            <i class="fas fa-clock text-purple-600"></i>
                            <?= $sppd['lama_perjalanan'] ?> hari
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anggaran -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="200">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-coins"></i>
                    Informasi Anggaran
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Estimasi Biaya</label>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?= format_rupiah($sppd['estimasi_biaya']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Realisasi Biaya</label>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            <?= $sppd['realisasi_biaya'] ? format_rupiah($sppd['realisasi_biaya']) : '-' ?>
                        </p>
                    </div>
                    <?php if ($sppd['realisasi_biaya']): ?>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Selisih</label>
                            <?php 
                                $selisih = $sppd['estimasi_biaya'] - $sppd['realisasi_biaya'];
                                $warna = $selisih >= 0 ? 'text-green-600' : 'text-red-600';
                            ?>
                            <p class="text-xl font-bold <?= $warna ?> mt-1">
                                <?= format_rupiah(abs($selisih)) ?> <?= $selisih >= 0 ? '(Hemat)' : '(Lebih)' ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Peserta SPPD -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="300">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    Peserta Perjalanan Dinas
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php foreach ($pegawai_list as $pegawai): ?>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <img src="<?= base_url('uploads/photos/' . ($pegawai['foto'] ?? 'default-avatar.png')) ?>" 
                                 alt="<?= esc($pegawai['nama']) ?>" 
                                 class="w-12 h-12 rounded-full object-cover"
                                 onerror="this.src='<?= base_url('assets/images/default-avatar.png') ?>'">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900"><?= esc($pegawai['nama']) ?></p>
                                <p class="text-sm text-gray-600">NIP: <?= esc($pegawai['nip']) ?></p>
                            </div>
                            <?php if ($pegawai['id'] == user_id()): ?>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    Anda
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column - Status & Documents -->
    <div class="space-y-6">
        
        <!-- Status Progress -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-aos="fade-up">
            <div class="p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Status Progress</h3>
                <div class="space-y-4">
                    <!-- Created -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">SPPD Dibuat</p>
                            <p class="text-sm text-gray-500"><?= format_tanggal($sppd['created_at'], true) ?></p>
                        </div>
                    </div>

                    <!-- Approved/Rejected -->
                    <?php if ($sppd['status'] == 'approved' || $sppd['status'] == 'rejected'): ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 <?= $sppd['status'] == 'approved' ? 'bg-green-100' : 'bg-red-100' ?> rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas <?= $sppd['status'] == 'approved' ? 'fa-check' : 'fa-times' ?> <?= $sppd['status'] == 'approved' ? 'text-green-600' : 'text-red-600' ?>"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?= $sppd['status'] == 'approved' ? 'Disetujui' : 'Ditolak' ?></p>
                                <p class="text-sm text-gray-500"><?= $sppd['approved_at'] ? format_tanggal($sppd['approved_at'], true) : '-' ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-500">Menunggu Persetujuan</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- LPPD -->
                    <?php if ($lppd && $lppd['is_submitted']): ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">LPPD Disubmit</p>
                                <p class="text-sm text-gray-500"><?= format_tanggal($lppd['tanggal_pengisian'], true) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Kwitansi -->
                    <?php if ($kwitansi && $kwitansi['is_submitted']): ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Kwitansi Disubmit</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Verified -->
                    <?php if ($sppd['status'] == 'verified'): ?>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check-double text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Terverifikasi</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white" data-aos="fade-up" data-aos-delay="100">
            <h3 class="font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h3>
            <div class="space-y-2">
                <?php if ($sppd['status'] == 'approved'): ?>
                    <a href="<?= base_url('kepaladinas/sppd/download-nota-dinas/' . $sppd['id']) ?>" target="_blank" class="block w-full px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-center transition-colors">
                        <i class="fas fa-download mr-2"></i> Download Nota Dinas
                    </a>
                <?php endif; ?>
                
                <?php if ($lppd): ?>
                    <button onclick="viewLPPD()" class="block w-full px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-center transition-colors">
                        <i class="fas fa-file-alt mr-2"></i> Lihat LPPD
                    </button>
                <?php endif; ?>
                
                <?php if ($kwitansi): ?>
                    <button onclick="viewKwitansi()" class="block w-full px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-center transition-colors">
                        <i class="fas fa-receipt mr-2"></i> Lihat Kwitansi
                    </button>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<!-- LPPD Modal -->
<?php if ($lppd): ?>
<div id="lppd-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Laporan Pelaksanaan Perjalanan Dinas</h3>
            <button onclick="closeLPPD()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Hasil Kegiatan</label>
                    <p class="text-gray-900 mt-2 whitespace-pre-line"><?= esc($lppd['hasil_kegiatan']) ?></p>
                </div>
                <?php if ($lppd['hambatan']): ?>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Hambatan</label>
                        <p class="text-gray-900 mt-2 whitespace-pre-line"><?= esc($lppd['hambatan']) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($lppd['saran']): ?>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Saran</label>
                        <p class="text-gray-900 mt-2 whitespace-pre-line"><?= esc($lppd['saran']) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($lppd['dokumentasi']): ?>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Dokumentasi</label>
                        <div class="grid grid-cols-3 gap-3 mt-2">
                            <?php foreach (json_decode($lppd['dokumentasi']) as $foto): ?>
                                <a href="<?= base_url('uploads/dokumentasi_kegiatan/' . $foto) ?>" target="_blank" class="block">
                                    <img src="<?= base_url('uploads/dokumentasi_kegiatan/' . $foto) ?>" alt="Dokumentasi" class="w-full h-32 object-cover rounded-lg">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Kwitansi Modal -->
<?php if ($kwitansi): ?>
<div id="kwitansi-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Kwitansi Biaya Perjalanan</h3>
            <button onclick="closeKwitansi()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <?php if ($kwitansi['biaya_perjalanan']): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Biaya Perjalanan</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1"><?= format_rupiah($kwitansi['biaya_perjalanan']) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($kwitansi['biaya_lumsum']): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Biaya Lumsum</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1"><?= format_rupiah($kwitansi['biaya_lumsum']) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($kwitansi['biaya_penginapan']): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Biaya Penginapan</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1"><?= format_rupiah($kwitansi['biaya_penginapan']) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($kwitansi['biaya_taxi']): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Biaya Taxi</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1"><?= format_rupiah($kwitansi['biaya_taxi']) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($kwitansi['biaya_tiket']): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Biaya Tiket</label>
                            <p class="text-lg font-semibold text-gray-900 mt-1"><?= format_rupiah($kwitansi['biaya_tiket']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="border-t pt-4">
                    <label class="text-sm font-medium text-gray-500">Total Biaya</label>
                    <p class="text-2xl font-bold text-green-600 mt-1"><?= format_rupiah($kwitansi['total_biaya']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function viewLPPD() {
    document.getElementById('lppd-modal').classList.remove('hidden');
}

function closeLPPD() {
    document.getElementById('lppd-modal').classList.add('hidden');
}

function viewKwitansi() {
    document.getElementById('kwitansi-modal').classList.remove('hidden');
}

function closeKwitansi() {
    document.getElementById('kwitansi-modal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>