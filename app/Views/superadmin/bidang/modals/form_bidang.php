<div id="modal-form-bidang" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-lg">
        <h3 class="font-semibold mb-4">Tambah / Edit Bidang</h3>
        <form id="form-bidang" method="post" action="#">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label>Kode Bidang</label>
                <input name="kode_bidang" class="w-full border p-2 rounded">
            </div>
            <div class="mb-3">
                <label>Nama Bidang</label>
                <input name="nama_bidang" class="w-full border p-2 rounded">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="$('#modal-form-bidang').addClass('hidden')" class="px-3 py-1 border">Batal</button>
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>