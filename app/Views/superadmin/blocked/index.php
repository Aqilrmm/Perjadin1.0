<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Blocked Users</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="blocked-table" class="min-w-full">
        <thead>
            <tr>
                <th>NIP</th>
                <th>Nama</th>
                <th>Reason</th>
                <th>Blocked Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->endSection() ?>