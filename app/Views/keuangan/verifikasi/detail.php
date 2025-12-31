<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="<?= base_url('keuangan/verifikasi') ?>" 
           class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Verifikasi SPPD</h1>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-gray-600"><?= esc($sppd['no_sppd'] ?? '') ?></span>
        <span><?= get_sppd_status_badge($sppd['status'] ?? '') ?></span>
    </div>
</div>

<!-- Main Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Documents Preview (2/3 width) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Document Tabs -->
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" role="tablist">
                    <button class="tab-btn active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600" 
                            data-tab="sppd">
                        <i class="fas fa-file-alt mr-2"></i> SPPD
                    </button>
                    <button class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                            data-tab="nota">
                        <i class="fas fa-file-invoice mr-2"></i> Nota Dinas
                    </button>
                    <button class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                            data-tab="lppd">
                        <i class="fas fa-clipboard-check mr-2"></i> LPPD
                    </button>
                    <button class="tab-btn px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                            data-tab="kwitansi">
                        <i class="fas fa-receipt mr-2"></i> Kwitansi
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- SPPD Tab -->
                <div id="tab-sppd" class="tab-content">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">No SPPD</label>
                                <p class="text-gray-900 mt-1"><?= esc($sppd['no_sppd'] ?? '-') ?></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Bidang</label>
                                <p class="text-gray-900 mt-1"><?= esc($sppd['nama_bidang'] ?? '-') ?></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tipe Perjalanan</label>
                                <p class="text-gray-900 mt-1"><?= esc($sppd['tipe_perjalanan'] ?? '-') ?></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Lama Perjalanan</label>
                                <p class="text-gray-900 mt-1"><?= esc($sppd['lama_perjalanan'] ?? '0') ?> Hari</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tanggal Berangkat</label>
                                <p class="text-gray-900 mt-1"><?= format_tanggal($sppd['tanggal_berangkat'] ?? '') ?></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tanggal Kembali</label>
                                <p class="text-gray-900 mt-1"><?= format_tanggal($sppd['tanggal_kembali'] ?? '') ?></p>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Maksud Perjalanan</label>
                            <p class="text-gray-900 mt-1"><?= esc($sppd['maksud_perjalanan'] ?? '-') ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tempat Tujuan</label>
                            <p class="text-gray-900 mt-1"><?= esc($sppd['tempat_tujuan'] ?? '-') ?></p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Estimasi Biaya</label>
                            <p class="text-2xl font-bold text-blue-600 mt-1"><?= format_rupiah($sppd['estimasi_biaya'] ?? 0) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Nota Dinas Tab -->
                <div id="tab-nota" class="tab-content hidden">
                    <?php if (!empty($sppd['file_nota_dinas'])): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <iframe src="<?= base_url('uploads/nota_dinas/' . $sppd['file_nota_dinas']) ?>" 
                                    class="w-full h-[600px]"></iframe>
                        </div>
                        <div class="mt-4">
                            <a href="<?= base_url('kepaladinas/sppd/download-nota-dinas/' . $sppd['id']) ?>" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-download mr-2"></i> Download Nota Dinas
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-file-invoice text-4xl mb-3"></i>
                            <p>Nota Dinas belum tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- LPPD Tab -->
                <div id="tab-lppd" class="tab-content hidden">
                    <?php if (!empty($lppd)): ?>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Hasil Kegiatan</label>
                                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-900 whitespace-pre-wrap"><?= esc($lppd['hasil_kegiatan'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Hambatan</label>
                                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-900 whitespace-pre-wrap"><?= esc($lppd['hambatan'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Saran</label>
                                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-900 whitespace-pre-wrap"><?= esc($lppd['saran'] ?? '-') ?></p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 mb-3 block">Dokumentasi Foto</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <?php if (!empty($lppd['dokumentasi_foto'])): ?>
                                        <?php 
                                        $photos = json_decode($lppd['dokumentasi_foto'], true);
                                        foreach ($photos as $photo): 
                                        ?>
                                            <div class="relative group">
                                                <img src="<?= base_url('uploads/lppd/' . $photo) ?>" 
                                                     alt="Dokumentasi"
                                                     class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition"
                                                     onclick="viewImage(this.src)">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg transition flex items-center justify-center">
                                                    <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition"></i>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="col-span-full text-gray-500 text-center py-4">Tidak ada foto</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-clipboard-check text-4xl mb-3"></i>
                            <p>LPPD belum diisi</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Kwitansi Tab -->
                <div id="tab-kwitansi" class="tab-content hidden">
                    <?php if (!empty($kwitansi)): ?>
                        <div class="space-y-6">
                            <!-- Rincian Biaya -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Rincian Biaya</h3>
                                <div class="space-y-3">
                                    <?php if (isset($kwitansi['biaya_perjalanan']) && $kwitansi['biaya_perjalanan'] > 0): ?>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-700">Biaya Perjalanan</span>
                                        <span class="font-semibold text-gray-900"><?= format_rupiah($kwitansi['biaya_perjalanan']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($kwitansi['biaya_lumsum']) && $kwitansi['biaya_lumsum'] > 0): ?>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-700">Biaya Lumsum</span>
                                        <span class="font-semibold text-gray-900"><?= format_rupiah($kwitansi['biaya_lumsum']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($kwitansi['biaya_penginapan']) && $kwitansi['biaya_penginapan'] > 0): ?>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-700">Biaya Penginapan</span>
                                        <span class="font-semibold text-gray-900"><?= format_rupiah($kwitansi['biaya_penginapan']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($kwitansi['biaya_taxi']) && $kwitansi['biaya_taxi'] > 0): ?>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-700">Biaya Taxi</span>
                                        <span class="font-semibold text-gray-900"><?= format_rupiah($kwitansi['biaya_taxi']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($kwitansi['biaya_tiket']) && $kwitansi['biaya_tiket'] > 0): ?>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-700">Biaya Tiket</span>
                                        <span class="font-semibold text-gray-900"><?= format_rupiah($kwitansi['biaya_tiket']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="border-t-2 border-gray-300 pt-3 mt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold text-gray-800">Total Biaya</span>
                                            <span class="text-2xl font-bold text-blue-600"><?= format_rupiah($kwitansi['total_biaya'] ?? 0) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bukti Pengeluaran -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Bukti Pengeluaran</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php 
                                    $buktiFields = [
                                        'bukti_perjalanan' => 'Perjalanan',
                                        'bukti_penginapan' => 'Penginapan',
                                        'bukti_taxi' => 'Taxi',
                                        'bukti_tiket' => 'Tiket'
                                    ];
                                    foreach ($buktiFields as $field => $label):
                                        if (!empty($kwitansi[$field])):
                                    ?>
                                        <div class="border rounded-lg p-4">
                                            <label class="text-sm font-medium text-gray-700 mb-2 block">Bukti <?= $label ?></label>
                                            <?php if (pathinfo($kwitansi[$field], PATHINFO_EXTENSION) === 'pdf'): ?>
                                                <a href="<?= base_url('uploads/kwitansi/' . $kwitansi[$field]) ?>" 
                                                   target="_blank"
                                                   class="flex items-center justify-center h-32 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                                    <div class="text-center">
                                                        <i class="fas fa-file-pdf text-4xl text-red-600 mb-2"></i>
                                                        <p class="text-sm text-gray-600">Lihat PDF</p>
                                                    </div>
                                                </a>
                                            <?php else: ?>
                                                <img src="<?= base_url('uploads/kwitansi/' . $kwitansi[$field]) ?>" 
                                                     alt="Bukti <?= $label ?>"
                                                     class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-90 transition"
                                                     onclick="viewImage(this.src)">
                                            <?php endif; ?>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-3"></i>
                            <p>Kwitansi belum diisi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pegawai List -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Pegawai</h3>
            <div class="space-y-3">
                <?php foreach ($pegawai_list as $pegawai): ?>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <img src="<?= get_user_avatar($pegawai['user_id'] ?? 0) ?>" 
                         alt="Avatar"
                         class="w-10 h-10 rounded-full object-cover">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900"><?= esc($pegawai['nama'] ?? '-') ?></p>
                        <p class="text-sm text-gray-600"><?= esc($pegawai['nip'] ?? '-') ?></p>
                    </div>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                        <?= esc($pegawai['nama_bidang'] ?? '-') ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Verification Form (1/3 width) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Verifikasi
            </h3>

            <?php if (($sppd['status'] ?? '') === 'submitted'): ?>
            <form id="form-verification" method="post">
                <?= csrf_field() ?>

                <!-- Checklist -->
                <div class="space-y-3 mb-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="checklist[lppd_lengkap]" value="1" 
                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">LPPD lengkap dan sesuai (minimal 50 karakter hasil kegiatan)</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="checklist[dokumentasi_lengkap]" value="1" 
                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Dokumentasi foto lengkap (minimal 1 foto)</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="checklist[kwitansi_lengkap]" value="1" 
                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Kwitansi lengkap sesuai tipe perjalanan</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="checklist[bukti_valid]" value="1" 
                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Semua bukti pengeluaran valid dan jelas</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="checklist[jumlah_sesuai]" value="1" 
                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Jumlah biaya sesuai dengan peraturan</span>
                    </label>
                </div>

                <!-- Catatan Verifikasi -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Verifikasi (Opsional)
                    </label>
                    <textarea name="catatan_verifikasi" 
                              rows="4"
                              placeholder="Catatan tambahan untuk verifikasi..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="button" id="btn-approve" 
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                        <i class="fas fa-check-circle mr-2"></i> Verifikasi & Approve
                    </button>
                    <button type="button" id="btn-reject" 
                            class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                        <i class="fas fa-undo mr-2"></i> Return untuk Revisi
                    </button>
                    <a href="<?= base_url('keuangan/verifikasi') ?>" 
                       class="block w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-center transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
            <?php else: ?>
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3">
                    <i class="fas fa-info-circle text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-600 mb-2">SPPD ini sudah diverifikasi</p>
                <span><?= get_sppd_status_badge($sppd['status'] ?? '') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="image-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4">
    <div class="relative max-w-7xl w-full">
        <button onclick="closeImageModal()" 
                class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300 z-10">
            <i class="fas fa-times"></i>
        </button>
        <img id="modal-image" src="" alt="Preview" class="w-full h-auto max-h-[90vh] object-contain">
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Tab switching
    $('.tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        
        // Update button states
        $('.tab-btn').removeClass('active border-blue-500 text-blue-600')
                     .addClass('border-transparent text-gray-500');
        $(this).addClass('active border-blue-500 text-blue-600')
               .removeClass('border-transparent text-gray-500');
        
        // Update content
        $('.tab-content').addClass('hidden');
        $('#tab-' + tab).removeClass('hidden');
    });

    // Approve button
    $('#btn-approve').on('click', function() {
        const checkedCount = $('input[name^="checklist"]:checked').length;
        const totalChecks = $('input[name^="checklist"]').length;
        
        if (checkedCount < totalChecks) {
            Swal.fire({
                title: 'Checklist Belum Lengkap',
                text: 'Semua item checklist harus dicentang untuk verifikasi',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        Swal.fire({
            title: 'Verifikasi SPPD?',
            text: 'SPPD akan disetujui dan siap untuk pencairan',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Verifikasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitVerification('approve');
            }
        });
    });

    // Reject button
    $('#btn-reject').on('click', function() {
        Swal.fire({
            title: 'Return untuk Revisi',
            html: '<textarea id="catatan-reject" class="swal2-textarea" placeholder="Jelaskan detail masalah yang ditemukan (minimal 20 karakter)" style="width: 100%; height: 120px;"></textarea>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Return',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const catatan = document.getElementById('catatan-reject').value;
                if (!catatan || catatan.length < 20) {
                    Swal.showValidationMessage('Catatan penolakan minimal 20 karakter dan harus menjelaskan detail masalah');
                    return false;
                }
                return catatan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitVerification('reject', result.value);
            }
        });
    });

    function submitVerification(action, catatanReject = null) {
        showLoading('Memproses verifikasi...');
        
        const formData = new FormData($('#form-verification')[0]);
        if (catatanReject) {
            formData.append('catatan_penolakan', catatanReject);
        }
        
        const url = action === 'approve' 
            ? '<?= base_url('keuangan/verifikasi/approve/' . ($sppd['id'] ?? '')) ?>'
            : '<?= base_url('keuangan/verifikasi/reject/' . ($sppd['id'] ?? '')) ?>';
        
        axios.post(url, formData)
            .then(response => {
                hideLoading();
                if (response.data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '<?= base_url('keuangan/verifikasi') ?>';
                    });
                } else {
                    showToast(response.data.message, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                const message = error.response?.data?.message || 'Terjadi kesalahan saat verifikasi';
                showToast(message, 'error');
            });
    }
});

// Image viewer functions
function viewImage(src) {
    document.getElementById('modal-image').src = src;
    document.getElementById('image-modal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
<?= $this->endSection() ?>