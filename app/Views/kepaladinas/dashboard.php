<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-yellow-100 p-4 rounded">Pending Program<br><span class="text-2xl font-bold">5</span></div>
    <div class="bg-orange-100 p-4 rounded">Pending Kegiatan<br><span class="text-2xl font-bold">3</span></div>
    <div class="bg-blue-100 p-4 rounded">Pending Sub Kegiatan<br><span class="text-2xl font-bold">2</span></div>
    <div class="bg-red-100 p-4 rounded">Pending SPPD<br><span class="text-2xl font-bold">7</span></div>
</div>

<div class="mt-6 bg-white p-4 rounded shadow">
    <h3 class="font-semibold mb-2">Recent Approvals</h3>
    <table id="approvals-table" class="min-w-full">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Nama</th>
                <th>Bidang</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        $('#approvals-table').DataTable();
    })
</script>
<?= $this->endSection() ?>