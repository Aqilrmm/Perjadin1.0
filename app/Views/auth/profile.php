<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="bg-white p-4 rounded shadow max-w-2xl">
    <h2 class="text-lg font-semibold mb-4">Profile</h2>
    <form method="post" action="#" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label>Nama</label>
                <input name="nama" class="w-full border p-2 rounded">
            </div>
            <div>
                <label>Email</label>
                <input name="email" type="email" class="w-full border p-2 rounded">
            </div>
            <div>
                <label>Foto</label>
                <input type="file" name="foto" class="w-full">
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button class="px-3 py-1 border">Batal</button>
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>