<div id="modal-form-program" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-2xl">
        <h3 class="font-semibold mb-3">Ajukan Program</h3>
        <form id="form-program" method="post" action="<?= site_url('kepalabidang/programs/create') ?>">
            <?= csrf_field() ?>
            <div id="form-program-errors" class="mb-3"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label for="kode_program">Kode Program</label>
                    <input id="kode_program" name="kode_program" class="w-full border p-2 rounded">
                    <div class="text-sm text-red-600 mt-1 form-error" data-field="kode_program"></div>
                </div>
                <div>
                    <label for="nama_program">Nama Program</label>
                    <input id="nama_program" name="nama_program" class="w-full border p-2 rounded">
                    <div class="text-sm text-red-600 mt-1 form-error" data-field="nama_program"></div>
                </div>
                <div>
                    <label for="tahun_anggaran">Tahun Anggaran</label>
                    <input id="tahun_anggaran" name="tahun_anggaran" type="number" class="w-full border p-2 rounded">
                    <div class="text-sm text-red-600 mt-1 form-error" data-field="tahun_anggaran"></div>
                </div>
                <div>
                    <label for="jumlah_anggaran">Jumlah Anggaran</label>
                    <input id="jumlah_anggaran" name="jumlah_anggaran" type="number" class="w-full border p-2 rounded">
                    <div class="text-sm text-red-600 mt-1 form-error" data-field="jumlah_anggaran"></div>
                </div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button type="button" class="btn-cancel-program px-3 py-1 border">Batal</button>
                <button id="btn-save-program" type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>