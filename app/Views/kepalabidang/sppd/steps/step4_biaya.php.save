<div class="step-container">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Estimasi Biaya Perjalanan Dinas</h3>
    <p class="text-sm text-gray-600 mb-6">Hitung estimasi biaya berdasarkan komponen perjalanan dinas</p>

    <form id="step4-form">
        <?= csrf_field() ?>
        
        <div class="space-y-5">
            <!-- Info Card -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-600 mb-1">Jumlah Pegawai</p>
                        <p class="text-2xl font-bold text-blue-600" id="summary-jumlah-pegawai">0</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-600 mb-1">Lama Perjalanan</p>
                        <p class="text-2xl font-bold text-purple-600" id="summary-lama-perjalanan">0 hari</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-600 mb-1">Sisa Anggaran</p>
                        <p class="text-lg font-bold text-green-600" id="summary-sisa-anggaran">Rp 0</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-600 mb-1">Total Estimasi</p>
                        <p class="text-lg font-bold text-orange-600" id="summary-total-estimasi">Rp 0</p>
                    </div>
                </div>
            </div>

            <!-- Komponen Biaya -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900">Komponen Biaya Per Pegawai</h4>
                    <p class="text-xs text-gray-600 mt-1">Isi estimasi biaya untuk setiap komponen (dalam Rupiah)</p>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Biaya Transportasi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-plane text-blue-600 mr-2"></i>
                                Biaya Transportasi (Pergi-Pulang)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="text" name="biaya_transportasi" id="biaya-transportasi" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" data-cleave="rupiah">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Tiket pesawat/kereta/bus/speedboat</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hotel text-purple-600 mr-2"></i>
                                Biaya Penginapan (Per Malam)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="text" name="biaya_penginapan" id="biaya-penginapan" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" data-cleave="rupiah">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Total: <span id="total-penginapan" class="font-semibold text-gray-700">0 malam × Rp 0 = Rp 0</span>
                            </p>
                        </div>
                    </div>

                    <!-- Uang Harian & Transport Lokal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-coins text-green-600 mr-2"></i>
                                Uang Harian (Per Hari)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="text" name="uang_harian" id="uang-harian" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" data-cleave="rupiah">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Total: <span id="total-uang-harian" class="font-semibold text-gray-700">0 hari × Rp 0 = Rp 0</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-taxi text-yellow-600 mr-2"></i>
                                Transport Lokal (Total)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="text" name="transport_lokal" id="transport-lokal" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" data-cleave="rupiah">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Taxi, ojek, rental mobil di lokasi tujuan</p>
                        </div>
                    </div>

                    <!-- Biaya Lain-lain -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-ellipsis-h text-gray-600 mr-2"></i>
                            Biaya Lain-lain (Optional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                            <input type="text" name="biaya_lainnya" id="biaya-lainnya" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" data-cleave="rupiah">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Parkir, toll, makan tambahan, dll</p>
                    </div>
                </div>
            </div>

            <!-- Breakdown Biaya -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900">Rincian Biaya Total</h4>
                </div>

                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Transportasi (<span id="breakdown-jumlah-pegawai-1">0</span> pegawai)</span>
                            <span class="font-semibold text-gray-900" id="breakdown-transportasi">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Penginapan (<span id="breakdown-jumlah-pegawai-2">0</span> pegawai × <span id="breakdown-lama">0</span> malam)</span>
                            <span class="font-semibold text-gray-900" id="breakdown-penginapan">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Uang Harian (<span id="breakdown-jumlah-pegawai-3">0</span> pegawai × <span id="breakdown-hari">0</span> hari)</span>
                            <span class="font-semibold text-gray-900" id="breakdown-uang-harian">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Transport Lokal (<span id="breakdown-jumlah-pegawai-4">0</span> pegawai)</span>
                            <span class="font-semibold text-gray-900" id="breakdown-transport">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Lain-lain</span>
                            <span class="font-semibold text-gray-900" id="breakdown-lainnya">Rp 0</span>
                        </div>
                        
                        <!-- Total -->
                        <div class="flex justify-between items-center py-3 bg-blue-50 px-4 rounded-lg mt-4">
                            <span class="text-base font-bold text-gray-900">TOTAL ESTIMASI BIAYA</span>
                            <span class="text-xl font-bold text-blue-600" id="breakdown-total">Rp 0</span>
                        </div>

                        <!-- Hidden input for total -->
                        <input type="hidden" name="estimasi_biaya" id="estimasi-biaya-total">
                    </div>
                </div>
            </div>

            <!-- Budget Warning -->
            <div id="warning-budget" class="hidden">
                <!-- Budget warning will be inserted here -->
            </div>

            <!-- Preset Templates -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-magic text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-semibold text-purple-900 mb-2">Template Biaya</h4>
                        <p class="text-xs text-purple-700 mb-3">Gunakan template untuk mempercepat pengisian</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <button type="button" class="btn-template px-3 py-2 bg-white hover:bg-purple-50 border border-purple-300 text-purple-700 text-xs font-medium rounded-lg transition-colors duration-200" data-template="dalam-daerah">
                                <i class="fas fa-map-marker-alt mr-1"></i>Dalam Daerah
                            </button>
                            <button type="button" class="btn-template px-3 py-2 bg-white hover:bg-purple-50 border border-purple-300 text-purple-700 text-xs font-medium rounded-lg transition-colors duration-200" data-template="luar-provinsi">
                                <i class="fas fa-plane mr-1"></i>Luar Provinsi
                            </button>
                            <button type="button" class="btn-template px-3 py-2 bg-white hover:bg-purple-50 border border-purple-300 text-purple-700 text-xs font-medium rounded-lg transition-colors duration-200" data-template="clear">
                                <i class="fas fa-eraser mr-1"></i>Reset Semua
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    let jumlahPegawai = 0;
    let lamaPerjalanan = 0;
    let sisaAnggaran = 0;
    let tipePerjalanan = '';

    // Templates
    const templates = {
        'dalam-daerah': {
            biaya_transportasi: 0,
            biaya_penginapan: 0,
            uang_harian: 200000,
            transport_lokal: 100000,
            biaya_lainnya: 50000
        },
        'luar-provinsi': {
            biaya_transportasi: 1500000,
            biaya_penginapan: 500000,
            uang_harian: 350000,
            transport_lokal: 250000,
            biaya_lainnya: 100000
        }
    };

    // Initialize Cleave.js for Rupiah formatting
    const biayaFields = ['biaya-transportasi', 'biaya-penginapan', 'uang-harian', 'transport-lokal', 'biaya-lainnya'];
    
    biayaFields.forEach(fieldId => {
        new Cleave('#' + fieldId, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: '.',
            numeralPositiveOnly: true,
            onValueChanged: function(e) {
                calculateTotal();
            }
        });
    });

    // Load data from previous steps
    $(document).on('stepLoaded', function(e, step, data) {
        if (step === 4) {
            jumlahPegawai = parseInt(data.jumlah_pegawai) || 0;
            lamaPerjalanan = parseInt(data.lama_perjalanan) || 0;
            sisaAnggaran = parseFloat(data.sisa_anggaran) || 0;
            tipePerjalanan = data.tipe_perjalanan || '';

            // Update summary
            $('#summary-jumlah-pegawai, #breakdown-jumlah-pegawai-1, #breakdown-jumlah-pegawai-2, #breakdown-jumlah-pegawai-3, #breakdown-jumlah-pegawai-4').text(jumlahPegawai);
            $('#summary-lama-perjalanan').text(lamaPerjalanan + ' hari');
            $('#breakdown-lama').text(lamaPerjalanan - 1); // Nights = days - 1
            $('#breakdown-hari').text(lamaPerjalanan);
            $('#summary-sisa-anggaran').text(formatRupiah(sisaAnggaran));

            // Restore form values
            biayaFields.forEach(fieldId => {
                const fieldName = fieldId.replace(/-/g, '_');
                if (data[fieldName]) {
                    $('#' + fieldId)[0].cleave.setRawValue(data[fieldName]);
                }
            });

            calculateTotal();
        }
    });

    // Calculate total
    function calculateTotal() {
        const biayaTransportasi = parseFloat($('#biaya-transportasi')[0].cleave.getRawValue()) || 0;
        const biayaPenginapan = parseFloat($('#biaya-penginapan')[0].cleave.getRawValue()) || 0;
        const uangHarian = parseFloat($('#uang-harian')[0].cleave.getRawValue()) || 0;
        const transportLokal = parseFloat($('#transport-lokal')[0].cleave.getRawValue()) || 0;
        const biayaLainnya = parseFloat($('#biaya-lainnya')[0].cleave.getRawValue()) || 0;

        const malam = lamaPerjalanan - 1; // Nights = days - 1
        
        // Calculate per pegawai
        const totalTransportasi = biayaTransportasi * jumlahPegawai;
        const totalPenginapan = biayaPenginapan * malam * jumlahPegawai;
        const totalUangHarian = uangHarian * lamaPerjalanan * jumlahPegawai;
        const totalTransportLokal = transportLokal * jumlahPegawai;
        const totalLainnya = biayaLainnya * jumlahPegawai;

        const grandTotal = totalTransportasi + totalPenginapan + totalUangHarian + totalTransportLokal + totalLainnya;

        // Update breakdown
        $('#breakdown-transportasi').text(formatRupiah(totalTransportasi));
        $('#breakdown-penginapan').text(formatRupiah(totalPenginapan));
        $('#breakdown-uang-harian').text(formatRupiah(totalUangHarian));
        $('#breakdown-transport').text(formatRupiah(totalTransportLokal));
        $('#breakdown-lainnya').text(formatRupiah(totalLainnya));
        $('#breakdown-total').text(formatRupiah(grandTotal));
        $('#summary-total-estimasi').text(formatRupiah(grandTotal));

        // Update helper text
        $('#total-penginapan').text(malam + ' malam × ' + formatRupiah(biayaPenginapan) + ' = ' + formatRupiah(biayaPenginapan * malam));
        $('#total-uang-harian').text(lamaPerjalanan + ' hari × ' + formatRupiah(uangHarian) + ' = ' + formatRupiah(uangHarian * lamaPerjalanan));

        // Set hidden input
        $('#estimasi-biaya-total').val(grandTotal);

        // Check budget
        checkBudget(grandTotal);
    }

    // Check budget
    function checkBudget(total) {
        const container = $('#warning-budget');
        container.empty();

        if (total > sisaAnggaran) {
            const alert = $('<div class="bg-red-50 border border-red-200 rounded-lg p-4">' +
                          '<div class="flex items-start">' +
                          '<div class="flex-shrink-0">' +
                          '<i class="fas fa-exclamation-circle text-red-600 text-xl"></i>' +
                          '</div>' +
                          '<div class="ml-3 flex-1">' +
                          '<h4 class="text-sm font-semibold text-red-900">Peringatan: Anggaran Melebihi Batas!</h4>' +
                          '<p class="text-sm text-red-700 mt-1">Total estimasi biaya <strong>' + formatRupiah(total) + '</strong> melebihi sisa anggaran <strong>' + formatRupiah(sisaAnggaran) + '</strong></p>' +
                          '<p class="text-xs text-red-600 mt-2">Selisih: <strong>' + formatRupiah(total - sisaAnggaran) + '</strong></p>' +
                          '<p class="text-xs text-red-600 mt-1">Silakan kurangi estimasi biaya atau hubungi admin untuk penambahan anggaran.</p>' +
                          '</div>' +
                          '</div>' +
                          '</div>');
            container.append(alert);
            container.removeClass('hidden');
        } else {
            const percentage = (total / sisaAnggaran) * 100;
            
            if (percentage > 80) {
                const alert = $('<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">' +
                              '<div class="flex items-start">' +
                              '<div class="flex-shrink-0">' +
                              '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>' +
                              '</div>' +
                              '<div class="ml-3 flex-1">' +
                              '<h4 class="text-sm font-semibold text-yellow-900">Peringatan: Anggaran Hampir Habis</h4>' +
                              '<p class="text-sm text-yellow-700 mt-1">Estimasi biaya mencapai <strong>' + Math.round(percentage) + '%</strong> dari sisa anggaran</p>' +
                              '<p class="text-xs text-yellow-600 mt-2">Sisa anggaran setelah SPPD ini: <strong>' + formatRupiah(sisaAnggaran - total) + '</strong></p>' +
                              '</div>' +
                              '</div>' +
                              '</div>');
                container.append(alert);
                container.removeClass('hidden');
            } else {
                const alert = $('<div class="bg-green-50 border border-green-200 rounded-lg p-4">' +
                              '<div class="flex items-start">' +
                              '<div class="flex-shrink-0">' +
                              '<i class="fas fa-check-circle text-green-600 text-xl"></i>' +
                              '</div>' +
                              '<div class="ml-3 flex-1">' +
                              '<h4 class="text-sm font-semibold text-green-900">Anggaran Mencukupi</h4>' +
                              '<p class="text-sm text-green-700 mt-1">Estimasi biaya dalam batas anggaran yang tersedia</p>' +
                              '<p class="text-xs text-green-600 mt-2">Sisa anggaran setelah SPPD ini: <strong>' + formatRupiah(sisaAnggaran - total) + '</strong> (' + Math.round(100 - percentage) + '% tersisa)</p>' +
                              '</div>' +
                              '</div>' +
                              '</div>');
                container.append(alert);
                container.removeClass('hidden');
            }
        }
    }

    // Template buttons
    $('.btn-template').on('click', function() {
        const template = $(this).data('template');
        
        if (template === 'clear') {
            Swal.fire({
                title: 'Reset semua biaya?',
                text: 'Semua input biaya akan dikosongkan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#EF4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    biayaFields.forEach(fieldId => {
                        $('#' + fieldId)[0].cleave.setRawValue(0);
                    });
                    calculateTotal();
                }
            });
        } else if (templates[template]) {
            Swal.fire({
                title: 'Gunakan template?',
                text: 'Template akan mengisi estimasi biaya default untuk ' + template.replace('-', ' '),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Gunakan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3B82F6'
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = templates[template];
                    $('#biaya-transportasi')[0].cleave.setRawValue(data.biaya_transportasi);
                    $('#biaya-penginapan')[0].cleave.setRawValue(data.biaya_penginapan);
                    $('#uang-harian')[0].cleave.setRawValue(data.uang_harian);
                    $('#transport-lokal')[0].cleave.setRawValue(data.transport_lokal);
                    $('#biaya-lainnya')[0].cleave.setRawValue(data.biaya_lainnya);
                    
                    calculateTotal();
                    
                    Toastify({
                        text: "Template berhasil diterapkan",
                        duration: 2000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #667eea, #764ba2)",
                    }).showToast();
                }
            });
        }
    });

    // Format Rupiah helper
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
});
</script>

<style>
input[data-cleave="rupiah"]:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>