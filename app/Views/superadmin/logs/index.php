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

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var logsTable = $('#logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('superadmin/logs/datatable') ?>',
                type: 'POST'
            },
            columns: [{
                    data: 'created_at_formatted'
                },
                {
                    data: 'user_display'
                },
                {
                    data: 'action_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description'
                },
                {
                    data: 'ip_address'
                }
            ],
            order: [
                [0, 'desc']
            ],
            createdRow: function(row, data, dataIndex) {
                // make the row clickable to view detail
                $(row).attr('data-id', data.id);
                $(row).addClass('cursor-pointer');
            }
        });

        // Row click -> show details
        $('#logs-table tbody').on('click', 'tr', function() {
            var id = $(this).data('id');
            if (!id) return;

            $.get('<?= site_url('superadmin/logs/detail') ?>/' + id, function(res) {
                if (res.status) {
                    var d = res.data;
                    var html = '<div style="text-align:left">';
                    html += '<p><strong>Timestamp:</strong> ' + (d.created_at_formatted || d.created_at) + '</p>';
                    html += '<p><strong>User:</strong> ' + (d.nama ? (d.nama + ' (' + (d.nip_nik || '-') + ')') : 'System') + '</p>';
                    html += '<p><strong>Action:</strong> ' + (d.action || '-') + '</p>';
                    html += '<p><strong>IP:</strong> ' + (d.ip_address || '-') + '</p>';
                    html += '<p><strong>User Agent:</strong><br/>' + (d.user_agent || '-') + '</p>';
                    html += '<p><strong>Description:</strong><br/>' + (d.description || '-') + '</p>';
                    html += '</div>';

                    Swal.fire({
                        title: 'Log Detail',
                        html: html,
                        width: 800
                    });
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat detail log', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Gagal memuat detail log', 'error');
            });
        });
    });
</script>
<?= $this->endSection() ?>