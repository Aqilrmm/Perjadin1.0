<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Login - Perjadin') ?></title>
    <link href="/assets/css/tailwind.css" rel="stylesheet">
    <script src="/assets/vendors/jquery/jquery.min.js"></script>
    <script src="/assets/vendors/sweetalert2/sweetalert2.min.js"></script>
    <script src="/assets/js/auth.js"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
    <div class="w-full max-w-md">
        <?= $this->renderSection('content') ?>
    </div>
    <?= $this->renderSection('scripts') ?>
</body>

</html>