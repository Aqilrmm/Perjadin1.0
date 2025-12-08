<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Persetujuan Program</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="kd-programs-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Program</th>
                <th>Bidang</th>
                <th>Tahun</th>
                <th>Anggaran</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->endSection() ?>