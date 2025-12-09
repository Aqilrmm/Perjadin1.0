<div id="modal-form-user" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center">
    <div class="bg-white w-full max-w-2xl p-6 rounded">
        <h3 class="text-lg font-semibold mb-4">Tambah / Edit User</h3>
        <form id="form-user" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="" />
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
                    <label>Gelar Depan</label>
                    <input name="gelar_depan" class="w-full border p-2 rounded">
                </div>

                <div>
                    <label>Gelar Belakang</label>
                    <input name="gelar_belakang" class="w-full border p-2 rounded">
                </div>

                <div>
                    <label>Email*</label>
                    <input name="email" type="email" class="w-full border p-2 rounded" required>
                </div>

                <div>
                    <label>Password</label>
                    <input name="password" type="password" class="w-full border p-2 rounded" placeholder="Kosongkan jika tidak diubah">
                </div>

                <div>
                    <label>Jenis Pegawai</label>
                    <select name="jenis_pegawai" class="w-full border p-2 rounded">
                        <option value="">Pilih</option>
                        <option value="ASN">ASN</option>
                        <option value="Non-ASN">Non-ASN</option>
                    </select>
                </div>

                <div>
                    <label>Jabatan</label>
                    <input name="jabatan" class="w-full border p-2 rounded">
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

                <div>
                    <label>Foto Profil</label>
                    <input name="foto" type="file" accept="image/*" class="w-full">
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="$('#modal-form-user').addClass('hidden')" class="px-3 py-1 border rounded">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>