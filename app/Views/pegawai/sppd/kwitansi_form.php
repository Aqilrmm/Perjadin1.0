<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold">Isi Kwitansi</h2>
    <form method="post" action="/api/pegawai/kwitansi/save/<?= $sppd_id ?? '' ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div>
            <label>Tipe Perjalanan</label>
            <div class="mt-1">Dalam Daerah / Luar Daerah</div>
        </div>
        <div class="mt-3">
            <label>Biaya Perjalanan</label>
            <input type="number" name="biaya_perjalanan" class="w-full border p-2 rounded">
        </div>
        <div class="mt-3">
            <label>Upload Bukti (max 2MB per file)</label>
            <input type="file" name="bukti[]" multiple accept="image/*,application/pdf" class="w-full">
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button class="px-3 py-1 border" type="button" onclick="window.history.back()">Batal</button>
            <button class="bg-blue-600 text-white px-3 py-1 rounded" type="submit">Simpan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>