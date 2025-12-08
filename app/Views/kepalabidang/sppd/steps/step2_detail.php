<div>
    <form id="step2-form">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 gap-3">
            <div>
                <label>Tipe Perjalanan*</label>
                <select name="tipe_perjalanan" class="w-full border p-2 rounded">
                    <option value="Dalam Daerah">Dalam Daerah</option>
                    <option value="Luar Daerah Dalam Provinsi">Luar Daerah Dalam Provinsi</option>
                    <option value="Luar Daerah Luar Provinsi">Luar Daerah Luar Provinsi</option>
                </select>
            </div>
            <div>
                <label>Maksud Perjalanan*</label>
                <textarea name="maksud_perjalanan" class="w-full border p-2 rounded" rows="4"></textarea>
            </div>
            <div>
                <label>Dasar Surat (Nomor)</label>
                <input name="dasar_surat" class="w-full border p-2 rounded">
            </div>
        </div>
    </form>
</div>