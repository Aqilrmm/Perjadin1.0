<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Dashboard Keuangan</h1>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded">Menunggu Verifikasi<br><span class="text-2xl font-bold">5</span></div>
    <div class="bg-white p-4 rounded">Diverifikasi Bulan Ini<br><span class="text-2xl font-bold">12</span></div>
    <div class="bg-white p-4 rounded">Total Pencairan<br><span class="text-2xl font-bold">Rp 200.000.000</span></div>
    <div class="bg-white p-4 rounded">Ditolak/Return<br><span class="text-2xl font-bold">2</span></div>
</div>
<?= $this->endSection() ?>