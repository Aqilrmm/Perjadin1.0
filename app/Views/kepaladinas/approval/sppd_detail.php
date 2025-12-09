<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= site_url('kepaladinas/sppd/approval') ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar SPPD
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h1 class="text-xl font-semibold text-white">Detail SPPD</h1>
        <?php if ($sppd['no_sppd']): ?>
            <p class="text-blue-100 text-sm mt-1"><?= esc($sppd['no_sppd']) ?></p>
        <?php else: ?>
            <p class="text-blue-100 text-sm mt-1 italic">Nomor SPPD akan digenerate saat disetujui</p>
        <?php endif; ?>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Status Badge -->
        <div class="mb-6">
            <?= get_sppd_status_badge($sppd['status']) ?>
        </div>

        <!-- Informasi Umum -->
        <div class="mb-6 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Bidang</label>
                    <p class="text-gray-900"><?= esc($sppd['nama_bidang']) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipe Perjalanan</label>
                    <p class="text-gray-900"><?= esc($sppd['tipe_perjalanan']) ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Program</label>
                    <p class="text-gray-900"><?= esc($sppd['nama_program']) ?> (<?= esc($sppd['kode_program']) ?>)</p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Kegiatan</label>
                    <p class="text-gray-900"><?= esc($sppd['nama_kegiatan']) ?> (<?= esc($sppd['kode_kegiatan']) ?>)</p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Sub Kegiatan</label>
                    <p class="text-gray-900"><?= esc($sppd['nama_sub_kegiatan']) ?> (<?= esc($sppd['kode_sub_kegiatan']) ?>)</p>
                </div>
            </div>
        </div>

        <!-- Tujuan dan Waktu -->
        <div class="mb-6 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tujuan dan Waktu Perjalanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Tempat Berangkat</label>
                    <p class="text-gray-900"><?= esc($sppd['tempat_berangkat']) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tempat Tujuan</label>
                    <p class="text-gray-900"><?= esc($sppd['tempat_tujuan']) ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Maksud Perjalanan</label>
                    <p class="text-gray-900"><?= esc($sppd['maksud_perjalanan']) ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Dasar Surat</label>
                    <p class="text-gray-900"><?= esc($sppd['dasar_surat']) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tanggal Berangkat</label>
                    <p class="text-gray-900">
                        <?php if ($sppd['tanggal_berangkat'] && $sppd['tanggal_berangkat'] != '0000-00-00'): ?>
                            <?= format_tanggal($sppd['tanggal_berangkat']) ?>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tanggal Kembali</label>
                    <p class="text-gray-900">
                        <?php if ($sppd['tanggal_kembali'] && $sppd['tanggal_kembali'] != '0000-00-00'): ?>
                            <?= format_tanggal($sppd['tanggal_kembali']) ?>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Lama Perjalanan</label>
                    <p class="text-gray-900"><?= esc($sppd['lama_perjalanan']) ?> hari</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Alat Angkut</label>
                    <p class="text-gray-900"><?= esc($sppd['alat_angkut']) ?: '-' ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Penanggung Jawab</label>
                    <p class="text-gray-900"><?= esc($sppd['penanggung_jawab_nama']) ?></p>
                </div>
            </div>
        </div>

        <!-- Anggaran -->
        <div class="mb-6 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Anggaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Estimasi Biaya</label>
                    <p class="text-gray-900 font-semibold text-lg"><?= format_rupiah($sppd['estimasi_biaya']) ?></p>
                </div>
                <?php if ($sppd['realisasi_biaya']): ?>
                <div>
                    <label class="text-sm font-medium text-gray-700">Realisasi Biaya</label>
                    <p class="text-gray-900 font-semibold text-lg"><?= format_rupiah($sppd['realisasi_biaya']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Daftar Pegawai -->
        <div class="mb-6 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Daftar Pegawai (<?= count($pegawai_list) ?> orang)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP/NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($pegawai_list)): ?>
                            <?php foreach ($pegawai_list as $idx => $pegawai): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= $idx + 1 ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= get_nama_lengkap($pegawai) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($pegawai['nip_nik']) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"><?= esc($pegawai['jabatan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">
                                    Tidak ada data pegawai
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Catatan -->
        <?php if ($sppd['catatan_kepala_dinas']): ?>
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan Kepala Dinas</h2>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                <p class="text-sm text-gray-700"><?= nl2br(esc($sppd['catatan_kepala_dinas'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($sppd['catatan_keuangan']): ?>
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan Keuangan</h2>
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <p class="text-sm text-gray-700"><?= nl2br(esc($sppd['catatan_keuangan'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <?php if ($sppd['status'] == 'pending'): ?>
        <div class="flex gap-3 pt-6 border-t">
            <button onclick="handleApprove(<?= $sppd['id'] ?>)" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-check-circle mr-2"></i>
                Setujui SPPD
            </button>
            <button onclick="handleReject(<?= $sppd['id'] ?>)" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-times-circle mr-2"></i>
                Tolak SPPD
            </button>
            <button onclick="handlePreview(<?= $sppd['id'] ?>)" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>
                Preview Nota Dinas
            </button>
        </div>
        <?php endif; ?>

        <?php if ($sppd['status'] == 'approved'): ?>
        <div class="flex gap-3 pt-6 border-t">
            <a href="<?= site_url('kepaladinas/sppd/download-nota-dinas/' . $sppd['id']) ?>" target="_blank" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors inline-flex items-center">
                <i class="fas fa-download mr-2"></i>
                Download Nota Dinas
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function handleApprove(id) {
        Swal.fire({
            title: 'Setujui SPPD ini?',
            text: 'SPPD akan disetujui dan nomor SPPD akan digenerate otomatis',
            input: 'textarea',
            inputPlaceholder: 'Catatan persetujuan (opsional)',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (catatan) => {
                return $.post('<?= site_url('kepaladinas/sppd/approve') ?>/' + id, {
                    catatan: catatan
                }).then(function(res) {
                    return res;
                }).catch(function(err) {
                    Swal.showValidationMessage('Request failed: ' + (err.responseJSON?.message || err.statusText));
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function(result) {
            if (result.isConfirmed && result.value) {
                if (result.value.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'SPPD berhasil disetujui dengan nomor: ' + (result.value.data?.no_sppd || ''),
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = '<?= site_url('kepaladinas/sppd/approval') ?>';
                    });
                } else {
                    Swal.fire('Error', result.value.message || 'Gagal menyetujui SPPD', 'error');
                }
            }
        });
    }

    function handleReject(id) {
        Swal.fire({
            title: 'Tolak SPPD',
            text: 'Alasan penolakan wajib diisi',
            input: 'textarea',
            inputPlaceholder: 'Masukkan alasan minimal 10 karakter',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Tolak SPPD',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value || value.trim().length < 10) {
                    return 'Catatan penolakan minimal 10 karakter';
                }
            },
            showLoaderOnConfirm: true,
            preConfirm: (catatan) => {
                return $.post('<?= site_url('kepaladinas/sppd/reject') ?>/' + id, {
                    catatan: catatan
                }).then(function(res) {
                    return res;
                }).catch(function(err) {
                    Swal.showValidationMessage('Request failed: ' + (err.responseJSON?.message || err.statusText));
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function(result) {
            if (result.isConfirmed && result.value) {
                if (result.value.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.value.message || 'SPPD berhasil ditolak',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = '<?= site_url('kepaladinas/sppd/approval') ?>';
                    });
                } else {
                    Swal.fire('Error', result.value.message || 'Gagal menolak SPPD', 'error');
                }
            }
        });
    }

    function handlePreview(id) {
        // Open preview in new window
        window.open('<?= site_url('kepaladinas/sppd/preview') ?>/' + id, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
    }
</script>
<?= $this->endSection() ?>