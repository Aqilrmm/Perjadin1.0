<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="text-xl font-semibold mb-4">Daftar SPPD Bidang</h1>
<div class="bg-white p-4 rounded shadow">
    <table id="sppd-table" class="min-w-full">
        <thead>
            <tr>
                <th>No SPPD</th>
                <th>Program</th>
                <th>Tujuan</th>
                <th>Tanggal</th>
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
        var table = $('#sppd-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('kepalabidang/sppd/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'no_sppd'
                },
                {
                    data: 'nama_program'
                },
                {
                    data: 'tempat_tujuan'
                },
                {
                    data: 'tanggal_formatted'
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
                [0, 'desc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>