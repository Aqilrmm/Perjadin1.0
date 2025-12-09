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

<?= $this->section('scripts') ?>
<script>
    $(function() {
        var blockedTable = $('#blocked-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('superadmin/blocked/datatable') ?>',
                type: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                error: function(xhr, error, thrown) {
                    console.error('Blocked datatable AJAX error:', xhr.responseText || error || thrown);
                    var msg = 'Terjadi error pada server';
                    try {
                        var json = JSON.parse(xhr.responseText || '{}');
                        if (json.message) msg = json.message;
                    } catch (e) {
                        // not JSON
                        msg = xhr.responseText ? xhr.responseText.substring(0, 1000) : msg;
                    }

                    function escapeHtml(s) {
                        return String(s).replace(/[&<>"']/g, function(m) {
                            return ({
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                '"': '&quot;',
                                "'": '&#39;'
                            } [m]);
                        });
                    }
                    Swal.fire({
                        title: 'AJAX Error',
                        html: '<pre style="text-align:left;white-space:pre-wrap">' + escapeHtml(msg) + '</pre>',
                        icon: 'error',
                        width: 800
                    });
                }
            },
            columns: [{
                    data: 'nip_nik'
                },
                {
                    data: 'nama_lengkap'
                },
                {
                    data: 'blocked_reason'
                },
                {
                    data: 'blocked_at_formatted'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [3, 'desc']
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).attr('data-id', data.id);
            }
        });

        // Detail
        $('#blocked-table').on('click', '.btn-detail', function() {
            var id = $(this).data('id');
            if (!id) return;
            $.get('<?= site_url('superadmin/blocked/detail') ?>/' + id, function(res) {
                if (res.status) {
                    var payload = res.data;
                    var u = payload.user || payload;
                    var recent = payload.recent_activities || [];
                    var html = '<div style="text-align:left">';
                    html += '<p><strong>NIP/NIK:</strong> ' + (u.nip_nik || '-') + '</p>';
                    html += '<p><strong>Nama:</strong> ' + (u.nama || '-') + '</p>';
                    html += '<p><strong>Role:</strong> ' + (u.role || '-') + '</p>';
                    html += '<p><strong>Blocked By:</strong> ' + (u.blocked_by_name || '-') + ' (' + (u.blocked_by_nip || '-') + ')</p>';
                    html += '<p><strong>Reason:</strong><br/>' + (u.blocked_reason || '-') + '</p>';
                    html += '<p><strong>Blocked At:</strong> ' + (u.blocked_at || '-') + '</p>';
                    if (recent.length) {
                        html += '<hr/><p><strong>Recent Activities:</strong></p><ul style="text-align:left;padding-left:1.2em;">';
                        recent.forEach(function(r) {
                            html += '<li>[' + (r.created_at || r.created_at) + '] ' + (r.action || '') + ' - ' + (r.description || '') + '</li>';
                        });
                        html += '</ul>';
                    }
                    html += '</div>';
                    Swal.fire({
                        title: 'User Detail',
                        html: html,
                        width: 800
                    });
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat detail', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Gagal memuat detail', 'error');
            });
        });

        // Unblock
        $('#blocked-table').on('click', '.btn-unblock', function() {
            var id = $(this).data('id');
            if (!id) return;
            Swal.fire({
                title: 'Yakin ingin membuka blokir?',
                icon: 'question',
                showCancelButton: true
            }).then(function(res) {
                if (res.isConfirmed) {
                    $.post('<?= site_url('superadmin/blocked/unblock') ?>/' + id, function(resp) {
                        if (resp.status) {
                            Swal.fire('Dibuka', resp.message, 'success');
                            blockedTable.ajax.reload();
                        } else {
                            Swal.fire('Error', resp.message || 'Gagal membuka blokir', 'error');
                        }
                    }, 'json').fail(function() {
                        Swal.fire('Error', 'Gagal membuka blokir', 'error');
                    });
                }
            });
        });

        // History (uses BlockController::history)
        $('#blocked-table').on('click', '.btn-history', function() {
            var id = $(this).data('id');
            if (!id) return;
            $.get('<?= site_url('superadmin/blocked/history') ?>/' + id, function(res) {
                if (res.status) {
                    var items = res.data.history || [];
                    var html = '<div style="text-align:left"><ul style="padding-left:1.2em;">';
                    items.forEach(function(it) {
                        html += '<li>[' + (it.created_at || '') + '] ' + (it.action || '') + ' - ' + (it.description || '') + '</li>';
                    });
                    html += '</ul></div>';
                    Swal.fire({
                        title: 'Block History',
                        html: html,
                        width: 800
                    });
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat history', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Gagal memuat history', 'error');
            });
        });
    });
</script>
<?= $this->endSection() ?>