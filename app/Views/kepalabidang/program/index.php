<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Program</h1>
<div class="bg-white p-4 rounded shadow">
    <div class="flex justify-between items-center mb-3">
        <div>
            <button id="btn-new-program" class="bg-blue-600 text-white px-3 py-1 rounded">+ Ajukan Program Baru</button>
        </div>
        <div>
            <button class="bg-gray-200 px-3 py-1 rounded">Export List</button>
        </div>
    </div>
    <table id="programs-table" class="min-w-full">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Program</th>
                <th>Tahun</th>
                <th>Anggaran</th>
                <th>Sisa</th>
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
        var table = $('#programs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepalabidang/programs/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'kode_program'
                },
                {
                    data: 'nama_program'
                },
                {
                    data: 'tahun_anggaran'
                },
                {
                    data: 'jumlah_anggaran'
                },
                {
                    data: 'sisa_anggaran'
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