<div id="modal-reject-form" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-md">
        <h3 class="font-semibold mb-2">Alasan Penolakan</h3>
        <form id="form-reject" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <textarea name="catatan" rows="4" class="w-full border p-2 rounded" placeholder="Masukkan alasan (min 10 karakter)"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="$('#modal-reject-form').hide()" class="px-3 py-1 border">Batal</button>
                <button class="bg-red-600 text-white px-3 py-1 rounded">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>