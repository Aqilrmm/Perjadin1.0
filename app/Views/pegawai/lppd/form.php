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
    <h1 class="text-2xl font-bold text-gray-900">Laporan Pelaksanaan Perjalanan Dinas (LPPD)</h1>
    <p class="text-gray-600 mt-1">SPPD: <?= esc($sppd['no_sppd'] ?? $sppd['nomor_sppd'] ?? 'Draft') ?> - <?= esc($sppd['tempat_tujuan'] ?? '-') ?></p>
</div>

<!-- SPPD Info Card -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg" data-aos="fade-down">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-500 text-xl mt-0.5 mr-3"></i>
        <div>
            <p class="font-semibold text-blue-800">Informasi SPPD</p>
            <div class="text-sm text-blue-700 mt-2 space-y-1">
                <p><strong>Tujuan:</strong> <?= esc($sppd['tempat_tujuan'] ?? '-') ?></p>
                <p><strong>Tanggal:</strong> <?= format_tanggal($sppd['tanggal_berangkat']) ?> s/d <?= format_tanggal($sppd['tanggal_kembali']) ?></p>
                <p><strong>Lama Perjalanan:</strong> <?= $sppd['lama_perjalanan'] ?> hari</p>
            </div>
        </div>
    </div>
</div>

<!-- LPPD Form -->
<div class="bg-white rounded-lg shadow-sm" data-aos="fade-up">
    <form id="lppd-form" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="p-6 space-y-6">
            
            <!-- Hasil Kegiatan -->
            <div>
                <label for="hasil_kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Hasil Kegiatan <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="hasil_kegiatan" 
                    name="hasil_kegiatan" 
                    rows="6" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Jelaskan secara detail hasil/output dari perjalanan dinas ini... (minimal 50 karakter)"
                    required
                    minlength="50"><?= isset($lppd['hasil_kegiatan']) ? esc($lppd['hasil_kegiatan']) : '' ?></textarea>
                <div class="flex justify-between mt-1">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Minimal 50 karakter
                    </p>
                    <p id="hasil-counter" class="text-xs text-gray-500">0 karakter</p>
                </div>
            </div>

            <!-- Hambatan -->
            <div>
                <label for="hambatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Hambatan/Kendala (Opsional)
                </label>
                <textarea 
                    id="hambatan" 
                    name="hambatan" 
                    rows="4" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Jelaskan hambatan atau kendala yang dihadapi (jika ada)..."><?= isset($lppd['hambatan']) ? esc($lppd['hambatan']) : '' ?></textarea>
            </div>

            <!-- Saran -->
            <div>
                <label for="saran" class="block text-sm font-medium text-gray-700 mb-2">
                    Saran/Rekomendasi (Opsional)
                </label>
                <textarea 
                    id="saran" 
                    name="saran" 
                    rows="4" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Berikan saran atau rekomendasi untuk perbaikan ke depan..."><?= isset($lppd['saran']) ? esc($lppd['saran']) : '' ?></textarea>
            </div>

            <!-- Dokumentasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Dokumentasi Foto <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                    <input 
                        type="file" 
                        id="dokumentasi" 
                        name="dokumentasi[]" 
                        multiple 
                        accept="image/*"
                        class="hidden"
                        <?= !isset($lppd) || !isset($lppd['is_submitted']) || !$lppd['is_submitted'] ? 'required' : '' ?>>
                    <label for="dokumentasi" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 font-medium">Klik untuk upload foto atau drag & drop</p>
                        <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG, JPEG | Max 5MB per file | Min 1 foto</p>
                    </label>
                </div>
                
                <!-- Preview Container -->
                <div id="preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden">
                    <!-- Previews will be added here -->
                </div>

                <!-- Existing Photos (if edit) -->
                <?php if (isset($lppd) && isset($lppd['dokumentasi']) && $lppd['dokumentasi']): ?>
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Foto yang sudah diupload:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php foreach (json_decode($lppd['dokumentasi']) as $foto): ?>
                                <div class="relative group">
                                    <img src="<?= base_url('uploads/dokumentasi_kegiatan/' . $foto) ?>" alt="Dokumentasi" class="w-full h-32 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                        <a href="<?= base_url('uploads/dokumentasi_kegiatan/' . $foto) ?>" target="_blank" class="text-white">
                                            <i class="fas fa-eye text-2xl"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info Box -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mt-0.5 mr-3"></i>
                    <div class="text-sm text-yellow-700">
                        <p class="font-semibold mb-1">Tips Pengisian LPPD:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Jelaskan hasil kegiatan secara detail dan jelas</li>
                            <li>Upload minimal 1 foto dokumentasi kegiatan</li>
                            <li>Jika ada hambatan, jelaskan beserta solusinya</li>
                            <li>Berikan saran yang konstruktif untuk perbaikan</li>
                        </ul>
                    </div>
                </div>
            </div>

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
                    <i class="fas fa-paper-plane mr-2"></i> Submit LPPD
                </button>
            </div>
        </div>

    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const sppdId = <?= $sppd['id'] ?>;
    let selectedFiles = [];

    // Character counter
    $('#hasil_kegiatan').on('input', function() {
        const length = $(this).val().length;
        $('#hasil-counter').text(length + ' karakter');
        
        if (length >= 50) {
            $('#hasil-counter').removeClass('text-red-500').addClass('text-green-600');
        } else {
            $('#hasil-counter').removeClass('text-green-600').addClass('text-red-500');
        }
    });

    // Trigger initial count
    $('#hasil_kegiatan').trigger('input');

    // File input change handler
    $('#dokumentasi').on('change', function(e) {
        const files = Array.from(e.target.files);
        selectedFiles = files;
        displayPreviews(files);
    });

    function displayPreviews(files) {
        const container = $('#preview-container');
        container.empty();

        if (files.length === 0) {
            container.addClass('hidden');
            return;
        }

        container.removeClass('hidden');

        files.forEach((file, index) => {
            if (file.size > 5 * 1024 * 1024) {
                showToast('File ' + file.name + ' terlalu besar (max 5MB)', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`
                    <div class="relative group">
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                        <button type="button" class="absolute top-2 right-2 w-6 h-6 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" data-index="${index}">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                        <div class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                            ${(file.size / 1024 / 1024).toFixed(2)} MB
                        </div>
                    </div>
                `);
                container.append(preview);
            };
            reader.readAsDataURL(file);
        });
    }

    // Remove preview
    $(document).on('click', '#preview-container button', function() {
        const index = $(this).data('index');
        selectedFiles.splice(index, 1);
        
        // Update file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        document.getElementById('dokumentasi').files = dt.files;
        
        displayPreviews(selectedFiles);
    });

    // Save Draft
    $('#btn-save-draft').on('click', function() {
        saveLPPD(false);
    });

    // Submit Form
    $('#lppd-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate
        const hasilKegiatan = $('#hasil_kegiatan').val().trim();
        if (hasilKegiatan.length < 50) {
            showToast('Hasil kegiatan minimal 50 karakter', 'error');
            return;
        }

        const files = $('#dokumentasi')[0].files;
        const existingPhotos = <?= isset($lppd) && isset($lppd['dokumentasi']) && $lppd['dokumentasi'] ? count(json_decode($lppd['dokumentasi'])) : 0 ?>;
        
        if (files.length === 0 && existingPhotos === 0) {
            showToast('Upload minimal 1 foto dokumentasi', 'error');
            return;
        }

        saveLPPD(false);
    });

    function saveLPPD(isSubmit) {
        const formData = new FormData($('#lppd-form')[0]);
        const endpoint = isSubmit ? 
            `<?= base_url('pegawai/lppd/submit/') ?>${sppdId}` : 
            `<?= base_url('pegawai/lppd/save/') ?>${sppdId}`;
        
        const btnText = isSubmit ? 'Submitting...' : 'Saving...';
        const $btn = isSubmit ? $('#btn-submit') : $('#btn-save-draft');
        
        $btn.prop('disabled', true).addClass('opacity-50');
        
        // Show loading using SweetAlert2
        Swal.fire({
            title: btnText,
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: endpoint,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $btn.prop('disabled', false).removeClass('opacity-50');
                
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (isSubmit) {
                            window.location.href = '<?= base_url('pegawai/sppd/detail/') ?>' + sppdId;
                        } else {
                            // Reload form to show saved data
                            location.reload();
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
                $btn.prop('disabled', false).removeClass('opacity-50');
                
                let errorMsg = 'Terjadi kesalahan sistem';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg,
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Helper function for toast (if showToast not defined globally)
    if (typeof showToast === 'undefined') {
        window.showToast = function(message, type) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type === 'error' ? 'error' : 'success',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        };
    }
});
</script>
<?= $this->endSection() ?>