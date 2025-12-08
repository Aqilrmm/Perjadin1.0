<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold">Isi LPPD</h2>
    <form method="post" action="/api/pegawai/lppd/save/<?= $sppd_id ?? '' ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mt-3">
            <label>Hasil Kegiatan*</label>
            <textarea name="hasil_kegiatan" class="w-full border p-2 rounded" required minlength="50"></textarea>
        </div>
        <div class="mt-3">
            <label>Upload Dokumentasi Foto* (min 1)</label>
            <input type="file" name="dokumentasi[]" multiple accept="image/*" class="w-full">
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button class="px-3 py-1 border" type="button" onclick="window.history.back()">Batal</button>
            <button class="bg-blue-600 text-white px-3 py-1 rounded" type="submit">Submit</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>