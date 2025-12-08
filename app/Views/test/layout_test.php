<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto bg-white shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Layout Test</h1>

    <p class="mb-4">This page tests the main layout, assets, DataTables and Select2.</p>

    <div class="mb-6">
        <label class="block mb-2">Select2 (Bidang options)</label>
        <select id="test-select2" style="width: 100%"></select>
    </div>

    <table id="test-table" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Alice</td>
                <td>Admin</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Bob</td>
                <td>User</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Charlie</td>
                <td>Guest</td>
            </tr>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // DataTable init
        $('#test-table').DataTable();

        // Select2 init with AJAX (uses existing API endpoint)
        $('#test-select2').select2({
            placeholder: 'Pilih Bidang',
            ajax: {
                url: '/api/bidang/options',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    // Expecting array of {id, text}
                    return {
                        results: data
                    };
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>