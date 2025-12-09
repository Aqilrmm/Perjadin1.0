<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buat SPPD Baru</h1>
                <p class="text-sm text-gray-600 mt-1">Wizard pembuatan Surat Perintah Perjalanan Dinas</p>
            </div>
            <a href="<?= base_url('kepalabidang/sppd') ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Wizard Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Step Progress Indicator -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center justify-between" id="step-progress">
                <!-- Step 1 -->
                <div class="flex items-center step-indicator active" data-step="1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-blue-600 font-bold shadow-lg step-circle">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="ml-3 hidden lg:block">
                        <div class="text-white text-sm font-semibold">Step 1</div>
                        <div class="text-blue-100 text-xs">Program</div>
                    </div>
                </div>

                <!-- Connector -->
                <div class="flex-1 h-1 bg-blue-400 mx-2"></div>

                <!-- Step 2 -->
                <div class="flex items-center step-indicator" data-step="2">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white font-bold step-circle">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                    <div class="ml-3 hidden lg:block">
                        <div class="text-blue-100 text-sm font-semibold">Step 2</div>
                        <div class="text-blue-200 text-xs">Detail</div>
                    </div>
                </div>

                <div class="flex-1 h-1 bg-blue-400 mx-2"></div>

                <!-- Step 3 -->
                <div class="flex items-center step-indicator" data-step="3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white font-bold step-circle">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="ml-3 hidden lg:block">
                        <div class="text-blue-100 text-sm font-semibold">Step 3</div>
                        <div class="text-blue-200 text-xs">Pegawai</div>
                    </div>
                </div>

                <div class="flex-1 h-1 bg-blue-400 mx-2"></div>

                <!-- Step 4 -->
                <!-- <div class="flex items-center step-indicator" data-step="4">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white font-bold step-circle">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="ml-3 hidden lg:block">
                        <div class="text-blue-100 text-sm font-semibold">Step 4</div>
                        <div class="text-blue-200 text-xs">Biaya</div>
                    </div>
                </div>

                <div class="flex-1 h-1 bg-blue-400 mx-2"></div> -->

                <!-- Step 5 -->
                <!-- <div class="flex items-center step-indicator" data-step="5">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white font-bold step-circle">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3 hidden lg:block">
                        <div class="text-blue-100 text-sm font-semibold">Step 5</div>
                        <div class="text-blue-200 text-xs">Review</div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Step Content -->
        <div class="p-8" id="step-content">
            <div class="flex items-center justify-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
                <span class="ml-3 text-gray-600">Memuat step...</span>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <button type="button" id="btn-previous" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                </button>

                <div class="flex items-center space-x-3">
                    <button type="button" id="btn-save-draft" class="px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan Draft
                    </button>
                    <button type="button" id="btn-next" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Selanjutnya<i class="fas fa-chevron-right ml-2"></i>
                    </button>
                    <button type="button" id="btn-submit" class="hidden px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Submit SPPD
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 3;
    const formData = {};

    // Load initial step
    loadStep(1);

    // Navigation handlers
    $('#btn-next').on('click', function() {
        validateAndProceed();
    });

    $('#btn-previous').on('click', function() {
        if (currentStep > 1) {
            saveCurrentStepData();
            loadStep(currentStep - 1);
        }
    });

    $('#btn-submit').on('click', function() {
        submitSPPD(false);
    });

    $('#btn-save-draft').on('click', function() {
        submitSPPD(true);
    });

    // Load step content
    function loadStep(stepNumber) {
        const $content = $('#step-content');
        
        // Show loading
        $content.html(`
            <div class="flex items-center justify-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
                <span class="ml-3 text-gray-600">Memuat step ${stepNumber}...</span>
            </div>
        `);

        $.ajax({
            url: `<?= base_url('kepalabidang/sppd/step/') ?>${stepNumber}`,
            type: 'GET',
            success: function(response) {
                if (response.status && response.data.html) {
                    $content.html(response.data.html);
                    currentStep = stepNumber;
                    updateStepIndicator();
                    updateNavigationButtons();
                    
                    // Restore saved data
                    restoreStepData(stepNumber);
                    
                    // Trigger custom event for step loaded
                    $(document).trigger('stepLoaded', [stepNumber, formData]);
                } else {
                    showToast('Gagal memuat step', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Gagal memuat step', 'error');
                $content.html(`
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-3"></i>
                        <p class="text-gray-600">Gagal memuat step. Silakan coba lagi.</p>
                    </div>
                `);
            }
        });
    }

    // Validate current step and proceed
    function validateAndProceed() {
        saveCurrentStepData();

        $.ajax({
            url: `<?= base_url('kepalabidang/sppd/validate-step') ?>${currentStep}`,
            type: 'POST',
            data: getCurrentStepFormData(),
            success: function(response) {
                if (response.status) {
                    // Save any additional data from validation
                    if (response.data) {
                        Object.assign(formData, response.data);
                    }
                    
                    if (currentStep < totalSteps) {
                        loadStep(currentStep + 1);
                    }
                } else {
                    showToast(response.message || 'Validasi gagal', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Validasi gagal', 'error');
                
                // Show validation errors if any
                if (response?.data) {
                    displayValidationErrors(response.data);
                }
            }
        });
    }

    // Get current step form data
    function getCurrentStepFormData() {
        const formSelector = `#step${currentStep}-form`;
        const $form = $(formSelector);
        
        if ($form.length) {
            return $form.serialize();
        }
        return {};
    }

    // Save current step data
    function saveCurrentStepData() {
        const formSelector = `#step${currentStep}-form`;
        const $form = $(formSelector);
        
        if ($form.length) {
            const data = $form.serializeArray();
            data.forEach(item => {
                formData[item.name] = item.value;
            });
        }
    }

    // Restore step data
    function restoreStepData(stepNumber) {
        const formSelector = `#step${stepNumber}-form`;
        const $form = $(formSelector);
        
        if ($form.length) {
            Object.keys(formData).forEach(key => {
                const $field = $form.find(`[name="${key}"]`);
                if ($field.length) {
                    $field.val(formData[key]);
                }
            });
        }
    }

    // Update step indicator
    function updateStepIndicator() {
        $('.step-indicator').each(function() {
            const step = $(this).data('step');
            const $circle = $(this).find('.step-circle');
            
            if (step < currentStep) {
                // Completed step
                $(this).addClass('completed').removeClass('active');
                $circle.removeClass('bg-blue-500 bg-white text-white text-blue-600')
                       .addClass('bg-green-500 text-white');
                $circle.html('<i class="fas fa-check"></i>');
            } else if (step === currentStep) {
                // Current step
                $(this).addClass('active').removeClass('completed');
                $circle.removeClass('bg-blue-500 bg-green-500 text-white')
                       .addClass('bg-white text-blue-600');
            } else {
                // Future step
                $(this).removeClass('active completed');
                $circle.removeClass('bg-white bg-green-500 text-blue-600')
                       .addClass('bg-blue-500 text-white');
            }
        });
    }

    // Update navigation buttons
    function updateNavigationButtons() {
        // Previous button
        $('#btn-previous').prop('disabled', currentStep === 1);
        
        // Next/Submit button
        if (currentStep === totalSteps) {
            $('#btn-next').hide();
            $('#btn-submit').show();
        } else {
            $('#btn-next').show();
            $('#btn-submit').hide();
        }
    }

    // Submit SPPD
    function submitSPPD(isDraft) {
        saveCurrentStepData();
        
        const submitData = { ...formData };
        submitData.save_as_draft = isDraft ? 'true' : 'false';
        
        // Show loading
        const $btn = isDraft ? $('#btn-save-draft') : $('#btn-submit');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

        $.ajax({
            url: '<?= base_url('kepalabidang/sppd/submit') ?>',
            type: 'POST',
            data: submitData,
            success: function(response) {
                if (response.status) {
                    showToast(response.message || 'SPPD berhasil disimpan', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= base_url('kepalabidang/sppd') ?>';
                    }, 1500);
                } else {
                    showToast(response.message || 'Gagal menyimpan SPPD', 'error');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Gagal menyimpan SPPD', 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    // Display validation errors
    function displayValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const $field = $(`[name="${field}"]`);
            if ($field.length) {
                $field.addClass('border-red-500');
                $field.after(`<p class="text-xs text-red-500 mt-1">${errors[field]}</p>`);
            }
        });
    }

    // Toast notification helper
    function showToast(message, type = 'info') {
        // Implement your toast notification here
        console.log(`[${type}] ${message}`);
        alert(message);
    }
});
</script>
<?= $this->endSection() ?>