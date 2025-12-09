<div id="modal-form-kegiatan" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-2xl">
        <h3 class="font-semibold mb-3">Buat Kegiatan</h3>
        <form id="form-kegiatan" method="post" action="<?= site_url('kepalabidang/kegiatan/create') ?>">
            <?= csrf_field() ?>
            <div>
                <label>Pilih Program</label>
                <select id="program-select" name="program_id" class="w-full border p-2 rounded"></select>
                <div class="text-sm text-red-600 mt-1 form-error" data-field="program_id"></div>
            </div>

            <div class="mt-3">
                <label>Kode Kegiatan</label>
                <input id="kode-kegiatan" name="kode_kegiatan" class="w-full border p-2 rounded" placeholder="AUTO jika kosong">
                <div class="text-sm text-red-600 mt-1 form-error" data-field="kode_kegiatan"></div>
            </div>

            <div class="mt-3">
                <label>Nama Kegiatan</label>
                <input id="nama-kegiatan" name="nama_kegiatan" class="w-full border p-2 rounded">
                <div class="text-sm text-red-600 mt-1 form-error" data-field="nama_kegiatan"></div>
            </div>

            <div class="mt-3">
                <label>Anggaran Kegiatan</label>
                <input id="anggaran-kegiatan" name="anggaran_kegiatan" type="number" class="w-full border p-2 rounded">
                <div class="text-sm text-red-600 mt-1 form-error" data-field="anggaran_kegiatan"></div>
            </div>

            <div class="mt-3">
                <label>Deskripsi</label>
                <textarea id="deskripsi-kegiatan" name="deskripsi" class="w-full border p-2 rounded" rows="4"></textarea>
                <div class="text-sm text-red-600 mt-1 form-error" data-field="deskripsi"></div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="save_as_draft" value="true" class="form-checkbox">
                    <span class="text-sm">Simpan sebagai draft</span>
                </label>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-cancel-kegiatan px-3 py-1 border">Batal</button>
                    <button id="btn-save-kegiatan" class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>