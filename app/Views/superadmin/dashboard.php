<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded shadow">Total User<br><span class="text-2xl font-bold"><?= esc($statistics['total_users'] ?? 0) ?></span></div>
    <div class="bg-white p-4 rounded shadow">Total Bidang<br><span class="text-2xl font-bold"><?= esc($statistics['total_bidang'] ?? 0) ?></span></div>
    <div class="bg-white p-4 rounded shadow">SPPD Active<br><span class="text-2xl font-bold"><?= esc($statistics['total_sppd'] ?? 0) ?></span></div>
    <div class="bg-white p-4 rounded shadow">Total Program<br><span class="text-2xl font-bold"><?= esc($statistics['total_programs'] ?? 0) ?></span></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
    <div class="bg-white p-4 rounded shadow">Active Users<br><span class="text-2xl font-bold"><?= esc($statistics['active_users'] ?? 0) ?></span></div>
    <div class="bg-white p-4 rounded shadow">Blocked Users<br><span class="text-2xl font-bold"><?= esc($statistics['blocked_users'] ?? 0) ?></span></div>
    <div class="bg-white p-4 rounded shadow">Pending Programs<br><span class="text-2xl font-bold"><?= esc($statistics['pending_programs'] ?? 0) ?></span></div>
    <div class="bg-white p-4 rounded shadow">Pending SPPD<br><span class="text-2xl font-bold"><?= esc($statistics['pending_sppd'] ?? 0) ?></span></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    <div class="bg-white p-4 rounded shadow">
        <canvas id="chart-sppd-month"></canvas>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <canvas id="chart-sppd-bidang"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">SPPD by Bidang</h3>
        <?php if (! empty($sppd_by_bidang)): ?>
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th>Bidang</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sppd_by_bidang as $row): ?>
                        <?php $r = (array) $row; ?>
                        <tr>
                            <td><?= esc($r['nama_bidang'] ?? $r['bidang_nama'] ?? 'N/A') ?></td>
                            <td class="text-right"><?= esc($r['total'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-sm text-gray-500">No data</div>
        <?php endif; ?>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Recent Users</h3>
        <?php if (! empty($recent_users)): ?>
            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th>NIP/NIK</th>
                        <th>Nama</th>
                        <th>Bidang</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_users as $u): ?>
                        <?php $user = (array) $u; ?>
                        <tr>
                            <td><?= esc($user['nip_nik'] ?? '-') ?></td>
                            <td><?= esc($user['nama'] ?? '-') ?></td>
                            <td><?= esc($user['nama_bidang'] ?? '-') ?></td>
                            <td><?= esc($user['role'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-sm text-gray-500">No users</div>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white p-4 rounded shadow">
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
            <?php if (! empty($recent_activities)): ?>
                <?php foreach ($recent_activities as $act): ?>
                    <?php $row = (array) $act; ?>
                    <tr>
                        <td><?= esc($row['created_at'] ?? $row['createdAt'] ?? '-') ?></td>
                        <td><?= esc($row['nama'] ?? $row['nama_user'] ?? $row['user_name'] ?? '-') ?></td>
                        <td><?= esc($row['action'] ?? $row['activity'] ?? $row['description'] ?? '-') ?></td>
                        <td><?= esc($row['status'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-sm text-gray-500">No recent activities</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Provide initial chart data from server to JS (fallback to AJAX loader below)
        const initialSppdTrend = <?= json_encode($sppd_trend ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const initialSppdByBidang = <?= json_encode($sppd_by_bidang ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        // helper to render line chart
        function renderLineChart(ctxId, labels, data, labelText) {
            const ctx = document.getElementById(ctxId);
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelText,
                        data: data,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79,70,229,0.05)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // helper to render doughnut/pie chart
        function renderDoughnutChart(ctxId, labels, data) {
            const ctx = document.getElementById(ctxId);
            if (!ctx) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#4F46E5', '#06B6D4', '#10B981', '#F59E0B', '#EF4444', '#A78BFA']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Fetch chart data from controller route
        function loadChart(type, onSuccess) {
            axios.get('<?= base_url('superadmin/dashboard/chart-data') ?>', {
                    params: {
                        type
                    }
                })
                .then(res => {
                    // expect controller to return structured data; handle common shapes
                    if (res.data && res.data.data) {
                        onSuccess(res.data.data);
                    } else {
                        onSuccess(res.data);
                    }
                }).catch(err => {
                    console.error('Chart load error', err);
                });
        }

        // Load SPPD trend (last months)
        if (initialSppdTrend && initialSppdTrend.length) {
            const labels = initialSppdTrend.map(d => d.month);
            const counts = initialSppdTrend.map(d => d.count);
            renderLineChart('chart-sppd-month', labels, counts, 'SPPD per month');
        } else {
            loadChart('sppd_trend', function(data) {
                const labels = data.map(d => d.month);
                const counts = data.map(d => d.count);
                renderLineChart('chart-sppd-month', labels, counts, 'SPPD per month');
            });
        }

        // Load SPPD by bidang
        if (initialSppdByBidang && initialSppdByBidang.length) {
            const labels = initialSppdByBidang.map(d => d.nama_bidang ?? d.bidang_nama ?? d.name ?? 'N/A');
            const counts = initialSppdByBidang.map(d => parseInt(d.total ?? d.count ?? 0, 10));
            renderDoughnutChart('chart-sppd-bidang', labels, counts);
        } else {
            loadChart('sppd_by_bidang', function(data) {
                const labels = data.map(d => d.nama_bidang ?? d.bidang_nama ?? d.name ?? 'N/A');
                const counts = data.map(d => parseInt(d.total ?? d.count ?? 0, 10));
                renderDoughnutChart('chart-sppd-bidang', labels, counts);
            });
        }
    });
</script>
<?= $this->endSection() ?>