<div class="step-container">
    <h3 class="text-lg font-semibold text-gray-900 mb-1">Pilih Pegawai & Penanggung Jawab</h3>
    <p class="text-sm text-gray-600 mb-6">Tentukan penanggung jawab dan pilih pegawai yang akan melakukan perjalanan dinas</p>

    <form id="step3-form">
        <?= csrf_field() ?>
        
        <div class="space-y-5">
            <!-- Penanggung Jawab -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <label class="block text-sm font-semibold text-blue-900 mb-2">
                            Penanggung Jawab SPPD <span class="text-red-500">*</span>
                        </label>
                        <select name="penanggung_jawab" id="penanggung-jawab" class="w-full" required>
                            <option value="">-- Pilih Penanggung Jawab --</option>
                        </select>
                        <p class="text-xs text-blue-700 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Penanggung jawab harus Kepala Bidang atau lebih tinggi
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-semibold text-purple-900 mb-1">Informasi Pemilihan Pegawai</h4>
                        <ul class="text-xs text-purple-700 space-y-1 list-disc list-inside">
                            <li>Minimal pilih 1 pegawai untuk perjalanan dinas</li>
                            <li>Tidak ada batas maksimal jumlah pegawai</li>
                            <li>Pegawai boleh dari bidang lain (lintas bidang)</li>
                            <li>Penanggung jawab otomatis termasuk dalam daftar jika belum dipilih</li>
                            <li>Sistem akan memberikan warning jika ada jadwal overlap</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Daftar Pegawai -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-900">
                            <i class="fas fa-user-friends text-green-600 mr-2"></i>
                            Pilih Pegawai
                        </h4>
                        <span class="text-xs text-gray-600">
                            Terpilih: <span id="count-pegawai" class="font-bold text-green-600">0</span> pegawai
                        </span>
                    </div>
                </div>
                
                <div class="p-4">
                    <!-- Search & Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cari Pegawai</label>
                            <input type="text" id="search-pegawai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Nama, NIP, atau Jabatan...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Filter Bidang</label>
                            <select id="filter-bidang" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="">Semua Bidang</option>
                                <!-- Will be populated via AJAX -->
                            </select>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex gap-2 mb-3">
                        <button type="button" id="btn-select-all" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors">
                            <i class="fas fa-check-double mr-1"></i>Pilih Semua
                        </button>
                        <button type="button" id="btn-clear-all" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded transition-colors">
                            <i class="fas fa-times mr-1"></i>Hapus Semua
                        </button>
                    </div>

                    <!-- Pegawai List -->
                    <div id="pegawai-list-container" class="max-h-96 overflow-y-auto">
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Memuat daftar pegawai...</p>
                        </div>
                    </div>

                    <!-- Selected Pegawai Preview -->
                    <div id="selected-pegawai-container" class="mt-4 pt-4 border-t border-gray-200" style="display:none;">
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">Pegawai Terpilih:</h5>
                        <div id="selected-pegawai-list" class="space-y-2">
                            <!-- Selected pegawai will appear here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overlap Warning Container -->
            <div id="overlap-warnings" class="hidden">
                <!-- Overlap warnings will be inserted here -->
            </div>
        </div>

        <!-- Hidden input for pegawai IDs -->
        <input type="hidden" name="pegawai_ids" id="pegawai-ids">
    </form>
</div>

<script>
$(document).ready(function() {
    let selectedPegawai = [];
    let allPegawai = [];
    let tanggalBerangkat = '';
    let tanggalKembali = '';

    // Initialize Penanggung Jawab Select2
    $('#penanggung-jawab').select2({
        placeholder: '-- Pilih Penanggung Jawab --',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '<?= base_url('api/pegawai/search') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    role: 'kepalabidang,kepaladinas,superadmin', // Only higher roles
                    status: 'active'
                };
            },
            processResults: function(data) {
                return {
                    results: data.results || data
                };
            },
            cache: true
        },
        minimumInputLength: 0,
        templateResult: formatPegawaiOption,
        templateSelection: formatPegawaiSelection
    });

    // Load data from previous steps
    $(document).on('stepLoaded', function(e, step, data) {
        if (step === 3) {
            console.log('Step 3 loaded with data:', data); // Debug
            
            tanggalBerangkat = data.tanggal_berangkat || '';
            tanggalKembali = data.tanggal_kembali || '';
            
            console.log('Tanggal:', tanggalBerangkat, tanggalKembali); // Debug
            
            // Set penanggung jawab if exists
            if (data.penanggung_jawab) {
                const option = new Option(data.penanggung_jawab_nama || 'Loading...', data.penanggung_jawab, true, true);
                $('#penanggung-jawab').append(option).trigger('change');
            } else {
                // Default to current user if Kepala Bidang
                const currentUserId = '<?= user_id() ?>';
                const currentUserRole = '<?= user_role() ?>';
                console.log('Current user:', currentUserId, currentUserRole); // Debug
                
                if (currentUserRole === 'kepalabidang') {
                    const currentUserName = '<?= esc(session('nama')) ?>';
                    const option = new Option(currentUserName, currentUserId, true, true);
                    $('#penanggung-jawab').append(option).trigger('change');
                }
            }

            // Restore selected pegawai
            if (data.pegawai_ids) {
                if (typeof data.pegawai_ids === 'string') {
                    try {
                        selectedPegawai = JSON.parse(data.pegawai_ids).map(id => parseInt(id));
                    } catch(e) {
                        selectedPegawai = [];
                    }
                } else if (Array.isArray(data.pegawai_ids)) {
                    selectedPegawai = data.pegawai_ids.map(id => parseInt(id));
                } else {
                    selectedPegawai = [];
                }
            }
            
            console.log('Selected pegawai:', selectedPegawai); // Debug

            // Load pegawai list
            loadPegawaiList();
            loadBidangFilter();
        }
    });

    // Format Select2 options
    function formatPegawaiOption(pegawai) {
        if (!pegawai.id) return pegawai.text;
        
        return $('<div class="flex items-center">' +
                '<div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">' +
                '<i class="fas fa-user text-blue-600"></i>' +
                '</div>' +
                '<div>' +
                '<div class="font-semibold">' + pegawai.text + '</div>' +
                '<div class="text-xs text-gray-500">' + (pegawai.nip || '') + ' - ' + (pegawai.jabatan || '') + '</div>' +
                '</div>' +
                '</div>');
    }

    function formatPegawaiSelection(pegawai) {
        return pegawai.text;
    }

    // Load Bidang Filter
    function loadBidangFilter() {
        $.get('<?= base_url('api/bidang/options') ?>', function(response) {
            const $select = $('#filter-bidang');
            $select.find('option:not(:first)').remove();
            
            if (response.results) {
                response.results.forEach(bidang => {
                    $select.append(new Option(bidang.text, bidang.id));
                });
            }
        });
    }

    // Load Pegawai List
    function loadPegawaiList(search = '', bidangId = '') {
        const container = $('#pegawai-list-container');
        container.html('<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i><p class="text-sm text-gray-500">Memuat daftar pegawai...</p></div>');

        // Use DataTables endpoint or create a simple list endpoint
        $.ajax({
            url: '<?= base_url('api/pegawai/search') ?>',
            type: 'GET',
            data: {
                search: search,
                bidang_id: bidangId
            },
            success: function(response) {
                console.log('API Response:', response); // Debug
                
                // Handle different response formats
                if (response.status && response.data) {
                    allPegawai = response.data;
                } else if (Array.isArray(response)) {
                    allPegawai = response;
                } else if (response.results) {
                    allPegawai = response.results;
                } else {
                    allPegawai = [];
                }
                
                console.log('All Pegawai:', allPegawai); // Debug
                renderPegawaiList();
            },
            error: function(xhr, status, error) {
                console.error('Error loading pegawai:', xhr, status, error);
                
                // Fallback: load from hardcoded data for testing
                loadPegawaiListFallback(search, bidangId);
            }
        });
    }
    
    // Fallback method if API not ready
    function loadPegawaiListFallback(search = '', bidangId = '') {
        const container = $('#pegawai-list-container');
        
        // Simulate pegawai data - replace with actual data from your database
        const mockPegawai = [
            {
                id: 1,
                text: 'John Doe',
                nama: 'John Doe',
                nip: '198501012010011001',
                jabatan: 'Staff IT',
                bidang_nama: 'Teknologi Informasi',
                bidang_id: 1
            },
            {
                id: 2,
                text: 'Jane Smith',
                nama: 'Jane Smith',
                nip: '198601012010012002',
                jabatan: 'Staff Keuangan',
                bidang_nama: 'Keuangan',
                bidang_id: 2
            },
            {
                id: 3,
                text: 'Bob Johnson',
                nama: 'Bob Johnson',
                nip: '198701012010013003',
                jabatan: 'Staff Kepegawaian',
                bidang_nama: 'Kepegawaian',
                bidang_id: 3
            },
            {
                id: 4,
                text: 'Alice Williams',
                nama: 'Alice Williams',
                nip: '198801012010014004',
                jabatan: 'Staff Perencanaan',
                bidang_nama: 'Perencanaan',
                bidang_id: 4
            },
            {
                id: 5,
                text: 'Charlie Brown',
                nama: 'Charlie Brown',
                nip: '198901012010015005',
                jabatan: 'Staff Umum',
                bidang_nama: 'Umum',
                bidang_id: 5
            }
        ];
        
        // Apply filters
        allPegawai = mockPegawai.filter(p => {
            let match = true;
            
            if (search) {
                const searchLower = search.toLowerCase();
                match = match && (
                    p.nama.toLowerCase().includes(searchLower) ||
                    p.nip.includes(searchLower) ||
                    p.jabatan.toLowerCase().includes(searchLower)
                );
            }
            
            if (bidangId) {
                match = match && p.bidang_id == bidangId;
            }
            
            return match;
        });
        
        console.log('Fallback Pegawai:', allPegawai);
        renderPegawaiList();
    }

    // Render Pegawai List
    function renderPegawaiList() {
        const container = $('#pegawai-list-container');
        
        console.log('Rendering pegawai list, total:', allPegawai.length); // Debug
        
        if (allPegawai.length === 0) {
            container.html('<div class="text-center py-8"><i class="fas fa-users text-3xl text-gray-300 mb-2"></i><p class="text-sm text-gray-500">Tidak ada pegawai ditemukan</p></div>');
            return;
        }

        container.empty();
        
        allPegawai.forEach(pegawai => {
            const pegawaiId = parseInt(pegawai.id);
            const isSelected = selectedPegawai.includes(pegawaiId);
            const pjId = $('#penanggung-jawab').val();
            const isPJ = pjId && parseInt(pjId) === pegawaiId;
            
            // Get pegawai name
            const pegawaiName = pegawai.nama || pegawai.text || 'Unknown';
            const pegawaiNip = pegawai.nip || pegawai.nip_nik || '-';
            const pegawaiJabatan = pegawai.jabatan || '-';
            const pegawaiBidang = pegawai.bidang_nama || '-';
            
            const item = $(`
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors mb-2 cursor-pointer pegawai-item" data-id="${pegawaiId}">
                    <div class="flex items-center flex-1 min-w-0">
                        <input type="checkbox" 
                               class="pegawai-checkbox w-4 h-4 text-blue-600 rounded mr-3 cursor-pointer" 
                               data-id="${pegawaiId}" 
                               ${isSelected ? 'checked' : ''}>
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-white font-bold text-sm">${pegawaiName.charAt(0).toUpperCase()}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">${pegawaiName}</p>
                            <p class="text-xs text-gray-500">NIP: ${pegawaiNip}</p>
                            <p class="text-xs text-gray-500">${pegawaiJabatan} - ${pegawaiBidang}</p>
                        </div>
                    </div>
                    ${isPJ ? '<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full flex-shrink-0">Penanggung Jawab</span>' : ''}
                </div>
            `);
            
            container.append(item);
        });

        updateSelectedCount();
        updateSelectedPreview();
    }

    // Search Pegawai
    // Event: Live Search
$('#search-pegawai').on('input', function () {
    loadPegawaiList($(this).val(), $('#filter-bidang').val());
});

// Event: Filter Bidang
$('#filter-bidang').on('change', function () {
    loadPegawaiList($('#search-pegawai').val(), $(this).val());
});

// Load Pegawai List (Tanpa mock, API asli)
function loadPegawaiList(search = '', bidangId = '') {
    const container = $('#pegawai-list-container');

    container.html(`
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
            <p class="text-sm text-gray-500">Memuat daftar pegawai...</p>
        </div>
    `);

    $.ajax({
        url: '<?= base_url('api/pegawai/search') ?>',
        type: 'GET',
        data: {
            q: search,
            bidang_id: bidangId,
            status: 'active'
        },
        success: function(response) {

            if (response.results) {
                allPegawai = response.results;
            } else if (response.data) {
                allPegawai = response.data;
            } else if (Array.isArray(response)) {
                allPegawai = response;
            } else {
                allPegawai = [];
            }

            renderPegawaiList();
        },
        error: function() {
            container.html(`
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-3xl text-red-400 mb-2"></i>
                    <p class="text-sm text-gray-500">Gagal memuat data pegawai</p>
                </div>
            `);
        }
    });
}


    // Filter Bidang
    $('#filter-bidang').on('change', function() {
        const bidangId = $(this).val();
        const search = $('#search-pegawai').val();
        loadPegawaiList(search, bidangId);
    });

    // Checkbox change handler (also handle click on item)
    $(document).on('change', '.pegawai-checkbox', function(e) {
        e.stopPropagation();
        const pegawaiId = parseInt($(this).data('id'));
        
        if ($(this).is(':checked')) {
            if (!selectedPegawai.includes(pegawaiId)) {
                selectedPegawai.push(pegawaiId);
            }
        } else {
            selectedPegawai = selectedPegawai.filter(id => id !== pegawaiId);
        }

        updateSelectedCount();
        updateSelectedPreview();
        checkOverlap();
    });
    
    // Handle click on pegawai item (toggle checkbox)
    $(document).on('click', '.pegawai-item', function(e) {
        if ($(e.target).hasClass('pegawai-checkbox')) {
            return; // Already handled by checkbox change
        }
        
        const checkbox = $(this).find('.pegawai-checkbox');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    });
    
    // Select All
    $('#btn-select-all').on('click', function() {
        if (allPegawai.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Tidak Ada Pegawai',
                text: 'Tidak ada pegawai untuk dipilih'
            });
            return;
        }
        
        Swal.fire({
            title: 'Pilih Semua Pegawai?',
            text: `${allPegawai.length} pegawai akan dipilih`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Pilih Semua',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3B82F6'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedPegawai = allPegawai.map(p => parseInt(p.id));
                renderPegawaiList();
                checkOverlap();
                
                Toastify({
                    text: `${selectedPegawai.length} pegawai dipilih`,
                    duration: 2000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                }).showToast();
            }
        });
    });
    
    // Clear All
    $('#btn-clear-all').on('click', function() {
        if (selectedPegawai.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Tidak Ada Pegawai Terpilih',
                text: 'Tidak ada pegawai untuk dihapus'
            });
            return;
        }
        
        const pjId = parseInt($('#penanggung-jawab').val());
        const hasPJ = pjId && selectedPegawai.includes(pjId);
        
        Swal.fire({
            title: 'Hapus Semua Pegawai?',
            text: hasPJ ? 'Penanggung jawab akan tetap terpilih' : `${selectedPegawai.length} pegawai akan dihapus`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#EF4444'
        }).then((result) => {
            if (result.isConfirmed) {
                if (hasPJ) {
                    selectedPegawai = [pjId]; // Keep only PJ
                } else {
                    selectedPegawai = [];
                }
                renderPegawaiList();
                $('#overlap-warnings').addClass('hidden').empty();
                
                Toastify({
                    text: "Semua pegawai dihapus",
                    duration: 2000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #f85032, #e73827)",
                }).showToast();
            }
        });
    });

    // Penanggung Jawab change handler
    $('#penanggung-jawab').on('change', function() {
        const pjId = parseInt($(this).val());
        
        if (pjId && !selectedPegawai.includes(pjId)) {
            selectedPegawai.push(pjId);
            updateSelectedCount();
            updateSelectedPreview();
            renderPegawaiList(); // Re-render to show PJ badge
            checkOverlap();
        }
    });

    // Update selected count
    function updateSelectedCount() {
        $('#count-pegawai').text(selectedPegawai.length);
        $('#pegawai-ids').val(JSON.stringify(selectedPegawai));
    }

    // Update selected preview
    function updateSelectedPreview() {
        const container = $('#selected-pegawai-list');
        const containerWrapper = $('#selected-pegawai-container');
        
        if (selectedPegawai.length === 0) {
            containerWrapper.hide();
            return;
        }

        containerWrapper.show();
        container.empty();

        selectedPegawai.forEach((pegawaiId, index) => {
            const pegawai = allPegawai.find(p => parseInt(p.id) === pegawaiId);
            if (!pegawai) return;

            const isPJ = parseInt($('#penanggung-jawab').val()) === pegawaiId;
            
            const badge = $('<div class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium mr-2 mb-2">' +
                          '<span class="mr-2">' + (index + 1) + '.</span>' +
                          '<span>' + pegawai.text + '</span>' +
                          (isPJ ? '<span class="ml-2 px-1.5 py-0.5 bg-blue-600 text-white rounded text-xs">PJ</span>' : '') +
                          '<button type="button" class="ml-2 text-blue-600 hover:text-blue-800 remove-pegawai" data-id="' + pegawai.id + '">' +
                          '<i class="fas fa-times"></i>' +
                          '</button>' +
                          '</div>');
            
            container.append(badge);
        });
    }

    // Remove pegawai from selection
    $(document).on('click', '.remove-pegawai', function() {
        const pegawaiId = parseInt($(this).data('id'));
        const isPJ = parseInt($('#penanggung-jawab').val()) === pegawaiId;
        
        if (isPJ) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Dihapus',
                text: 'Penanggung jawab tidak dapat dihapus dari daftar pegawai. Ubah penanggung jawab terlebih dahulu.'
            });
            return;
        }

        selectedPegawai = selectedPegawai.filter(id => id !== pegawaiId);
        updateSelectedCount();
        updateSelectedPreview();
        renderPegawaiList();
        checkOverlap();
    });

    // Check overlap
    function checkOverlap() {
        if (!tanggalBerangkat || !tanggalKembali || selectedPegawai.length === 0) {
            $('#overlap-warnings').addClass('hidden').empty();
            return;
        }

        $.ajax({
            url: '<?= base_url('kepalabidang/sppd/validate-step3') ?>',
            type: 'POST',
            data: {
                pegawai_ids: selectedPegawai,
                tanggal_berangkat: tanggalBerangkat,
                tanggal_kembali: tanggalKembali
            },
            success: function(response) {
                if (response.status && response.data.warnings && response.data.warnings.length > 0) {
                    displayOverlapWarnings(response.data.warnings);
                } else {
                    $('#overlap-warnings').addClass('hidden').empty();
                }
            }
        });
    }

    // Display overlap warnings
    function displayOverlapWarnings(warnings) {
        const container = $('#overlap-warnings');
        container.empty();

        warnings.forEach(warning => {
            const alert = $('<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-3">' +
                          '<div class="flex items-start">' +
                          '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>' +
                          '<div class="flex-1">' +
                          '<h5 class="text-sm font-semibold text-yellow-900 mb-1">Peringatan Jadwal Overlap</h5>' +
                          '<p class="text-sm text-yellow-700 mb-2">' +
                          '<strong>' + warning.pegawai_nama + '</strong> memiliki SPPD yang overlap pada tanggal tersebut:' +
                          '</p>' +
                          '<ul class="text-xs text-yellow-600 space-y-1 list-disc list-inside">' +
                          warning.overlaps.map(o => '<li>' + o.no_sppd + ' (' + o.tanggal_berangkat + ' - ' + o.tanggal_kembali + ')</li>').join('') +
                          '</ul>' +
                          '<p class="text-xs text-yellow-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Ini hanya peringatan, Anda masih dapat melanjutkan.</p>' +
                          '</div>' +
                          '</div>' +
                          '</div>');
            
            container.append(alert);
        });

        container.removeClass('hidden');
    }

    // Debounce helper
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Validate before proceeding
    $(document).on('validateStep', function(e, step) {
        if (step === 3) {
            if (!$('#penanggung-jawab').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Penanggung jawab harus dipilih'
                });
                return false;
            }

            if (selectedPegawai.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Minimal pilih 1 pegawai untuk perjalanan dinas'
                });
                return false;
            }

            return true;
        }
    });
    
    // Save data when moving to next step
    $(document).on('beforeNextStep', function(e, currentStep) {
        if (currentStep === 3) {
            // Update hidden input with latest data
            $('#pegawai-ids').val(JSON.stringify(selectedPegawai));
            
            // Also save to formData (if formData object exists in parent scope)
            if (typeof window.sppdFormData !== 'undefined') {
                window.sppdFormData.pegawai_ids = selectedPegawai;
                window.sppdFormData.penanggung_jawab = $('#penanggung-jawab').val();
                window.sppdFormData.jumlah_pegawai = selectedPegawai.length;
                
                // Save pegawai data for display in review
                window.sppdFormData.pegawai_data = selectedPegawai.map(id => {
                    const pegawai = allPegawai.find(p => parseInt(p.id) === id);
                    return pegawai || null;
                }).filter(p => p !== null);
            }
            
            console.log('Step 3 data saved:', {
                pegawai_ids: selectedPegawai,
                penanggung_jawab: $('#penanggung-jawab').val(),
                jumlah_pegawai: selectedPegawai.length
            });
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

.pegawai-checkbox:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

#pegawai-list-container::-webkit-scrollbar {
    width: 8px;
}

#pegawai-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#pegawai-list-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

#pegawai-list-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>