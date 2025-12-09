<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex items-center">
            <a href="<?= base_url('kepalabidang/sppd') ?>" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail SPPD</h1>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap Surat Perjalanan Dinas</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <?php if ($sppd['status'] == 'approved'): ?>
                <button id="btn-download-nota" class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Download Nota Dinas
                </button>
            <?php endif; ?>
            
            <?php if ($sppd['status'] == 'draft'): ?>
                <button id="btn-edit-sppd" class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <i class="fas fa-pencil-alt mr-2"></i>
                    Edit SPPD
                </button>
                <button id="btn-submit-sppd" class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan SPPD
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Status Banner -->
<div class="mb-6">
    <div class="bg-gradient-to-r <?= get_sppd_status_gradient($sppd['status']) ?> text-white rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas <?= get_sppd_status_icon($sppd['status']) ?> text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90">Status SPPD</p>
                    <p class="text-xl font-bold"><?= strtoupper($sppd['status']) ?></p>
                    <?php if ($sppd['submitted_at']): ?>
                        <p class="text-xs opacity-75 mt-1">Diajukan: <?= date('d M Y H:i', strtotime($sppd['submitted_at'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($sppd['no_sppd']): ?>
                <div class="text-right">
                    <p class="text-sm opacity-90">No. SPPD</p>
                    <p class="text-2xl font-bold"><?= $sppd['no_sppd'] ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Program & Kegiatan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-folder text-blue-600 mr-2"></i>
                    Program & Kegiatan
                </h3>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500">Program</label>
                        <p class="font-semibold text-gray-900"><?= $sppd['kode_program'] ?> - <?= $sppd['nama_program'] ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Kegiatan</label>
                        <p class="font-semibold text-gray-900"><?= $sppd['kode_kegiatan'] ?> - <?= $sppd['nama_kegiatan'] ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Sub Kegiatan</label>
                        <p class="font-semibold text-gray-900"><?= $sppd['kode_sub_kegiatan'] ?> - <?= $sppd['nama_sub_kegiatan'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Perjalanan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plane text-purple-600 mr-2"></i>
                    Detail Perjalanan
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500">Tipe Perjalanan</label>
                        <p class="font-semibold text-gray-900">
                            <?= get_tipe_perjalanan_badge($sppd['tipe_perjalanan']) ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Alat Angkut</label>
                        <p class="font-semibold text-gray-900"><?= $sppd['alat_angkut'] ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Tempat Berangkat</label>
                        <p class="font-semibold text-gray-900"><?= $sppd['tempat_berangkat'] ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Tempat Tujuan</label>
                        <p class="font-semibold text-gray-900"><?= $sppd['tempat_tujuan'] ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Tanggal Berangkat</label>
                        <p class="font-semibold text-gray-900"><?= format_tanggal($sppd['tanggal_berangkat'], false) ?></p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Tanggal Kembali</label>
                        <p class="font-semibold text-gray-900"><?= format_tanggal($sppd['tanggal_kembali'], false) ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500">Lama Perjalanan</label>
                        <p class="font-semibold text-gray-900">
                            <i class="fas fa-calendar-alt text-blue-600 mr-1"></i>
                            <?= $sppd['lama_perjalanan'] ?> Hari
                        </p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="text-xs text-gray-500">Maksud Perjalanan</label>
                    <p class="text-gray-900 leading-relaxed mt-1"><?= nl2br($sppd['maksud_perjalanan']) ?></p>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="text-xs text-gray-500">Dasar Surat</label>
                    <p class="font-semibold text-gray-900"><?= $sppd['dasar_surat'] ?></p>
                </div>

                <?php if ($sppd['file_surat_tugas']): ?>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="text-xs text-gray-500">Surat Tugas</label>
                    <a href="<?= base_url('uploads/surat_tugas/' . $sppd['file_surat_tugas']) ?>" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800 mt-1">
                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                        <span class="font-medium"><?= $sppd['file_surat_tugas'] ?></span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pegawai List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-users text-green-600 mr-2"></i>
                    Daftar Pegawai (<?= count($pegawai_list) ?> Orang)
                </h3>
            </div>
            <div class="p-4">
                <div class="mb-4 pb-4 border-b border-gray-100">
                    <label class="text-xs text-gray-500">Penanggung Jawab</label>
                    <div class="flex items-center mt-2">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900"><?= $sppd['penanggung_jawab_nama'] ?></p>
                            <p class="text-xs text-gray-500">Penanggung Jawab SPPD</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($pegawai_list as $index => $pegawai): ?>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-white font-bold text-sm"><?= $index + 1 ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate"><?= $pegawai->nama ?></p>
                            <p class="text-xs text-gray-500">NIP: <?= $pegawai->nip ?></p>
                            <p class="text-xs text-gray-500"><?= $pegawai->jabatan ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <?php if ($sppd['catatan_kepala_dinas'] || $sppd['catatan_keuangan']): ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-comment-alt text-orange-600 mr-2"></i>
                    Catatan
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <?php if ($sppd['catatan_kepala_dinas']): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-semibold mb-1">Catatan Kepala Dinas</p>
                    <p class="text-sm text-gray-900"><?= nl2br($sppd['catatan_kepala_dinas']) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($sppd['catatan_keuangan']): ?>
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-xs text-green-600 font-semibold mb-1">Catatan Keuangan</p>
                    <p class="text-sm text-gray-900"><?= nl2br($sppd['catatan_keuangan']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="space-y-6">
        <!-- Biaya Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                    Informasi Biaya
                </h3>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Estimasi Biaya</span>
                        <span class="font-bold text-lg text-blue-600"><?= format_rupiah($sppd['estimasi_biaya']) ?></span>
                    </div>
                    
                    <?php if ($sppd['realisasi_biaya']): ?>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Realisasi Biaya</span>
                        <span class="font-bold text-lg text-green-600"><?= format_rupiah($sppd['realisasi_biaya']) ?></span>
                    </div>
                    
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Selisih</span>
                        <span class="font-bold text-lg <?= ($sppd['estimasi_biaya'] - $sppd['realisasi_biaya']) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= format_rupiah($sppd['estimasi_biaya'] - $sppd['realisasi_biaya']) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Timeline Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-2"></i>
                    Timeline
                </h3>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                            </div>
                            <div class="w-0.5 h-full bg-gray-200 my-1"></div>
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-xs text-gray-500"><?= date('d M Y H:i', strtotime($sppd['created_at'])) ?></p>
                            <p class="text-sm font-semibold text-gray-900">SPPD Dibuat</p>
                            <p class="text-xs text-gray-600">Oleh: <?= $sppd['created_by_nama'] ?></p>
                        </div>
                    </div>

                    <?php if ($sppd['submitted_at']): ?>
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-paper-plane text-yellow-600 text-xs"></i>
                            </div>
                            <div class="w-0.5 h-full bg-gray-200 my-1"></div>
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-xs text-gray-500"><?= date('d M Y H:i', strtotime($sppd['submitted_at'])) ?></p>
                            <p class="text-sm font-semibold text-gray-900">Diajukan</p>
                            <p class="text-xs text-gray-600">Menunggu persetujuan</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($sppd['approved_at_kepaladinas']): ?>
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-xs"></i>
                            </div>
                            <?php if ($sppd['verified_at_keuangan']): ?>
                            <div class="w-0.5 h-full bg-gray-200 my-1"></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-xs text-gray-500"><?= date('d M Y H:i', strtotime($sppd['approved_at_kepaladinas'])) ?></p>
                            <p class="text-sm font-semibold text-gray-900">Disetujui</p>
                            <p class="text-xs text-gray-600">Oleh Kepala Dinas</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($sppd['verified_at_keuangan']): ?>
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-double text-purple-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500"><?= date('d M Y H:i', strtotime($sppd['verified_at_keuangan'])) ?></p>
                            <p class="text-sm font-semibold text-gray-900">Diverifikasi</p>
                            <p class="text-xs text-gray-600">Oleh Bagian Keuangan</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-4 border border-blue-200">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Aksi Cepat</h4>
            <div class="space-y-2">
                <button class="w-full px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 border border-gray-200 flex items-center justify-center">
                    <i class="fas fa-print mr-2 text-gray-600"></i>
                    Cetak SPPD
                </button>
                <button class="w-full px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 border border-gray-200 flex items-center justify-center">
                    <i class="fas fa-share-alt mr-2 text-gray-600"></i>
                    Bagikan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const sppdId = <?= $sppd['id'] ?>;

    // Edit SPPD
    $('#btn-edit-sppd').on('click', function() {
        window.location.href = '<?= base_url('kepalabidang/sppd/edit/') ?>' + sppdId;
    });

    // Submit SPPD
    $('#btn-submit-sppd').on('click', function() {
        Swal.fire({
            title: 'Ajukan SPPD?',
            text: 'SPPD akan dikirim ke Kepala Dinas untuk disetujui',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3B82F6'
        }).then((result) => {
            if (result.isConfirmed) {
                submitSPPD();
            }
        });
    });

    // Download Nota
    $('#btn-download-nota').on('click', function() {
        window.open('<?= base_url('kepalabidang/sppd/nota-dinas/') ?>' + sppdId, '_blank');
    });

    function submitSPPD() {
        Swal.fire({
            title: 'Mengajukan SPPD...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('kepalabidang/sppd/submit/') ?>' + sppdId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>