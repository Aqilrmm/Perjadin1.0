<div id="modal-form-subkegiatan" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-2xl">
        <h3 class="font-semibold mb-3">Buat Sub Kegiatan</h3>
        <form id="form-subkegiatan" method="post" action="<?= site_url('kepalabidang/subkegiatan/create') ?>">
            <?= csrf_field() ?>
            <div>
                <label>Pilih Kegiatan</label>
                <select id="kegiatan-select" name="kegiatan_id" class="w-full border p-2 rounded"></select>
                <div class="text-sm text-red-600 mt-1 form-error" data-field="kegiatan_id"></div>
            </div>

            <div class="mt-3">
                <label>Kode Sub Kegiatan</label>
                <input id="kode-subkegiatan" name="kode_sub_kegiatan" class="w-full border p-2 rounded" placeholder="AUTO jika kosong">
                <div class="text-sm text-red-600 mt-1 form-error" data-field="kode_sub_kegiatan"></div>
            </div>

            <div class="mt-3">
                <label>Nama Sub Kegiatan</label>
                <input id="nama-subkegiatan" name="nama_sub_kegiatan" class="w-full border p-2 rounded">
                <div class="text-sm text-red-600 mt-1 form-error" data-field="nama_sub_kegiatan"></div>
            </div>

            <div class="mt-3">
                <label>Anggaran Sub Kegiatan</label>
                <input id="anggaran-subkegiatan" name="anggaran_sub_kegiatan" type="number" class="w-full border p-2 rounded">
                <div class="text-sm text-red-600 mt-1 form-error" data-field="anggaran_sub_kegiatan"></div>
            </div>

            <div class="mt-3">
                <label>Deskripsi</label>
                <textarea id="deskripsi-subkegiatan" name="deskripsi" class="w-full border p-2 rounded" rows="3"></textarea>
                <div class="text-sm text-red-600 mt-1 form-error" data-field="deskripsi"></div>
            </div>

            <div class="mt-3 flex justify-between items-center gap-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="save_as_draft" value="true" class="form-checkbox">
                    <span class="text-sm">Simpan sebagai draft</span>
                </label>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-cancel-subkegiatan px-3 py-1 border">Batal</button>
                    <button id="btn-save-subkegiatan" class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>