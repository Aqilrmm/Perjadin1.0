<div class="step-container">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Detail Perjalanan Dinas</h3>
    <p class="text-sm text-gray-600 mb-6">Lengkapi informasi terkait perjalanan dinas yang akan dilakukan</p>

    <form id="step2-form">
        <?= csrf_field() ?>
        
        <div class="space-y-5">
            <!-- Tipe Perjalanan & Dasar Surat -->
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
                        Dasar Surat / Nomor Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="dasar_surat" id="dasar-surat" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 005/SPPD/I/2024" required>
                </div>
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

            <!-- Upload Surat Tugas (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Surat Tugas (Optional)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-all" id="file-upload-area">
                    <input type="file" id="file-surat-tugas" name="file_surat_tugas" accept=".pdf,.doc,.docx" class="hidden">
                    <div id="upload-placeholder">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 mb-1">Klik untuk upload atau drag & drop</p>
                        <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max. 5MB)</p>
                    </div>
                    <div id="file-preview" class="hidden">
                        <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                        <p class="text-sm text-gray-700 font-medium" id="file-name"></p>
                        <p class="text-xs text-gray-500" id="file-size"></p>
                        <button type="button" id="remove-file" class="mt-2 text-xs text-red-600 hover:text-red-800">
                            <i class="fas fa-times mr-1"></i>Hapus file
                        </button>
                    </div>
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

    // File upload handling
    $('#file-upload-area').on('click', function() {
        if (!$('#file-preview').hasClass('hidden')) return;
        $('#file-surat-tugas').click();
    });

    $('#file-upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('border-blue-500 bg-blue-50');
    });

    $('#file-upload-area').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('border-blue-500 bg-blue-50');
    });

    $('#file-upload-area').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('border-blue-500 bg-blue-50');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    $('#file-surat-tugas').on('change', function() {
        if (this.files.length > 0) {
            handleFileSelect(this.files[0]);
        }
    });

    function handleFileSelect(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (!allowedTypes.includes(file.type)) {
            showToast('Format file tidak didukung. Gunakan PDF, DOC, atau DOCX', 'error');
            return;
        }

        if (file.size > maxSize) {
            showToast('Ukuran file terlalu besar. Maksimal 5MB', 'error');
            return;
        }

        // Show preview
        $('#upload-placeholder').addClass('hidden');
        $('#file-preview').removeClass('hidden');
        $('#file-name').text(file.name);
        $('#file-size').text(formatFileSize(file.size));

        // Update icon based on file type
        const icon = file.type.includes('pdf') ? 'fa-file-pdf text-red-500' : 'fa-file-word text-blue-500';
        $('#file-preview i').attr('class', 'fas ' + icon + ' text-4xl mb-2');
    }

    $('#remove-file').on('click', function(e) {
        e.stopPropagation();
        $('#file-surat-tugas').val('');
        $('#file-preview').addClass('hidden');
        $('#upload-placeholder').removeClass('hidden');
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
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

            // Restore file if exists
            if (data.file_surat_tugas_name) {
                $('#upload-placeholder').addClass('hidden');
                $('#file-preview').removeClass('hidden');
                $('#file-name').text(data.file_surat_tugas_name);
                $('#file-size').text(data.file_surat_tugas_size || '');
            }
        }
    });
});
</script>

<style>
input[type="radio"]:checked + i + span {
    font-weight: 600;
}

#file-upload-area {
    cursor: pointer;
}

#file-upload-area:hover {
    background-color: #f9fafb;
}
</style>