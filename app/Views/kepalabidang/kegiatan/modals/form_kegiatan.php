<div id="modal-form-kegiatan" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-2xl">
        <h3 class="font-semibold mb-3">Buat Kegiatan</h3>
        <form id="form-kegiatan" method="post">
            <?= csrf_field() ?>
            <div>
                <label>Pilih Program</label>
                <select name="program_id" class="w-full border p-2 rounded"></select>
            </div>
            <div class="mt-3">
                <label>Nama Kegiatan</label>
                <input name="nama_kegiatan" class="w-full border p-2 rounded">
            </div>
            <div class="mt-3">
                <label>Anggaran Kegiatan</label>
                <input name="anggaran_kegiatan" type="number" class="w-full border p-2 rounded">
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button type="button" onclick="$('#modal-form-kegiatan').hide()" class="px-3 py-1 border">Batal</button>
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>