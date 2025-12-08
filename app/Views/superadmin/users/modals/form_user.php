<div id="modal-form-user" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white w-full max-w-2xl p-6 rounded">
        <h3 class="text-lg font-semibold mb-4">Tambah / Edit User</h3>
        <form id="form-user" method="post" enctype="multipart/form-data" action="<?= site_url('superadmin/users/create') ?>">
            <?= csrf_field() ?>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label>NIP/NIK*</label>
                    <input name="nip_nik" class="w-full border p-2 rounded" required>
                </div>
                <div>
                    <label>Nama*</label>
                    <input name="nama" class="w-full border p-2 rounded" required>
                </div>
                <div>
                    <label>Email*</label>
                    <input name="email" type="email" class="w-full border p-2 rounded" required>
                </div>
                <div>
                    <label>Role*</label>
                    <select name="role" class="w-full border p-2 rounded">
                        <option value="superadmin">Super Admin</option>
                        <option value="kepaladinas">Kepala Dinas</option>
                        <option value="kepalabidang">Kepala Bidang</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="keuangan">Keuangan</option>
                    </select>
                </div>
                <div>
                    <label>Bidang</label>
                    <select name="bidang_id" class="w-full border p-2 rounded">
                        <option value="">Pilih Bidang</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="$('#modal-form-user').hide()" class="px-3 py-1">Batal</button>
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>