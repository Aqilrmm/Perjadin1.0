<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Persetujuan SPPD</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="kd-sppd-table" class="min-w-full">
        <thead>
            <tr>
                <th>No SPPD</th>
                <th>Bidang</th>
                <th>Tujuan</th>
                <th>Tanggal</th>
                <th>Jumlah Pegawai</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->endSection() ?>