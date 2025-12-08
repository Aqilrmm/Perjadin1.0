<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="flex items-center justify-center h-64">
    <div class="text-center">
        <h1 class="text-6xl font-bold">404</h1>
        <p class="mt-2">Halaman tidak ditemukan.</p>
        <a href="/" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">Kembali ke Dashboard</a>
    </div>
</div>
<?= $this->endSection() ?>