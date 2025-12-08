<div>
    <form id="step3-form">
        <?= csrf_field() ?>
        <div>
            <label>Penanggung Jawab</label>
            <input name="penanggung_jawab" class="w-full border p-2 rounded" readonly value="<?= esc(session('nama') ?? '') ?>">
        </div>
        <div class="mt-3">
            <label>Daftar Pegawai (multi-select)</label>
            <select name="pegawai_ids[]" multiple class="w-full border p-2 rounded select2"></select>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('select[name="pegawai_ids[]"]').select2({
            ajax: {
                url: '<?= site_url('api/pegawai/search') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    // Expect API to return array of {id, text}
                    return {
                        results: data.results || data
                    };
                }
            },
            minimumInputLength: 1,
            placeholder: 'Pilih pegawai'
        });
    });
</script>