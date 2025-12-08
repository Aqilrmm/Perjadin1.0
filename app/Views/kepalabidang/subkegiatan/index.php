<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Sub Kegiatan</h1>
<div class="bg-white p-4 rounded shadow">
    <div class="flex justify-between items-center mb-3">
        <div>
            <button id="btn-new-subkegiatan" class="bg-blue-600 text-white px-3 py-1 rounded">+ Buat Sub Kegiatan</button>
        </div>
    </div>
    <table id="subkegiatan-table" class="min-w-full">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kegiatan</th>
                <th>Anggaran</th>
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
        var table = $('#subkegiatan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepalabidang/subkegiatan/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'kode_sub_kegiatan'
                },
                {
                    data: 'nama_sub_kegiatan'
                },
                {
                    data: 'nama_kegiatan'
                },
                {
                    data: 'anggaran_sub_kegiatan'
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
                [0, 'asc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>