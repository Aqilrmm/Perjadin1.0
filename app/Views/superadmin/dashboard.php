<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Users Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total User</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= esc($statistics['total_users'] ?? 0) ?></h3>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-users text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Bidang Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Bidang</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= esc($statistics['total_bidang'] ?? 0) ?></h3>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-building text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total SPPD Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">SPPD Active</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= esc($statistics['total_sppd'] ?? 0) ?></h3>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-plane text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Program Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Program</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= esc($statistics['total_programs'] ?? 0) ?></h3>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-tasks text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Active Users Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Active Users</p>
                <h3 class="text-3xl font-bold text-green-600"><?= esc($statistics['active_users'] ?? 0) ?></h3>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-user-check text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Blocked Users Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Blocked Users</p>
                <h3 class="text-3xl font-bold text-red-600"><?= esc($statistics['blocked_users'] ?? 0) ?></h3>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-user-lock text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending Programs Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Pending Programs</p>
                <h3 class="text-3xl font-bold text-orange-600"><?= esc($statistics['pending_programs'] ?? 0) ?></h3>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-clock text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending SPPD Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 card-hover" data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Pending SPPD</p>
                <h3 class="text-3xl font-bold text-indigo-600"><?= esc($statistics['pending_sppd'] ?? 0) ?></h3>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
                <i class="fas fa-hourglass-half text-indigo-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- SPPD Trend Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6" data-aos="fade-up">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>Trend SPPD per Bulan
        </h3>
        <div style="position: relative; height: 300px;">
            <canvas id="chart-sppd-month"></canvas>
        </div>
    </div>

    <!-- SPPD by Bidang Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6" data-aos="fade-up" data-aos-delay="100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-pie mr-2 text-purple-600"></i>SPPD per Bidang
        </h3>
        <div style="position: relative; height: 300px;">
            <canvas id="chart-sppd-bidang"></canvas>
        </div>
    </div>
</div>

<!-- Data Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- SPPD by Bidang Table -->
    <div class="bg-white rounded-lg shadow-sm p-6" data-aos="fade-up">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-table mr-2 text-green-600"></i>SPPD by Bidang
        </h3>
        <div class="overflow-x-auto">
            <?php if (!empty($sppd_by_bidang)): ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bidang</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($sppd_by_bidang as $row): ?>
                            <?php $r = (array) $row; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($r['nama_bidang'] ?? $r['bidang_nama'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-semibold"><?= esc($r['total'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada data SPPD</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="bg-white rounded-lg shadow-sm p-6" data-aos="fade-up" data-aos-delay="100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-user-plus mr-2 text-blue-600"></i>Pengguna Terbaru
        </h3>
        <div class="overflow-x-auto">
            <?php if (!empty($recent_users)): ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP/NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recent_users as $u): ?>
                            <?php $user = (array) $u; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($user['nip_nik'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= esc($user['nama'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= get_role_badge_class($user['role'] ?? '') ?>">
                                        <?= esc(ucfirst($user['role'] ?? '-')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada pengguna</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Activities Table -->
<div class="bg-white rounded-lg shadow-sm p-6" data-aos="fade-up">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-history mr-2 text-purple-600"></i>Aktivitas Terbaru
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="recent-activities-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($recent_activities)): ?>
                    <?php foreach ($recent_activities as $act): ?>
                        <?php $row = (array) $act; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                <i class="far fa-clock mr-1 text-gray-400"></i>
                                <?= esc($row['created_at'] ?? $row['createdAt'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <?= esc($row['nama'] ?? $row['nama_user'] ?? $row['user_name'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <?= esc($row['action'] ?? $row['activity'] ?? $row['description'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= get_status_badge_class($row['status'] ?? '') ?>">
                                    <?= esc(ucfirst($row['status'] ?? '-')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center">
                            <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-2"></i>
                            <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get chart data from controller
        const initialSppdTrend = <?= json_encode($sppd_trend ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const initialSppdByBidang = <?= json_encode($sppd_by_bidang ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        // Chart instances to prevent re-initialization
        let chartSppdMonth = null;
        let chartSppdBidang = null;

        /**
         * Render Line Chart for SPPD Trend
         */
        function renderLineChart(labels, data) {
            const ctx = document.getElementById('chart-sppd-month');
            if (!ctx) return;

            // Destroy existing chart if exists
            if (chartSppdMonth) {
                chartSppdMonth.destroy();
            }

            chartSppdMonth = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah SPPD',
                        data: data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' SPPD';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        /**
         * Render Doughnut Chart for SPPD by Bidang
         */
        function renderDoughnutChart(labels, data) {
            const ctx = document.getElementById('chart-sppd-bidang');
            if (!ctx) return;

            // Destroy existing chart if exists
            if (chartSppdBidang) {
                chartSppdBidang.destroy();
            }

            // Generate colors
            const colors = [
                '#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', 
                '#ef4444', '#06b6d4', '#ec4899', '#6366f1'
            ];

            chartSppdBidang = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            return {
                                                text: label + ': ' + value,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' SPPD (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        /**
         * Load SPPD Trend Chart
         */
        if (initialSppdTrend && initialSppdTrend.length > 0) {
            const labels = initialSppdTrend.map(d => d.month || d.bulan || 'N/A');
            const counts = initialSppdTrend.map(d => parseInt(d.count || d.total || 0, 10));
            renderLineChart(labels, counts);
        } else {
            // Try loading from AJAX if initial data is empty
            axios.get('<?= base_url('superadmin/dashboard/chart-data') ?>', {
                    params: { type: 'sppd_trend' }
                })
                .then(res => {
                    const data = res.data.data || res.data || [];
                    if (data.length > 0) {
                        const labels = data.map(d => d.month || d.bulan || 'N/A');
                        const counts = data.map(d => parseInt(d.count || d.total || 0, 10));
                        renderLineChart(labels, counts);
                    } else {
                        // Show empty state
                        const ctx = document.getElementById('chart-sppd-month');
                        if (ctx) {
                            ctx.parentElement.innerHTML = '<div class="flex items-center justify-center h-full"><div class="text-center"><i class="fas fa-chart-line text-gray-300 text-5xl mb-3"></i><p class="text-gray-500 text-sm">Belum ada data trend SPPD</p></div></div>';
                        }
                    }
                })
                .catch(err => {
                    console.error('Error loading SPPD trend:', err);
                    showToast('Gagal memuat data trend SPPD', 'error');
                });
        }

        /**
         * Load SPPD by Bidang Chart
         */
        if (initialSppdByBidang && initialSppdByBidang.length > 0) {
            const labels = initialSppdByBidang.map(d => d.nama_bidang || d.bidang_nama || d.name || 'N/A');
            const counts = initialSppdByBidang.map(d => parseInt(d.total || d.count || 0, 10));
            renderDoughnutChart(labels, counts);
        } else {
            // Try loading from AJAX if initial data is empty
            axios.get('<?= base_url('superadmin/dashboard/chart-data') ?>', {
                    params: { type: 'sppd_by_bidang' }
                })
                .then(res => {
                    const data = res.data.data || res.data || [];
                    if (data.length > 0) {
                        const labels = data.map(d => d.nama_bidang || d.bidang_nama || d.name || 'N/A');
                        const counts = data.map(d => parseInt(d.total || d.count || 0, 10));
                        renderDoughnutChart(labels, counts);
                    } else {
                        // Show empty state
                        const ctx = document.getElementById('chart-sppd-bidang');
                        if (ctx) {
                            ctx.parentElement.innerHTML = '<div class="flex items-center justify-center h-full"><div class="text-center"><i class="fas fa-chart-pie text-gray-300 text-5xl mb-3"></i><p class="text-gray-500 text-sm">Belum ada data SPPD per bidang</p></div></div>';
                        }
                    }
                })
                .catch(err => {
                    console.error('Error loading SPPD by bidang:', err);
                    showToast('Gagal memuat data SPPD per bidang', 'error');
                });
        }

        // Initialize DataTable for recent activities if needed
        if ($.fn.DataTable && document.getElementById('recent-activities-table')) {
            $('#recent-activities-table').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        }
    });

    /**
     * Helper function to get role badge class
     */
    function get_role_badge_class(role) {
        const badges = {
            'superadmin': 'bg-purple-100 text-purple-800',
            'kepaladinas': 'bg-blue-100 text-blue-800',
            'kepalabidang': 'bg-green-100 text-green-800',
            'pegawai': 'bg-gray-100 text-gray-800',
            'keuangan': 'bg-yellow-100 text-yellow-800'
        };
        return badges[role] || 'bg-gray-100 text-gray-800';
    }

    /**
     * Helper function to get status badge class
     */
    function get_status_badge_class(status) {
        const badges = {
            'success': 'bg-green-100 text-green-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'failed': 'bg-red-100 text-red-800',
            'approved': 'bg-blue-100 text-blue-800',
            'rejected': 'bg-red-100 text-red-800'
        };
        return badges[status] || 'bg-gray-100 text-gray-800';
    }
</script>

<?php
// Helper functions if not defined globally
if (!function_exists('get_role_badge_class')) {
    function get_role_badge_class($role) {
        $badges = [
            'superadmin' => 'bg-purple-100 text-purple-800',
            'kepaladinas' => 'bg-blue-100 text-blue-800',
            'kepalabidang' => 'bg-green-100 text-green-800',
            'pegawai' => 'bg-gray-100 text-gray-800',
            'keuangan' => 'bg-yellow-100 text-yellow-800'
        ];
        return $badges[$role] ?? 'bg-gray-100 text-gray-800';
    }
}

if (!function_exists('get_status_badge_class')) {
    function get_status_badge_class($status) {
        $badges = [
            'success' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];
        return $badges[$status] ?? 'bg-gray-100 text-gray-800';
    }
}
?>

<?= $this->endSection() ?>