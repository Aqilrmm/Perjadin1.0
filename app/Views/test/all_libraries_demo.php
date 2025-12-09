<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Semua Library Perjadin</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Dropzone.js -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@6/dist/dropzone.css">
    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Intro.js -->
    <link href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <h1 class="text-3xl font-bold mb-6">Demo Semua Library Perjadin</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- DataTables & Select2 -->
        <div class="bg-white p-6 rounded shadow" data-aos="fade-up">
            <h2 class="text-xl font-semibold mb-4">DataTables & Select2</h2>
            <select id="demo-select2" style="width:100%" multiple>
                <option value="1">Bidang 1</option>
                <option value="2">Bidang 2</option>
                <option value="3">Bidang 3</option>
            </select>
            <table id="demo-table" class="display mt-4" style="width:100%">
                <thead>
                    <tr><th>Nama</th><th>Role</th></tr>
                </thead>
                <tbody>
                    <tr><td>Aqil</td><td>Super Admin</td></tr>
                    <tr><td>Budi</td><td>Pegawai</td></tr>
                </tbody>
            </table>
        </div>
        <!-- Flatpickr, Cleave.js, Day.js -->
        <div class="bg-white p-6 rounded shadow" data-aos="fade-up" data-aos-delay="100">
            <h2 class="text-xl font-semibold mb-4">Flatpickr, Cleave.js, Day.js</h2>
            <input id="demo-date" class="border p-2 rounded w-full mb-2" placeholder="Pilih tanggal">
            <input id="demo-rupiah" class="border p-2 rounded w-full mb-2" placeholder="Input Rupiah">
            <div id="demo-dayjs" class="mt-2 text-gray-700"></div>
        </div>
        <!-- Chart.js -->
        <div class="bg-white p-6 rounded shadow" data-aos="fade-up" data-aos-delay="200">
            <h2 class="text-xl font-semibold mb-4">Chart.js</h2>
            <canvas id="demo-chart" width="400" height="200"></canvas>
        </div>
        <!-- Dropzone.js -->
        <div class="bg-white p-6 rounded shadow" data-aos="fade-up" data-aos-delay="300">
            <h2 class="text-xl font-semibold mb-4">Dropzone.js</h2>
            <form action="/upload" class="dropzone" id="demo-dropzone"></form>
        </div>
        <!-- SweetAlert2, Toastify, Axios -->
        <div class="bg-white p-6 rounded shadow" data-aos="fade-up" data-aos-delay="400">
            <h2 class="text-xl font-semibold mb-4">SweetAlert2, Toastify, Axios</h2>
            <button id="demo-swal" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Show Alert</button>
            <button id="demo-toast" class="bg-green-500 text-white px-4 py-2 rounded">Show Toast</button>
            <button id="demo-axios" class="bg-purple-500 text-white px-4 py-2 rounded mt-2">Get Data (Axios)</button>
            <div id="demo-axios-result" class="mt-2 text-gray-700"></div>
        </div>
        <!-- Lodash, jQuery Validation, Intro.js -->
        <div class="bg-white p-6 rounded shadow" data-aos="fade-up" data-aos-delay="500">
            <h2 class="text-xl font-semibold mb-4">Lodash, jQuery Validation, Intro.js</h2>
            <form id="demo-form">
                <input name="email" class="border p-2 rounded w-full mb-2" placeholder="Email">
                <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded">Submit</button>
            </form>
            <button id="demo-intro" class="bg-yellow-500 text-white px-4 py-2 rounded mt-2">Start Intro.js</button>
            <div id="demo-lodash" class="mt-2 text-gray-700"></div>
        </div>
    </div>
    <footer class="mt-12 text-center text-gray-500">Demo ini hanya untuk dokumentasi. Backend (mPDF, PHPMailer, PhpSpreadsheet) digunakan di Controller, bukan di halaman ini.</footer>
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.10/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/dropzone@6"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>
    <script>
        // DataTables & Select2
        $(document).ready(function() {
            $('#demo-table').DataTable();
            $('#demo-select2').select2();
        });
        // Flatpickr
        flatpickr('#demo-date', {dateFormat: 'd-m-Y'});
        // Cleave.js
        new Cleave('#demo-rupiah', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            prefix: 'Rp ',
            rawValueTrimPrefix: true
        });
        // Day.js
        document.getElementById('demo-dayjs').innerText = 'Tanggal sekarang: ' + dayjs().format('DD MMMM YYYY');
        // Chart.js
        new Chart(document.getElementById('demo-chart'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar'],
                datasets: [{ label: 'SPPD', data: [12, 19, 3], backgroundColor: '#3b82f6' }]
            }
        });
        // Dropzone.js
        new Dropzone('#demo-dropzone', { url: '/upload', autoProcessQueue: false });
        // SweetAlert2
        document.getElementById('demo-swal').onclick = function() {
            Swal.fire({ title: 'Demo Alert', text: 'Ini dari SweetAlert2!', icon: 'info' });
        };
        // Toastify
        document.getElementById('demo-toast').onclick = function() {
            Toastify({ text: 'Ini notifikasi Toastify!', duration: 3000, gravity: 'top', position: 'right' }).showToast();
        };
        // Axios
        document.getElementById('demo-axios').onclick = function() {
            axios.get('https://jsonplaceholder.typicode.com/users/1').then(function(res) {
                document.getElementById('demo-axios-result').innerText = 'Nama: ' + res.data.name;
            });
        };
        // Lodash
        document.getElementById('demo-lodash').innerText = 'Unique: ' + _.uniq([1,2,2,3,4,4,5]).join(', ');
        // jQuery Validation
        $('#demo-form').validate({ rules: { email: { required: true, email: true } } });
        // Intro.js
        document.getElementById('demo-intro').onclick = function() {
            introJs().setOptions({ steps: [
                { element: '#demo-table', intro: 'Ini adalah tabel DataTables.' },
                { element: '#demo-select2', intro: 'Ini adalah dropdown Select2.' },
                { element: '#demo-date', intro: 'Ini adalah input tanggal Flatpickr.' }
            ] }).start();
        };
        // AOS
        AOS.init();
    </script>
</body>
</html>
