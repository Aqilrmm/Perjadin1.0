<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Logs Keamanan</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="logs-table" class="min-w-full">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<?= $this->endSection() ?>