<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Kelola Bidang</h1>
    <div>
        <button id="btn-add-bidang" class="bg-blue-600 text-white px-3 py-1 rounded">+ Tambah Bidang</button>
    </div>
</div>

<div class="bg-white p-4 rounded shadow">
    <table id="bidang-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Bidang</th>
                <th>Jumlah Pegawai</th>
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
        var table = $('#bidang-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('superadmin/bidang/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'kode_bidang'
                },
                {
                    data: 'nama_bidang'
                },
                {
                    data: 'jumlah_pegawai'
                },
                {
                    data: 'status_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [1, 'asc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>