<div class="step-container">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Pilih Program, Kegiatan & Sub Kegiatan</h3>
    <p class="text-sm text-gray-600 mb-6">Pilih program, kegiatan, dan sub kegiatan yang sudah disetujui</p>

    <form id="step1-form">
        <?= csrf_field() ?>
        
        <div class="space-y-4">
            <!-- Program -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Program <span class="text-red-500">*</span>
                </label>
                <select name="program_id" id="program-select" class="w-full" required>
                    <option value="">-- Pilih Program --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Hanya program yang sudah disetujui yang ditampilkan</p>
            </div>

            <!-- Kegiatan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kegiatan <span class="text-red-500">*</span>
                </label>
                <select name="kegiatan_id" id="kegiatan-select" class="w-full" required disabled>
                    <option value="">-- Pilih Kegiatan --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih program terlebih dahulu</p>
            </div>

            <!-- Sub Kegiatan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sub Kegiatan <span class="text-red-500">*</span>
                </label>
                <select name="sub_kegiatan_id" id="subkegiatan-select" class="w-full" required disabled>
                    <option value="">-- Pilih Sub Kegiatan --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih kegiatan terlebih dahulu</p>
            </div>

            <!-- Info Anggaran -->
            <div id="anggaran-info" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-semibold text-blue-900 mb-2">Informasi Anggaran</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                            <div>
                                <span class="text-blue-700 block mb-1">Total Anggaran</span>
                                <span class="font-bold text-blue-900" id="total-anggaran">Rp 0</span>
                            </div>
                            <div>
                                <span class="text-blue-700 block mb-1">Sudah Terpakai</span>
                                <span class="font-bold text-blue-900" id="anggaran-terpakai">Rp 0</span>
                            </div>
                            <div>
                                <span class="text-blue-700 block mb-1">Sisa Anggaran</span>
                                <span class="font-bold text-green-600 text-lg" id="sisa-anggaran">Rp 0</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-blue-700">Penggunaan Anggaran</span>
                                <span class="text-xs font-semibold text-blue-900" id="anggaran-percentage">0%</span>
                            </div>
                            <div class="w-full bg-blue-200 rounded-full h-2">
                                <div id="anggaran-progress" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Detail Sub Kegiatan -->
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-blue-700">Kode Sub Kegiatan:</span>
                                    <span class="font-semibold text-blue-900 ml-1" id="kode-subkegiatan">-</span>
                                </div>
                                <div>
                                    <span class="text-blue-700">Status:</span>
                                    <span class="ml-1" id="status-subkegiatan">
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                            <i class="fas fa-check-circle mr-1"></i>Approved
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning low budget -->
            <div id="warning-low-budget" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-yellow-900">Peringatan Anggaran Rendah</h4>
                        <p class="text-sm text-yellow-700 mt-1">Sisa anggaran sub kegiatan ini sudah kurang dari 20%. Pastikan estimasi biaya SPPD tidak melebihi sisa anggaran.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 for Program
    $('#program-select').select2({
        placeholder: '-- Pilih Program --',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?= base_url('api/programs/options') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    status: 'approved',
                    bidang_id: '<?= user_bidang_id() ?>'
                };
            },
            processResults: function(data) {
                return {
                    results: data.results || data
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Initialize Select2 for Kegiatan
    $('#kegiatan-select').select2({
        placeholder: '-- Pilih Kegiatan --',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?= base_url('api/kegiatan/options') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    program_id: $('#program-select').val(),
                    status: 'approved'
                };
            },
            processResults: function(data) {
                return {
                    results: data.results || data
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Initialize Select2 for Sub Kegiatan
    $('#subkegiatan-select').select2({
        placeholder: '-- Pilih Sub Kegiatan --',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?= base_url('api/subkegiatan/options') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    kegiatan_id: $('#kegiatan-select').val(),
                    status: 'approved'
                };
            },
            processResults: function(data) {
                return {
                    results: data.results || data
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Cascade: Program changed
    $('#program-select').on('change', function() {
        const programId = $(this).val();
        
        // Reset kegiatan & sub kegiatan
        $('#kegiatan-select').val(null).trigger('change').prop('disabled', !programId);
        $('#subkegiatan-select').val(null).trigger('change').prop('disabled', true);
        $('#anggaran-info').addClass('hidden');
        $('#warning-low-budget').addClass('hidden');
        
        if (programId) {
            $('#kegiatan-select').prop('disabled', false);
        }
    });

    // Cascade: Kegiatan changed
    $('#kegiatan-select').on('change', function() {
        const kegiatanId = $(this).val();
        
        // Reset sub kegiatan
        $('#subkegiatan-select').val(null).trigger('change').prop('disabled', !kegiatanId);
        $('#anggaran-info').addClass('hidden');
        $('#warning-low-budget').addClass('hidden');
        
        if (kegiatanId) {
            $('#subkegiatan-select').prop('disabled', false);
        }
    });

    // Sub Kegiatan changed - Load anggaran info
    $('#subkegiatan-select').on('change', function() {
        const subKegiatanId = $(this).val();
        
        if (!subKegiatanId) {
            $('#anggaran-info').addClass('hidden');
            $('#warning-low-budget').addClass('hidden');
            return;
        }

        // Show loading
        $('#anggaran-info').removeClass('hidden');
        $('#total-anggaran, #anggaran-terpakai, #sisa-anggaran').html('<i class="fas fa-spinner fa-spin"></i>');

        // Fetch anggaran data
        $.post('<?= base_url('kepalabidang/sppd/validate-step1') ?>', {
            sub_kegiatan_id: subKegiatanId,
            program_id: $('#program-select').val(),
            kegiatan_id: $('#kegiatan-select').val()
        })
        .done(function(response) {
            if (response.status && response.data) {
                const data = response.data;
                const subKegiatan = data.sub_kegiatan;
                const sisaAnggaran = data.sisa_anggaran || 0;
                const totalAnggaran = data.total_anggaran || 0;
                const terpakaiAnggaran = totalAnggaran - sisaAnggaran;
                const percentage = totalAnggaran > 0 ? ((terpakaiAnggaran / totalAnggaran) * 100) : 0;

                // Update display
                $('#total-anggaran').text(formatRupiah(totalAnggaran));
                $('#anggaran-terpakai').text(formatRupiah(terpakaiAnggaran));
                $('#sisa-anggaran').text(formatRupiah(sisaAnggaran));
                $('#anggaran-percentage').text(Math.round(percentage) + '%');
                $('#anggaran-progress').css('width', percentage + '%');
                $('#kode-subkegiatan').text(subKegiatan.kode_sub_kegiatan || '-');

                // Show warning if low budget
                if (percentage > 80) {
                    $('#warning-low-budget').removeClass('hidden');
                    $('#anggaran-progress').removeClass('bg-blue-600').addClass('bg-yellow-600');
                } else if (percentage > 90) {
                    $('#anggaran-progress').removeClass('bg-blue-600 bg-yellow-600').addClass('bg-red-600');
                } else {
                    $('#warning-low-budget').addClass('hidden');
                    $('#anggaran-progress').removeClass('bg-yellow-600 bg-red-600').addClass('bg-blue-600');
                }

                // Animate progress bar
                setTimeout(() => {
                    $('#anggaran-progress').css('width', percentage + '%');
                }, 100);

            } else {
                showToast('Gagal memuat informasi anggaran', 'error');
                $('#anggaran-info').addClass('hidden');
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            showToast(response?.message || 'Gagal memuat informasi anggaran', 'error');
            $('#anggaran-info').addClass('hidden');
        });
    });

    // Format Rupiah helper
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Restore data on step load
    $(document).on('stepLoaded', function(e, step, data) {
        if (step === 1 && data.program_id) {
            setTimeout(() => {
                if (data.program_id) {
                    const option = new Option(data.program_text || 'Loading...', data.program_id, true, true);
                    $('#program-select').append(option).trigger('change');
                }
                if (data.kegiatan_id) {
                    const option = new Option(data.kegiatan_text || 'Loading...', data.kegiatan_id, true, true);
                    $('#kegiatan-select').append(option).trigger('change');
                }
                if (data.sub_kegiatan_id) {
                    const option = new Option(data.sub_kegiatan_text || 'Loading...', data.sub_kegiatan_id, true, true);
                    $('#subkegiatan-select').append(option).trigger('change');
                }
            }, 300);
        }
    });
});
</script>

<style>
.select2-container--default .select2-selection--single {
    height: 42px !important;
    padding: 6px 12px !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
    color: #374151 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
}

.select2-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
}
</style>