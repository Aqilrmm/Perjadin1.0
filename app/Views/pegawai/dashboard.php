<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded">Total SPPD Saya<br><span class="text-2xl font-bold">12</span></div>
    <div class="bg-white p-4 rounded">SPPD Berjalan<br><span class="text-2xl font-bold">2</span></div>
    <div class="bg-white p-4 rounded">Butuh Action<br><span class="text-2xl font-bold">1</span></div>
</div>

<div class="mt-6">
    <h3 class="text-lg font-semibold mb-2">Upcoming Trips</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <?= $this->include('pegawai/components/sppd_card') ?>
    </div>
</div>

<?= $this->endSection() ?>