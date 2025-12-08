<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Login - Perjadin') ?></title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery CDN (resmi) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-+0xYsi2JxK2NN1JtSPDh8a/mQPEd6G/Jw7P3F6t8N3Q="
            crossorigin="anonymous"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- auth.js (tetap lokal karena file custom kamu) -->
    <script src="/assets/js/auth.js"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
    <div class="w-full max-w-md">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->renderSection('scripts') ?>
</body>

</html>
