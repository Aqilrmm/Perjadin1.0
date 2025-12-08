<div id="modal-form-program" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-2xl">
        <h3 class="font-semibold mb-3">Ajukan Program</h3>
        <form id="form-program" method="post">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label>Kode Program</label>
                    <input name="kode_program" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label>Nama Program</label>
                    <input name="nama_program" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label>Tahun Anggaran</label>
                    <input name="tahun_anggaran" type="number" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label>Jumlah Anggaran</label>
                    <input name="jumlah_anggaran" type="number" class="w-full border p-2 rounded">
                </div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button type="button" onclick="$('#modal-form-program').hide()" class="px-3 py-1 border">Batal</button>
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>