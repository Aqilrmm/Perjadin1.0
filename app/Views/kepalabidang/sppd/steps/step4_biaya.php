<div>
    <form id="step4-form">
        <?= csrf_field() ?>
        <div>
            <label>Estimasi Biaya per Pegawai</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <input name="biaya_perjalanan" type="number" placeholder="Perjalanan" class="w-full border p-2 rounded">
                <input name="biaya_penginapan" type="number" placeholder="Penginapan" class="w-full border p-2 rounded">
            </div>
        </div>
        <div class="mt-3">
            <label>Jumlah Pegawai</label>
            <input name="jumlah_pegawai" type="number" class="w-full border p-2 rounded">
        </div>
        <div class="mt-3">
            <label>Total Estimasi</label>
            <div id="total-estimasi">Rp 0</div>
        </div>
    </form>
</div>