<div>
    <form id="step1-form">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label>Program*</label>
                <select name="program_id" id="program-select" class="w-full border p-2 rounded select2">
                    <option value="">Pilih Program</option>
                </select>
            </div>
            <div>
                <label>Kegiatan*</label>
                <select name="kegiatan_id" id="kegiatan-select" class="w-full border p-2 rounded select2">
                    <option value="">Pilih Kegiatan</option>
                </select>
            </div>
            <div>
                <label>Sub Kegiatan*</label>
                <select name="sub_kegiatan_id" id="subkegiatan-select" class="w-full border p-2 rounded select2">
                    <option value="">Pilih Sub Kegiatan</option>
                </select>
            </div>
            <div>
                <label>Sisa Anggaran</label>
                <div id="sisa-anggaran" class="p-2">Rp 0</div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        function formatRupiah(num) {
            if (num === null || num === undefined) return 'Rp 0';
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        $('#program-select').select2({
            ajax: {
                url: '<?= site_url('api/programs/options') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                }
            },
            minimumInputLength: 0,
            placeholder: 'Pilih Program'
        });

        $('#kegiatan-select').select2({
            ajax: {
                url: '<?= site_url('api/kegiatan/options') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        program_id: $('#program-select').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                }
            },
            minimumInputLength: 0,
            placeholder: 'Pilih Kegiatan'
        });

        $('#subkegiatan-select').select2({
            ajax: {
                url: '<?= site_url('api/subkegiatan/options') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        kegiatan_id: $('#kegiatan-select').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                }
            },
            minimumInputLength: 0,
            placeholder: 'Pilih Sub Kegiatan'
        });

        // Cascade selects
        $('#program-select').on('change', function() {
            $('#kegiatan-select').val(null).trigger('change');
            $('#subkegiatan-select').val(null).trigger('change');
            $('#sisa-anggaran').text('Rp 0');
        });

        $('#kegiatan-select').on('change', function() {
            $('#subkegiatan-select').val(null).trigger('change');
            $('#sisa-anggaran').text('Rp 0');
        });

        // When subkegiatan selected, call validation endpoint to fetch sisa anggaran
        $('#subkegiatan-select').on('change', function() {
            var subId = $(this).val();
            if (!subId) return;
            $.post('<?= site_url('kepalabidang/sppd/validate-step1') ?>', {
                    sub_kegiatan_id: subId
                })
                .done(function(res) {
                    if (res.status) {
                        var sisa = res.data.sisa_anggaran ?? res.data.sisa_anggaran;
                        $('#sisa-anggaran').text(formatRupiah(sisa));
                    } else {
                        $('#sisa-anggaran').text('Rp 0');
                    }
                }).fail(function() {
                    $('#sisa-anggaran').text('Rp 0');
                });
        });
    });
</script>