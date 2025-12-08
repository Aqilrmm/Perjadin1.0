<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Dashboard Kepala Bidang</h1>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded">Program Aktif<br><span class="text-2xl font-bold">10</span></div>
    <div class="bg-white p-4 rounded">Kegiatan Berjalan<br><span class="text-2xl font-bold">3</span></div>
    <div class="bg-white p-4 rounded">SPPD Bulan Ini<br><span class="text-2xl font-bold">1</span></div>
    <div class="bg-white p-4 rounded">Sisa Anggaran<br><span class="text-2xl font-bold">Rp 100.000.000</span></div>
</div>
<?= $this->endSection() ?>