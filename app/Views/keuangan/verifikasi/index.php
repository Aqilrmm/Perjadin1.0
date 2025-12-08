<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Verifikasi SPPD</h1>
    <div>
        <button class="bg-gray-200 px-3 py-1 rounded">Export</button>
    </div>
</div>

<div class="bg-white p-4 rounded shadow">
    <table id="verifikasi-table" class="min-w-full">
        <thead>
            <tr>
                <th>No SPPD</th>
                <th>Bidang</th>
                <th>Pegawai</th>
                <th>Tgl Submit</th>
                <th>Total</th>
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
        $('#verifikasi-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('keuangan/verifikasi/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'no_sppd'
                },
                {
                    data: 'nama_bidang'
                },
                {
                    data: 'jumlah_pegawai'
                },
                {
                    data: 'submitted_at_formatted'
                },
                {
                    data: 'estimasi_biaya_formatted'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [3, 'desc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>