<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Perjadin') ?></title>
    <!-- Core CSS -->
    <link href="/assets/css/tailwind.css" rel="stylesheet">

    <!-- DataTables CSS: CDN with local fallback -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="/assets/vendors/datatables/datatables.min.css" rel="stylesheet">

    <!-- Select2 CSS: CDN with local fallback -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="/assets/vendors/select2/select2.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- JS Libraries (CDN first, local fallback) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        window.jQuery || document.write('\x3Cscript src="/assets/vendors/jquery/jquery.min.js">\x3C/script>')
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        window.Swal || document.write('\x3Cscript src="/assets/vendors/sweetalert2/sweetalert2.min.js">\x3C/script>')
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        typeof $.fn.dataTable === 'undefined' && document.write('\x3Cscript src="/assets/vendors/datatables/datatables.min.js">\x3C/script>')
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        typeof $.fn.select2 === 'undefined' && document.write('\x3Cscript src="/assets/vendors/select2/select2.min.js">\x3C/script>')
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        window.Chart || document.write('\x3Cscript src="/assets/vendors/chartjs/chart.umd.js">\x3C/script>')
    </script>
    <script>
        window.csrfName = '<?= csrf_token() ?>';
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <?= $this->include('layouts/components/sidebar') ?>
    <div class="flex-1 min-h-screen">
        <?= $this->include('layouts/components/topbar') ?>
        <main class="p-6">
            <?= $this->renderSection('content') ?>
        </main>
        <?= $this->include('layouts/components/footer') ?>
    </div>
    <script src="/assets/js/app.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>