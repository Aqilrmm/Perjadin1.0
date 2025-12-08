<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded shadow">Total User<br><span class="text-2xl font-bold">1.234</span></div>
    <div class="bg-white p-4 rounded shadow">Total Bidang<br><span class="text-2xl font-bold">12</span></div>
    <div class="bg-white p-4 rounded shadow">SPPD Active<br><span class="text-2xl font-bold">45</span></div>
    <div class="bg-white p-4 rounded shadow">Total Program<br><span class="text-2xl font-bold">78</span></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    <div class="bg-white p-4 rounded shadow">
        <canvas id="chart-sppd-month"></canvas>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <canvas id="chart-sppd-bidang"></canvas>
    </div>
</div>

<div class="bg-white p-4 rounded shadow mt-6">
    <h3 class="font-semibold mb-2">Recent Activities</h3>
    <table class="min-w-full" id="recent-activities-table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>User</th>
                <th>Action</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2025-01-01 10:00</td>
                <td>Admin</td>
                <td>Login</td>
                <td><span class="text-green-600">Success</span></td>
            </tr>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const ctx = document.getElementById('chart-sppd-month');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar'],
                datasets: [{
                    label: 'SPPD',
                    data: [3, 5, 2],
                    borderColor: '#4F46E5'
                }]
            }
        });
    }
</script>
<?= $this->endSection() ?>