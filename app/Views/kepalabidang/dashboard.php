<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Dashboard Kepala Bidang</h1>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded">Program Aktif<br><span class="text-2xl font-bold"><?= $statistics['total_programs'] ?></span></div>
    <div class="bg-white p-4 rounded">SPPD Bulan Ini<br><span class="text-2xl font-bold"><?= $statistics['sppd_bulan_ini'] ?></span></div>
    <div class="bg-white p-4 rounded">Anggaran Tahun Ini<br><span class="text-2xl font-bold"><?= format_rupiah($statistics['anggaran_tahun_ini']) ?></span></div>
</div>
<?= $this->endSection() ?>