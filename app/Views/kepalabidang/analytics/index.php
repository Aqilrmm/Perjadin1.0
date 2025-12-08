<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Analytics Bidang</h1>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white p-4 rounded shadow"><canvas id="kb-chart-1"></canvas></div>
    <div class="bg-white p-4 rounded shadow"><canvas id="kb-chart-2"></canvas></div>
</div>
<?= $this->endSection() ?>