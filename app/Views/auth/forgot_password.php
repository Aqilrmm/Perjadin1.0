<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Lupa Password</h2>
    <form method="post" action="#">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Email</label>
            <input name="email" type="email" class="w-full border p-2 rounded">
        </div>
        <div class="mt-4">
            <button class="w-full bg-blue-600 text-white py-2 rounded">Kirim Reset Link</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>