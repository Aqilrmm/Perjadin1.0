<div class="step-container">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Review & Submit SPPD</h3>
    <p class="text-sm text-gray-600 mb-6">Periksa kembali semua data sebelum mengajukan SPPD</p>

    <form id="step5-form">
        <?= csrf_field() ?>
        
        <div class="space-y-5">
            <!-- No SPPD -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Nomor SPPD</p>
                        <div class="flex items-center">
                            <input type="text" name="no_sppd" id="no-sppd" class="bg-white text-gray-900 px-4 py-2 rounded-lg font-bold text-lg focus:ring-2 focus:ring-white" placeholder="AUTO-GENERATE" readonly>
                            <button type="button" id="btn-generate-no" class="ml-3 px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-sync-alt mr-1"></i>Generate
                            </button>
                        </div>
                        <p class="text-xs opacity-75 mt-2">*Nomor akan digenerate otomatis setelah disetujui atau bisa diisi manual</p>
                    </div>
                    <div class="text-right">
                        <i class="fas fa-file-alt text-6xl opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Review Cards -->
            <!-- 1. Program & Kegiatan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-folder text-blue-600"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Program & Kegiatan</h4>
                    </div>
                    <button type="button" class="btn-edit-step text-xs text-blue-600 hover:text-blue-800 font-medium" data-step="1">
                        <i class="fas fa-pencil-alt mr-1"></i>Edit
                    </button>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Program</p>
                            <p class="font-semibold text-gray-900" id="review-program">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Kegiatan</p>
                            <p class="font-semibold text-gray-900" id="review-kegiatan">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Sub Kegiatan</p>
                            <p class="font-semibold text-gray-900" id="review-subkegiatan">-</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Sisa Anggaran</span>
                            <span class="font-bold text-green-600" id="review-sisa-anggaran">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Detail Perjalanan -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-plane text-purple-600"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Detail Perjalanan</h4>
                    </div>
                    <button type="button" class="btn-edit-step text-xs text-blue-600 hover:text-blue-800 font-medium" data-step="2">
                        <i class="fas fa-pencil-alt mr-1"></i>Edit
                    </button>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tipe Perjalanan</p>
                            <p class="font-semibold text-gray-900" id="review-tipe">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Alat Angkut</p>
                            <p class="font-semibold text-gray-900" id="review-alat-angkut">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tempat Berangkat</p>
                            <p class="font-semibold text-gray-900" id="review-tempat-berangkat">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tempat Tujuan</p>
                            <p class="font-semibold text-gray-900" id="review-tempat-tujuan">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Berangkat</p>
                            <p class="font-semibold text-gray-900" id="review-tanggal-berangkat">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Kembali</p>
                            <p class="font-semibold text-gray-900" id="review-tanggal-kembali">-</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-1">Maksud Perjalanan</p>
                        <p class="text-sm text-gray-900 leading-relaxed" id="review-maksud">-</p>
                    </div>
                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-1">Dasar Surat</p>
                        <p class="text-sm text-gray-900" id="review-dasar-surat">-</p>
                    </div>
                    <div class="mt-3" id="review-file-container" style="display:none;">
                        <p class="text-xs text-gray-500 mb-1">Surat Tugas</p>
                        <div class="flex items-center text-sm text-blue-600">
                            <i class="fas fa-file-pdf mr-2"></i>
                            <span id="review-file-name">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Pegawai -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Pegawai & Penanggung Jawab</h4>
                    </div>
                    <button type="button" class="btn-edit-step text-xs text-blue-600 hover:text-blue-800 font-medium" data-step="3">
                        <i class="fas fa-pencil-alt mr-1"></i>Edit
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-4 pb-4 border-b border-gray-100">
                        <p class="text-xs text-gray-500 mb-2">Penanggung Jawab</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-tie text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900" id="review-pj-nama">-</p>
                                <p class="text-xs text-gray-500" id="review-pj-nip">-</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Daftar Pegawai (<span id="review-jumlah-pegawai">0</span> orang)</p>
                        <div id="review-pegawai-list" class="space-y-2">
                            <!-- Pegawai list will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Estimasi Biaya -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-money-bill-wave text-orange-600"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Estimasi Biaya</h4>
                    </div>
                    <button type="button" class="btn-edit-step text-xs text-blue-600 hover:text-blue-800 font-medium" data-step="4">
                        <i class="fas fa-pencil-alt mr-1"></i>Edit
                    </button>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Transportasi</span>
                            <span class="font-semibold text-gray-900" id="review-biaya-transportasi">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Penginapan</span>
                            <span class="font-semibold text-gray-900" id="review-biaya-penginapan">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Uang Harian</span>
                            <span class="font-semibold text-gray-900" id="review-uang-harian">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Transport Lokal</span>
                            <span class="font-semibold text-gray-900" id="review-transport-lokal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Lain-lain</span>
                            <span class="font-semibold text-gray-900" id="review-biaya-lainnya">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t-2 border-gray-200">
                            <span class="font-bold text-gray-900">TOTAL</span>
                            <span class="font-bold text-xl text-orange-600" id="review-total-biaya">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Declaration -->
            <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" id="declare-responsible" class="mt-1 mr-3 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 mb-1">Pernyataan Tanggung Jawab</p>
                        <p class="text-xs text-gray-700 leading-relaxed">
                            Saya menyatakan bahwa data yang diisikan dalam SPPD ini adalah benar dan dapat dipertanggungjawabkan. 
                            Saya bersedia menerima konsekuensi apabila dikemudian hari terdapat data yang tidak sesuai.
                        </p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row gap-3">
                <button type="button" id="btn-save-draft" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i>
                    Simpan sebagai Draft
                </button>
                <button type="submit" id="btn-submit-sppd" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center shadow-lg" disabled>
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan SPPD
                </button>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    let formData = {};

    // Load all data from previous steps
    $(document).on('stepLoaded', function(e, step, data) {
        if (step === 5) {
            formData = data;
            populateReview();
        }
    });

    // Populate review
    function populateReview() {
        // Program & Kegiatan
        $('#review-program').text(formData.program_text || '-');
        $('#review-kegiatan').text(formData.kegiatan_text || '-');
        $('#review-subkegiatan').text(formData.sub_kegiatan_text || '-');
        $('#review-sisa-anggaran').text(formatRupiah(formData.sisa_anggaran || 0));

        // Detail Perjalanan
        $('#review-tipe').text(formData.tipe_perjalanan || '-');
        $('#review-alat-angkut').text(formData.alat_angkut || '-');
        $('#review-tempat-berangkat').text(formData.tempat_berangkat || '-');
        $('#review-tempat-tujuan').text(formData.tempat_tujuan || '-');
        $('#review-tanggal-berangkat').text(formatTanggal(formData.tanggal_berangkat) || '-');
        $('#review-tanggal-kembali').text(formatTanggal(formData.tanggal_kembali) || '-');
        $('#review-maksud').text(formData.maksud_perjalanan || '-');
        $('#review-dasar-surat').text(formData.dasar_surat || '-');

        if (formData.file_surat_tugas_name) {
            $('#review-file-name').text(formData.file_surat_tugas_name);
            $('#review-file-container').show();
        }

        // Pegawai
        $('#review-pj-nama').text(formData.penanggung_jawab_nama || '-');
        $('#review-pj-nip').text('NIP: ' + (formData.penanggung_jawab_nip || '-'));
        $('#review-jumlah-pegawai').text(formData.jumlah_pegawai || 0);

        // Populate pegawai list
        const pegawaiContainer = $('#review-pegawai-list');
        pegawaiContainer.empty();
        
        if (formData.pegawai_data && formData.pegawai_data.length > 0) {
            formData.pegawai_data.forEach((pegawai, index) => {
                const item = $('<div class="flex items-center p-2 bg-gray-50 rounded-lg">' +
                              '<div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 font-bold text-sm">' + (index + 1) + '</div>' +
                              '<div class="flex-1 min-w-0">' +
                              '<p class="text-sm font-semibold text-gray-900 truncate">' + pegawai.nama + '</p>' +
                              '<p class="text-xs text-gray-500">NIP: ' + pegawai.nip + '</p>' +
                              '</div>' +
                              '</div>');
                pegawaiContainer.append(item);
            });
        }

        // Biaya
        const biaya = formData.biaya || {};
        $('#review-biaya-transportasi').text(formatRupiah(biaya.transportasi || 0));
        $('#review-biaya-penginapan').text(formatRupiah(biaya.penginapan || 0));
        $('#review-uang-harian').text(formatRupiah(biaya.uang_harian || 0));
        $('#review-transport-lokal').text(formatRupiah(biaya.transport_lokal || 0));
        $('#review-biaya-lainnya').text(formatRupiah(biaya.lainnya || 0));
        $('#review-total-biaya').text(formatRupiah(formData.estimasi_biaya || 0));
    }

    // Generate No SPPD
    $('#btn-generate-no').on('click', function() {
        const year = new Date().getFullYear();
        const month = ('0' + (new Date().getMonth() + 1)).slice(-2);
        const random = Math.floor(Math.random() * 9000) + 1000;
        const noSppd = `SPPD/${random}/<?= user_bidang_id() ?>/${month}/${year}`;
        
        $('#no-sppd').val(noSppd);
        
        Toastify({
            text: "Nomor SPPD berhasil digenerate",
            duration: 2000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
        }).showToast();
    });

    // Edit step
    $('.btn-edit-step').on('click', function() {
        const step = $(this).data('step');
        $(document).trigger('goToStep', [step]);
    });

    // Declaration checkbox
    $('#declare-responsible').on('change', function() {
        $('#btn-submit-sppd').prop('disabled', !$(this).is(':checked'));
    });

    // Save as draft
    $('#btn-save-draft').on('click', function() {
        submitForm(true);
    });

    // Submit form
    $('#step5-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#declare-responsible').is(':checked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Anda harus menyetujui pernyataan tanggung jawab terlebih dahulu'
            });
            return;
        }

        submitForm(false);
    });

    // Submit function
    function submitForm(isDraft) {
        const submitData = new FormData();
        
        // Add all form data
        Object.keys(formData).forEach(key => {
            if (key !== 'pegawai_data' && key !== 'biaya') {
                submitData.append(key, formData[key]);
            }
        });

        // Add pegawai IDs
        if (formData.pegawai_ids) {
            formData.pegawai_ids.forEach(id => {
                submitData.append('pegawai_ids[]', id);
            });
        }

        // Add file if exists
        if (formData.file_surat_tugas) {
            submitData.append('file_surat_tugas', formData.file_surat_tugas);
        }

        // Add draft flag
        submitData.append('save_as_draft', isDraft ? 'true' : 'false');

        // Add no_sppd if filled
        const noSppd = $('#no-sppd').val();
        if (noSppd) {
            submitData.append('no_sppd', noSppd);
        }

        // Show loading
        Swal.fire({
            title: isDraft ? 'Menyimpan Draft...' : 'Mengajukan SPPD...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit
        $.ajax({
            url: '<?= base_url('kepalabidang/sppd/submit') ?>',
            type: 'POST',
            data: submitData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '<?= base_url('kepalabidang/sppd') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response?.message || 'Terjadi kesalahan saat menyimpan SPPD'
                });
            }
        });
    }

    // Format helpers
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function formatTanggal(dateStr) {
        if (!dateStr) return '-';
        
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        // Parse date (format: dd-mm-yyyy)
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            const date = new Date(parts[2], parts[1] - 1, parts[0]);
            const dayName = days[date.getDay()];
            const day = parts[0];
            const month = months[date.getMonth()];
            const year = parts[2];
            
            return `${dayName}, ${day} ${month} ${year}`;
        }
        
        return dateStr;
    }
});
</script>