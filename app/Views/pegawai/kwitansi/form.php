<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Back Button -->
<div class="mb-4">
    <a href="<?= base_url('pegawai/sppd/detail/' . $sppd['id']) ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail SPPD
    </a>
</div>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Form Kwitansi Biaya Perjalanan</h1>
    <p class="text-gray-600 mt-1">SPPD: <?= esc($sppd['nomor_sppd'] ?? 'Draft') ?> - <?= esc($sppd['tujuan']) ?></p>
</div>

<!-- Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column - Form -->
    <div class="lg:col-span-2">
        
        <!-- SPPD Info -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg" data-aos="fade-down">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5 mr-3"></i>
                <div class="flex-1">
                    <p class="font-semibold text-blue-800">Informasi SPPD</p>
                    <div class="text-sm text-blue-700 mt-2 grid grid-cols-2 gap-2">
                        <div><strong>Tujuan:</strong> <?= esc($sppd['tujuan']) ?></div>
                        <div><strong>Tipe:</strong> <?= get_tipe_perjalanan_text($sppd['tipe_perjalanan']) ?></div>
                        <div><strong>Lama:</strong> <?= $sppd['lama_perjalanan'] ?> hari</div>
                        <div><strong>Estimasi:</strong> <?= format_rupiah($sppd['estimasi_biaya']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kwitansi Form -->
        <div class="bg-white rounded-lg shadow-sm" data-aos="fade-up">
            <form id="kwitansi-form" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="p-6 space-y-6">
                    
                    <!-- Biaya Perjalanan -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-plane text-blue-600"></i>
                            Biaya Perjalanan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nominal Biaya
                                </label>
                                <input 
                                    type="text" 
                                    id="biaya_perjalanan" 
                                    name="biaya_perjalanan" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Rp 0"
                                    value="<?= $kwitansi['biaya_perjalanan'] ?? '' ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <textarea 
                                    name="keterangan_perjalanan" 
                                    rows="2" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Transportasi PP Jakarta - Bandung"><?= $kwitansi['keterangan_perjalanan'] ?? '' ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Bukti (Opsional)
                                </label>
                                <input 
                                    type="file" 
                                    name="bukti_perjalanan" 
                                    accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF | Max 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Biaya Lumsum -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-coins text-yellow-600"></i>
                            Uang Harian (Lumsum)
                        </h3>
                        <div class="bg-yellow-50 p-3 rounded-lg mb-4">
                            <p class="text-sm text-yellow-800">
                                <strong>Tarif Lumsum:</strong>
                                <?php
                                $tarif = 0;
                                if ($sppd['tipe_perjalanan'] == 'dalam_daerah') {
                                    $tarif = $lumsum['lumsum_dalam_daerah'] ?? 0;
                                } elseif ($sppd['tipe_perjalanan'] == 'luar_daerah_dalam_provinsi') {
                                    $tarif = $lumsum['lumsum_luar_daerah_dalam_provinsi'] ?? 0;
                                } else {
                                    $tarif = $lumsum['lumsum_luar_daerah_luar_provinsi'] ?? 0;
                                }
                                ?>
                                <?= format_rupiah($tarif) ?> per hari × <?= $sppd['lama_perjalanan'] ?> hari = 
                                <strong><?= format_rupiah($tarif * $sppd['lama_perjalanan']) ?></strong>
                            </p>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nominal Biaya
                                </label>
                                <input 
                                    type="text" 
                                    id="biaya_lumsum" 
                                    name="biaya_lumsum" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Rp 0"
                                    value="<?= $kwitansi['biaya_lumsum'] ?? $tarif * $sppd['lama_perjalanan'] ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <textarea 
                                    name="keterangan_lumsum" 
                                    rows="2" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Keterangan uang harian..."><?= $kwitansi['keterangan_lumsum'] ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Biaya Penginapan (jika luar daerah) -->
                    <?php if ($sppd['tipe_perjalanan'] != 'dalam_daerah'): ?>
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-hotel text-purple-600"></i>
                            Biaya Penginapan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nominal Biaya
                                </label>
                                <input 
                                    type="text" 
                                    id="biaya_penginapan" 
                                    name="biaya_penginapan" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Rp 0"
                                    value="<?= $kwitansi['biaya_penginapan'] ?? '' ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <textarea 
                                    name="keterangan_penginapan" 
                                    rows="2" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nama hotel, jumlah malam, dll"><?= $kwitansi['keterangan_penginapan'] ?? '' ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Bukti <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="file" 
                                    name="bukti_penginapan" 
                                    accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="text-xs text-gray-500 mt-1">Wajib upload bukti penginapan</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Biaya Taxi (jika luar daerah luar provinsi) -->
                    <?php if ($sppd['tipe_perjalanan'] == 'luar_daerah_luar_provinsi'): ?>
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-taxi text-green-600"></i>
                            Biaya Transportasi Lokal (Taxi)
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nominal Biaya
                                </label>
                                <input 
                                    type="text" 
                                    id="biaya_taxi" 
                                    name="biaya_taxi" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Rp 0"
                                    value="<?= $kwitansi['biaya_taxi'] ?? '' ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <textarea 
                                    name="keterangan_taxi" 
                                    rows="2" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Detail transportasi lokal..."><?= $kwitansi['keterangan_taxi'] ?? '' ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Bukti (Opsional)
                                </label>
                                <input 
                                    type="file" 
                                    name="bukti_taxi" 
                                    accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            </div>
                        </div>
                    </div>

                    <!-- Biaya Tiket -->
                    <div class="pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-ticket-alt text-red-600"></i>
                            Biaya Tiket Perjalanan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nominal Biaya
                                </label>
                                <input 
                                    type="text" 
                                    id="biaya_tiket" 
                                    name="biaya_tiket" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Rp 0"
                                    value="<?= $kwitansi['biaya_tiket'] ?? '' ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan
                                </label>
                                <textarea 
                                    name="keterangan_tiket" 
                                    rows="2" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Jenis tiket (pesawat, kereta, bus), rute, dll"><?= $kwitansi['keterangan_tiket'] ?? '' ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Bukti <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="file" 
                                    name="bukti_tiket" 
                                    accept="image/*,application/pdf"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                <p class="text-xs text-gray-500 mt-1">Wajib upload bukti tiket</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
                    <a href="<?= base_url('pegawai/sppd/detail/' . $sppd['id']) ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                    <div class="flex gap-3">
                        <button 
                            type="button" 
                            id="btn-save-draft" 
                            class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg font-medium hover:bg-blue-50">
                            <i class="fas fa-save mr-2"></i> Simpan Draft
                        </button>
                        <button 
                            type="submit" 
                            id="btn-submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Kwitansi
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>

    <!-- Right Column - Summary -->
    <div>
        
        <!-- Total Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6" data-aos="fade-up">
            <h3 class="font-semibold text-gray-900 mb-4">Ringkasan Biaya</h3>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Biaya Perjalanan:</span>
                    <span id="summary-perjalanan" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Uang Harian:</span>
                    <span id="summary-lumsum" class="font-medium">Rp 0</span>
                </div>
                <?php if ($sppd['tipe_perjalanan'] != 'dalam_daerah'): ?>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Penginapan:</span>
                    <span id="summary-penginapan" class="font-medium">Rp 0</span>
                </div>
                <?php endif; ?>
                <?php if ($sppd['tipe_perjalanan'] == 'luar_daerah_luar_provinsi'): ?>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Transportasi Lokal:</span>
                    <span id="summary-taxi" class="font-medium">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tiket:</span>
                    <span id="summary-tiket" class="font-medium">Rp 0</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-900">Total Biaya:</span>
                    <span id="summary-total" class="text-2xl font-bold text-blue-600">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Estimasi:</span>
                    <span class="font-medium"><?= format_rupiah($sppd['estimasi_biaya']) ?></span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-gray-600">Selisih:</span>
                    <span id="summary-selisih" class="font-medium">-</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span>Penggunaan Anggaran</span>
                    <span id="percentage">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script>
$(document).ready(function() {
    const sppdId = <?= $sppd['id'] ?>;
    const estimasiBiaya = <?= $sppd['estimasi_biaya'] ?>;

    // Initialize Cleave.js for currency formatting
    const biayaFields = ['biaya_perjalanan', 'biaya_lumsum', 'biaya_penginapan', 'biaya_taxi', 'biaya_tiket'];
    const cleaveInstances = {};

    biayaFields.forEach(field => {
        const el = document.getElementById(field);
        if (el) {
            cleaveInstances[field] = new Cleave('#' + field, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                prefix: 'Rp ',
                rawValueTrimPrefix: true,
                onValueChanged: updateSummary
            });
        }
    });

    function updateSummary() {
        let total = 0;

        biayaFields.forEach(field => {
            if (cleaveInstances[field]) {
                const value = parseInt(cleaveInstances[field].getRawValue()) || 0;
                total += value;
                $(`#summary-${field.replace('biaya_', '')}`).text(formatRupiah(value));
            }
        });

        $('#summary-total').text(formatRupiah(total));

        // Calculate difference
        const selisih = estimasiBiaya - total;
        const selisihEl = $('#summary-selisih');
        
        if (selisih >= 0) {
            selisihEl.text(formatRupiah(selisih) + ' (Hemat)').removeClass('text-red-600').addClass('text-green-600');
        } else {
            selisihEl.text(formatRupiah(Math.abs(selisih)) + ' (Lebih)').removeClass('text-green-600').addClass('text-red-600');
        }

        // Update progress bar
        const percentage = Math.min((total / estimasiBiaya) * 100, 100);
        $('#percentage').text(percentage.toFixed(0) + '%');
        $('#progress-bar').css('width', percentage + '%');

        if (percentage > 100) {
            $('#progress-bar').removeClass('bg-blue-600').addClass('bg-red-600');
        } else if (percentage > 90) {
            $('#progress-bar').removeClass('bg-blue-600').addClass('bg-yellow-600');
        } else {
            $('#progress-bar').removeClass('bg-red-600 bg-yellow-600').addClass('bg-blue-600');
        }
    }

    // Initial update
    updateSummary();

    // Save Draft
    $('#btn-save-draft').on('click', function() {
        saveKwitansi(false);
    });

    // Submit Form
    $('#kwitansi-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validation
        const total = parseInt($('#summary-total').text().replace(/[^0-9]/g, '')) || 0;
        
        if (total === 0) {
            showToast('Minimal isi 1 komponen biaya', 'error');
            return;
        }

        if (total > estimasiBiaya) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Total biaya melebihi estimasi. Yakin ingin melanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveKwitansi(true);
                }
            });
        } else {
            saveKwitansi(true);
        }
    });

    function saveKwitansi(isSubmit) {
        const formData = new FormData($('#kwitansi-form')[0]);
        
        // Add raw values
        biayaFields.forEach(field => {
            if (cleaveInstances[field]) {
                formData.set(field, cleaveInstances[field].getRawValue());
            }
        });

        const endpoint = isSubmit ? 
            `<?= base_url('pegawai/kwitansi/submit/') ?>${sppdId}` : 
            `<?= base_url('pegawai/kwitansi/save/') ?>${sppdId}`;
        
        const $btn = isSubmit ? $('#btn-submit') : $('#btn-save-draft');
        $btn.prop('disabled', true).addClass('btn-loading');
        showLoading(isSubmit ? 'Submitting...' : 'Saving...');

        $.ajax({
            url: endpoint,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                $btn.prop('disabled', false).removeClass('btn-loading');
                
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (isSubmit) {
                            window.location.href = '<?= base_url('pegawai/sppd/detail/') ?>' + sppdId;
                        } else {
                            showToast('Kwitansi berhasil disimpan', 'success');
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                hideLoading();
                $btn.prop('disabled', false).removeClass('btn-loading');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>