<div class="step-container">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Detail Perjalanan Dinas</h3>
    <p class="text-sm text-gray-600 mb-6">Lengkapi informasi terkait perjalanan dinas yang akan dilakukan</p>

    <form id="step2-form">
        <?= csrf_field() ?>
        
        <div class="space-y-5">
            <!-- Tipe Perjalanan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Perjalanan <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_perjalanan" id="tipe-perjalanan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Dalam Daerah">Dalam Daerah</option>
                        <option value="Luar Daerah Dalam Provinsi">Luar Daerah Dalam Provinsi</option>
                        <option value="Luar Daerah Luar Provinsi">Luar Daerah Luar Provinsi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alat Angkut Yang Dipergunakan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="alat_angkut" id="alat-angkut" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Mobil Dinas, Pesawat, Kereta Api" required>
                    <p class="text-xs text-gray-500 mt-1">Masukkan jenis alat angkut yang akan digunakan</p>
                </div>
            </div>

            <!-- Dasar Surat Radio Button -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Apakah Memiliki Dasar Surat? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-6">
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" name="has_dasar_surat" value="1" class="w-4 h-4 text-blue-600 focus:ring-blue-500 focus:ring-2">
                        <span class="ml-2 text-sm text-gray-700 group-hover:text-blue-600">Ya, Ada Dasar Surat</span>
                    </label>
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" name="has_dasar_surat" value="0" class="w-4 h-4 text-blue-600 focus:ring-blue-500 focus:ring-2">
                        <span class="ml-2 text-sm text-gray-700 group-hover:text-blue-600">Tidak Ada</span>
                    </label>
                </div>
            </div>

            <!-- Dasar Surat Form (Conditional) -->
            <div id="dasar-surat-container" class="hidden space-y-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start mb-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-alt text-blue-600 text-lg"></i>
                    </div>
                    <div class="ml-2">
                        <h4 class="text-sm font-semibold text-blue-900">Informasi Dasar Surat</h4>
                        <p class="text-xs text-blue-700 mt-1">Masukkan nomor surat</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Dasar Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="dasar_surat" id="dasar-surat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 005/SPPD/I/2024">
                </div>
            </div>

            <!-- Kode Rekening -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" name="kode_rekening" id="kode-rekening" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 5.1.02.01.01.0001" required>
                <p class="text-xs text-gray-500 mt-1">Masukkan kode rekening sesuai dengan sub kegiatan</p>
            </div>

            <!-- Maksud Perjalanan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Maksud Perjalanan <span class="text-red-500">*</span>
                </label>
                <textarea name="maksud_perjalanan" id="maksud-perjalanan" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan tujuan dan maksud perjalanan dinas secara detail (minimal 20 karakter)" required></textarea>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-xs text-gray-500">Minimal 20 karakter</p>
                    <span id="char-count" class="text-xs text-gray-500">0 karakter</span>
                </div>
            </div>

            <!-- Tempat Berangkat & Tujuan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tempat Berangkat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tempat_berangkat" id="tempat-berangkat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Banjarmasin" value="Banjarmasin" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tempat Tujuan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tempat_tujuan" id="tempat-tujuan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Jakarta" required>
                </div>
            </div>

            <!-- Tanggal & Lama Perjalanan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Berangkat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tanggal_berangkat" id="tanggal-berangkat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pilih tanggal" required readonly>
                    <p class="text-xs text-gray-500 mt-1">Minimal H+1 dari hari ini</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Lama Perjalanan (Hari) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="lama_perjalanan" id="lama-perjalanan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" min="1" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Kembali <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tanggal_kembali" id="tanggal-kembali" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50" placeholder="Auto calculated" readonly>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Initialize Flatpickr for Tanggal Berangkat
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    const tanggalBerangkatPicker = flatpickr('#tanggal-berangkat', {
        dateFormat: 'd-m-Y',
        minDate: tomorrow,
        locale: 'id',
        onChange: function(selectedDates, dateStr) {
            calculateTanggalKembali();
        }
    });

    // Toggle Dasar Surat Form
    $('input[name="has_dasar_surat"]').on('change', function() {
        const hasDasarSurat = $(this).val() === '1';
        
        if (hasDasarSurat) {
            $('#dasar-surat-container').stop(true, true).removeClass('hidden').addClass('animate-fade-in');
            $('#dasar-surat').prop('required', true);
        } else {
            $('#dasar-surat-container').stop(true, true).addClass('hidden').removeClass('animate-fade-in');
            $('#dasar-surat').prop('required', false).val('');
        }
    });

    // Character counter for maksud perjalanan
    $('#maksud-perjalanan').on('input', function() {
        const length = $(this).val().length;
        $('#char-count').text(length + ' karakter');
        
        if (length < 20) {
            $('#char-count').removeClass('text-gray-500').addClass('text-red-500');
        } else {
            $('#char-count').removeClass('text-red-500').addClass('text-gray-500');
        }
    });

    // Calculate tanggal kembali
    $('#lama-perjalanan').on('input', function() {
        calculateTanggalKembali();
    });

    function calculateTanggalKembali() {
        const tanggalBerangkat = $('#tanggal-berangkat').val();
        const lamaPerjalanan = parseInt($('#lama-perjalanan').val()) || 0;

        if (tanggalBerangkat && lamaPerjalanan > 0) {
            const berangkat = flatpickr.parseDate(tanggalBerangkat, 'd-m-Y');
            const kembali = new Date(berangkat);
            kembali.setDate(kembali.getDate() + lamaPerjalanan - 1);

            const formatted = flatpickr.formatDate(kembali, 'd-m-Y');
            $('#tanggal-kembali').val(formatted);
        } else {
            $('#tanggal-kembali').val('');
        }
    }

    // Restore data on step load
    $(document).on('stepLoaded', function(e, step, data) {
        if (step === 2) {
            // Restore form values
            Object.keys(data).forEach(key => {
                const $field = $(`[name="${key}"]`);
                if ($field.length) {
                    if ($field.attr('type') === 'radio') {
                        $(`[name="${key}"][value="${data[key]}"]`).prop('checked', true).trigger('change');
                    } else {
                        $field.val(data[key]);
                    }
                }
            });

            // Update character count
            const length = $('#maksud-perjalanan').val().length;
            $('#char-count').text(length + ' karakter');

            // Trigger dasar surat toggle if data exists
            if (data.has_dasar_surat) {
                $(`input[name="has_dasar_surat"][value="${data.has_dasar_surat}"]`).trigger('change');
            }
        }
    });
});
</script>

<style>
input[type="radio"]:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>