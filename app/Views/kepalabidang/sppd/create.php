<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold">Buat SPPD (Wizard)</h2>
    <div id="sppd-wizard" class="mt-4">
        <div class="mb-4">Step <span id="wizard-step">1</span>/5</div>
        <div id="wizard-step-content">
            <?= $this->include('kepalabidang/sppd/steps/step1_program') ?>
        </div>
    </div>
    <div class="mt-4 flex justify-between">
        <button id="wizard-prev" class="px-3 py-1 border rounded" disabled>Previous</button>
        <div>
            <button id="wizard-next" class="px-3 py-1 bg-blue-600 text-white rounded">Next</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let step = 1;
    $('#wizard-next').on('click', () => {
        if (step === 1) {
            $('#wizard-step-content').load('/views/kepalabidang/sppd/steps/step2_detail.php');
        }
        step++;
        $('#wizard-step').text(step);
    });
</script>
<?= $this->endSection() ?>