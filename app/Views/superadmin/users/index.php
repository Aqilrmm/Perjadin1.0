<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold">Kelola User</h1>
    <div class="flex gap-2">
        <button id="btn-add-user" class="bg-blue-600 text-white px-3 py-1 rounded">+ Tambah User</button>
        <button id="btn-export" class="bg-gray-200 px-3 py-1 rounded">Export Excel</button>
    </div>
</div>

<div class="bg-white p-4 rounded shadow">
    <table id="users-table" class="min-w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>NIP/NIK</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Bidang</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?= $this->include('superadmin/users/modals/form_user') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Initialize DataTable with server-side AJAX
        var usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= site_url('superadmin/users/datatable') ?>',
                type: 'POST',
                data: function(d) {
                    // pass additional filters if needed
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nip_nik'
                },
                {
                    data: 'nama_lengkap'
                },
                {
                    data: 'jabatan'
                },
                {
                    data: 'nama_bidang'
                },
                {
                    data: 'role_badge',
                    orderable: false,
                    searchable: false
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
            ],
            createdRow: function(row, data, dataIndex) {
                // Add index number
                var info = usersTable.page.info();
                $('td', row).eq(0).html(dataIndex + 1 + (info.page * info.length));
            }
        });

        // Show modal add user
        $('#btn-add-user').on('click', function() {
            $('#modal-form-user').removeClass('hidden');
            $('#form-user')[0].reset();
        });

        // Export handler (simple redirect to export route)
        $('#btn-export').on('click', function() {
            var form = $('<form method="post" action="<?= site_url('api/datatable/export') ?>"></form>');
            form.append('<input type="hidden" name="type" value="users" />');
            $('body').append(form);
            form.submit();
            form.remove();
        });

        // Delegate action buttons
        $('#users-table').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.get('<?= site_url('superadmin/users/get') ?>/' + id, function(res) {
                if (res.status) {
                    var u = res.data;
                    $('#form-user [name="nip_nik"]').val(u.nip_nik);
                    $('#form-user [name="nama"]').val(u.nama);
                    $('#form-user [name="email"]').val(u.email);
                    $('#form-user [name="role"]').val(u.role);
                    if (u.bidang_id) {
                        var option = new Option(u.nama_bidang, u.bidang_id, true, true);
                        $('#form-user [name="bidang_id"]').append(option).trigger('change');
                    }
                    $('#modal-form-user').removeClass('hidden');
                } else {
                    Swal.fire('Error', res.message || 'Gagal memuat data', 'error');
                }
            });
        });

        $('#users-table').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus?',
                icon: 'warning',
                showCancelButton: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('superadmin/users/delete') ?>/' + id,
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Terhapus', 'User dihapus', 'success');
                                usersTable.ajax.reload();
                            }
                        }
                    });
                }
            });
        });

        // Initialize Select2 for bidang in the modal
        $('#modal-form-user [name="bidang_id"]').select2({
            dropdownParent: $('#modal-form-user'),
            ajax: {
                url: '<?= site_url('api/bidang/options') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                }
            },
            minimumInputLength: 0
        });

        // Submit form-user via AJAX
        $('#form-user').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '<?= site_url('superadmin/users/create') ?>',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Sukses', res.message, 'success');
                        $('#modal-form-user').addClass('hidden');
                        usersTable.ajax.reload();
                    } else {
                        Swal.fire('Error', res.message || 'Validasi gagal', 'error');
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>