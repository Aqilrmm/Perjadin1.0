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

    <?= $this->include('kepalabidang/programs/modals/form_program') ?>
    <?= $this->include('kepalabidang/programs/modals/detail_program') ?>

    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script>
        $(function() {
            // DataTable init
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
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ]
            });

            // Open modal
            $('#btn-new-program').on('click', function(e) {
                e.preventDefault();
                $('#modal-form-program').removeClass('hidden');
            });

            // Close modal (delegated in case modal is loaded later)
            $(document).on('click', '.btn-cancel-program', function(e) {
                e.preventDefault();
                $('#modal-form-program').addClass('hidden');
            });

            // Optional: close modal when backdrop clicked
            $(document).on('click', '#modal-form-program', function(e) {
                if (e.target.id === 'modal-form-program') {
                    $('#modal-form-program').addClass('hidden');
                }
            });

            // View detail (open detail modal)
            $('#programs-table').on('click', '.btn-view', function() {
                var id = $(this).data('id');
                if (!id) return;

                showLoading('Memuat detail...');
                fetch('<?= site_url('kepalabidang/programs/get') ?>/' + id, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(res) {
                    return res.json();
                }).then(function(json) {
                    hideLoading();
                    if (json.status) {
                        var d = json.data;
                        var html = '<div class="space-y-2">';
                        html += '<p><strong>Kode:</strong> ' + (d.kode_program || d.kode || '-') + '</p>';
                        html += '<p><strong>Nama:</strong> ' + (d.nama_program || d.nama || '-') + '</p>';
                        html += '<p><strong>Tahun:</strong> ' + (d.tahun_anggaran || d.tahun || '-') + '</p>';
                        html += '<p><strong>Jumlah Anggaran:</strong> ' + (d.jumlah_anggaran || '-') + '</p>';
                        html += '<p><strong>Sisa Anggaran:</strong> ' + (d.sisa_anggaran || '-') + '</p>';
                        html += '<p><strong>Status:</strong> ' + (d.status || '-') + '</p>';
                        html += '<p><strong>Deskripsi:</strong><br/>' + (d.deskripsi || '-') + '</p>';
                        html += '</div>';

                        $('#detail-program-content').html(html);
                        $('#modal-detail-program').removeClass('hidden');
                    } else {
                        showToast(json.message || 'Gagal memuat detail', 'error');
                    }
                }).catch(function(err) {
                    hideLoading();
                    console.error(err);
                    showToast('Gagal memuat detail', 'error');
                });
            });

            // Close detail modal
            $(document).on('click', '.btn-close-detail', function() {
                $('#modal-detail-program').addClass('hidden');
            });

            // Close detail modal when clicking backdrop
            $(document).on('click', '#modal-detail-program', function(e) {
                if (e.target.id === 'modal-detail-program') {
                    $('#modal-detail-program').addClass('hidden');
                }
            });

            // Handle form submit via fetch (JSON response expected)
            $(document).on('submit', '#form-program', function(e) {
                e.preventDefault();

                // clear previous errors
                $('#form-program-errors').empty();
                $('.form-error').text('');

                const form = this;
                const btn = document.getElementById('btn-save-program');
                btn.disabled = true;
                btn.classList.add('btn-loading');

                const formData = new FormData(form);

                // Ensure CSRF token included (meta contains hash, and csrf_token() gives name)
                const csrfName = '<?= csrf_token() ?>';
                const csrfHash = document.querySelector('meta[name="csrf-token"]').content;
                if (!formData.has(csrfName)) {
                    formData.append(csrfName, csrfHash);
                }

                showLoading('Menyimpan program...');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(async (res) => {
                    const json = await res.json().catch(() => ({}));
                    if (res.status === 422) {
                        // Validation errors
                        const errors = json.data || json.errors || json || {};
                        // If errors is an object of arrays/object, render per field
                        if (typeof errors === 'object') {
                            Object.keys(errors).forEach(function(key) {
                                const msg = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
                                $(`.form-error[data-field="${key}"]`).text(msg);
                            });
                            $('#form-program-errors').html('<div class="text-sm text-red-600">Periksa form kembali.</div>');
                        } else {
                            $('#form-program-errors').html('<div class="text-sm text-red-600">Validasi gagal.</div>');
                        }
                    } else if (res.ok) {
                        // Success
                        const message = json.message || json.data?.message || 'Program berhasil disimpan';
                        showToast(message, 'success');
                        $('#modal-form-program').addClass('hidden');
                        // reset form
                        form.reset();
                        // reload table
                        try {
                            $('#programs-table').DataTable().ajax.reload(null, false);
                        } catch (err) {
                            // ignore
                        }
                    } else {
                        // Other error
                        const msg = json.message || 'Terjadi kesalahan pada server';
                        showToast(msg, 'error');
                    }
                }).catch((err) => {
                    console.error(err);
                    showToast('Gagal menghubungi server', 'error');
                }).finally(() => {
                    btn.disabled = false;
                    btn.classList.remove('btn-loading');
                    hideLoading();
                });
            });
        });
    </script>
    <?= $this->endSection() ?>