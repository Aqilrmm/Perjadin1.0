<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid md:grid-cols-3 gap-4">
    <div class="md:col-span-2 bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Documents</h3>
        <div class="tabs">
            <button class="px-3 py-1">SPPD</button>
            <button class="px-3 py-1">Nota Dinas</button>
            <button class="px-3 py-1">LPPD</button>
            <button class="px-3 py-1">Kwitansi</button>
        </div>
        <div class="mt-3">Embed / preview area (PDF / images)</div>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold">Verification</h3>
        <form method="post" action="/api/keuangan/verifikasi/approve/<?= $sppd_id ?? '' ?>">
            <?= csrf_field() ?>
            <div class="mt-2">
                <label>Checklist</label>
                <div class="mt-2">
                    <label><input type="checkbox" name="lppd_lengkap"> LPPD lengkap</label><br>
                    <label><input type="checkbox" name="kwitansi_lengkap"> Kwitansi lengkap</label>
                </div>
            </div>
            <div class="mt-3">
                <label>Catatan Verifikasi</label>
                <textarea name="catatan_verifikasi" class="w-full border p-2 rounded"></textarea>
            </div>
            <div class="mt-3 flex gap-2">
                <button class="px-3 py-1 border">Cancel</button>
                <button class="bg-green-600 text-white px-3 py-1 rounded">Submit Verifikasi</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>