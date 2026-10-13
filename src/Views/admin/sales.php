<?php
$adminTitle = 'Báo cáo thống kê đơn hàng';
$activeMenu = 'sales';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';

// 1. Chuẩn bị dữ liệu biểu đồ Doanh thu & Đơn hàng theo tháng (theo trình tự thời gian)
$chartMonthly = array_reverse($monthlyStats ?? []);
$monthLabels = [];
$monthRevenues = [];
$monthOrders = [];

foreach ($chartMonthly as $m) {
    $monthLabels[] = 'T' . (int)$m['month'] . '/' . (int)$m['year'];
    $monthRevenues[] = (float)$m['revenue'];
    $monthOrders[] = (int)$m['order_count'];
}

// 2. Chuẩn bị dữ liệu biểu đồ theo Quý
$chartQuarterly = array_reverse($quarterlyStats ?? []);
$quarterLabels = [];
$quarterRevenues = [];

foreach ($chartQuarterly as $q) {
    $quarterLabels[] = 'Quý ' . (int)$q['quarter'] . '/' . (int)$q['year'];
    $quarterRevenues[] = (float)$q['revenue'];
}

// 3. Chuẩn bị dữ liệu biểu đồ Phương thức thanh toán
$paymentLabels = [];
$paymentRevenues = [];
$paymentOrders = [];
$methodNameMap = [
    'COD' => 'Tiền mặt khi nhận (COD)',
    'BANK_TRANSFER' => 'Chuyển khoản (VietQR)',
    'MOMO' => 'Ví điện tử MoMo',
    'VNPAY' => 'Cổng thanh toán VNPAY',
];

foreach (($paymentStats ?? []) as $p) {
    $methodKey = (string)($p['payment_method'] ?? 'COD');
    $paymentLabels[] = $methodNameMap[$methodKey] ?? $methodKey;
    $paymentRevenues[] = (float)($p['total_amount'] ?? 0);
    $paymentOrders[] = (int)($p['order_count'] ?? 0);
}
?>

<main class="admin-main">
    <div style="margin-bottom: 20px;">
        <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
        <h1 style="margin: 4px 0 0;">Thống kê Đơn hàng & Doanh thu</h1>
        <small style="color: #666;">Báo cáo số lượng sản phẩm và thành tiền theo Tháng, Quý và Năm</small>
    </div>

    <!-- 1. CÁC THẺ TỔNG QUAN (KPIs) -->
    <div class="stats" style="margin-bottom: 24px;">
        <div class="stat">
            <span>TỔNG DOANH THU TOÀN CỬA HÀNG</span>
            <b style="color: #276238;"><?= number_format((float)($summary['total_revenue'] ?? 0), 0, ',', '.') ?>₫</b>
            <i class="trend" style="font-style: normal; color: #666;">Đơn hàng không bị hủy</i>
        </div>
        <div class="stat">
            <span>TỔNG SỐ ĐƠN HÀNG</span>
            <b><?= number_format((int)($summary['total_orders'] ?? 0)) ?> đơn</b>
            <i class="trend" style="font-style: normal; color: #666;">Đã tạo thành công</i>
        </div>
        <div class="stat">
            <span>SỐ LƯỢNG SẢN PHẨM ĐÃ BÁN</span>
            <b style="color: #1e40af;"><?= number_format((int)($summary['total_items'] ?? 0)) ?> món</b>
            <i class="trend" style="font-style: normal; color: #666;">Tổng số lượng chi tiết</i>
        </div>
        <div class="stat">
            <span>GIÁ TRỊ ĐƠN TRUNG BÌNH (AOV)</span>
            <b>
                <?php 
                    $avg = ($summary['total_orders'] ?? 0) > 0 
                        ? ($summary['total_revenue'] / $summary['total_orders']) 
                        : 0;
                    echo number_format((float)$avg, 0, ',', '.') . '₫';
                ?>
            </b>
            <i class="trend" style="font-style: normal; color: #666;">Doanh thu / Đơn hàng</i>
        </div>
    </div>

    <!-- 2. KHỐI BIỂU ĐỒ TRỰC QUAN (CHART.JS) -->
    <div class="sales-charts-grid" style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 20px; margin-bottom: 24px;">
        <!-- Biểu đồ 1: Doanh thu & Đơn hàng theo Tháng -->
        <div class="admin-panel" style="padding: 22px; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; color: #1e3323; font-weight: 700;">Biểu đồ Doanh thu & Đơn hàng theo Tháng</h3>
                    <small style="color: #666;">Cột xanh: Doanh thu (₫) · Đường cam: Số lượng đơn hàng</small>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #2e5b38; background: #eaf4ec; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                        <span style="display: inline-block; width: 8px; height: 8px; background: #2e5b38; border-radius: 2px;"></span> Doanh thu
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #d97706; background: #fef3c7; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                        <span style="display: inline-block; width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></span> Đơn hàng
                    </span>
                </div>
            </div>
            
            <?php if (!empty($monthLabels)): ?>
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            <?php else: ?>
                <div class="admin-empty-cell" style="padding: 80px 20px;">
                    <span>📊 Chưa có đủ dữ liệu đơn hàng để hiển thị biểu đồ theo tháng.</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Biểu đồ 2: Tỷ trọng phân bổ Doughnut -->
        <div class="admin-panel" style="padding: 22px; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; color: #1e3323; font-weight: 700;" id="doughnutChartTitle">Tỷ trọng Doanh thu theo Quý</h3>
                    <small style="color: #666;" id="doughnutChartSubtitle">Phân bổ doanh thu các quý</small>
                </div>
                <div style="display: inline-flex; background: #f0f3eb; padding: 3px; border-radius: 6px; gap: 3px;">
                    <button type="button" id="btnViewQuarter" onclick="switchDoughnutView('quarter')" style="border: none; background: #2e5b38; color: #fff; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all .2s;">Theo Quý</button>
                    <button type="button" id="btnViewPayment" onclick="switchDoughnutView('payment')" style="border: none; background: transparent; color: #555; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: all .2s;">Phương thức TT</button>
                </div>
            </div>

            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="doughnutSalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 2. BẢNG THỐNG KÊ THEO THÁNG -->
    <section class="admin-panel" style="margin-bottom: 24px;">
        <div class="title-row" style="margin-bottom: 14px;">
            <h2 style="font-size: 16px; margin: 0; color: #2e5b38;">
                1. Thống kê theo Tháng
            </h2>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Số lượng đơn hàng</th>
                        <th>Số sản phẩm bán ra</th>
                        <th style="text-align: right;">Doanh thu (Thành tiền)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monthlyStats)): ?>
                        <?php foreach ($monthlyStats as $row): ?>
                            <tr>
                                <td><strong>Tháng <?= (int)$row['month'] ?> / <?= (int)$row['year'] ?></strong></td>
                                <td><?= number_format((int)$row['order_count']) ?> đơn</td>
                                <td><?= number_format((int)$row['item_count']) ?> món</td>
                                <td style="text-align: right; color: #276238; font-weight: bold;">
                                    <?= number_format((float)$row['revenue'], 0, ',', '.') ?>₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="admin-empty-cell">Chưa có dữ liệu đơn hàng theo tháng.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 3. BẢNG THỐNG KÊ THEO QUÝ -->
    <section class="admin-panel" style="margin-bottom: 24px;">
        <div class="title-row" style="margin-bottom: 14px;">
            <h2 style="font-size: 16px; margin: 0; color: #1e40af;">
                2. Thống kê theo Quý
            </h2>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Số lượng đơn hàng</th>
                        <th>Số sản phẩm bán ra</th>
                        <th style="text-align: right;">Doanh thu (Thành tiền)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($quarterlyStats)): ?>
                        <?php foreach ($quarterlyStats as $row): ?>
                            <tr>
                                <td><strong>Quý <?= (int)$row['quarter'] ?> - Năm <?= (int)$row['year'] ?></strong></td>
                                <td><?= number_format((int)$row['order_count']) ?> đơn</td>
                                <td><?= number_format((int)$row['item_count']) ?> món</td>
                                <td style="text-align: right; color: #1e40af; font-weight: bold;">
                                    <?= number_format((float)$row['revenue'], 0, ',', '.') ?>₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="admin-empty-cell">Chưa có dữ liệu đơn hàng theo quý.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 4. BẢNG THỐNG KÊ THEO NĂM -->
    <section class="admin-panel">
        <div class="title-row" style="margin-bottom: 14px;">
            <h2 style="font-size: 16px; margin: 0; color: #6b21a8;">
                3. Thống kê theo Năm
            </h2>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Năm</th>
                        <th>Số lượng đơn hàng</th>
                        <th>Số sản phẩm bán ra</th>
                        <th style="text-align: right;">Doanh thu (Thành tiền)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($yearlyStats)): ?>
                        <?php foreach ($yearlyStats as $row): ?>
                            <tr>
                                <td><strong>Năm <?= (int)$row['year'] ?></strong></td>
                                <td><?= number_format((int)$row['order_count']) ?> đơn</td>
                                <td><?= number_format((int)$row['item_count']) ?> món</td>
                                <td style="text-align: right; color: #6b21a8; font-weight: bold;">
                                    <?= number_format((float)$row['revenue'], 0, ',', '.') ?>₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="admin-empty-cell">Chưa có dữ liệu đơn hàng theo năm.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- Nạp thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cài đặt font chữ đồng bộ
    Chart.defaults.font.family = "'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#555';

    // Dữ liệu từ PHP
    const monthLabels = <?= json_encode($monthLabels, JSON_UNESCAPED_UNICODE) ?>;
    const monthRevenues = <?= json_encode($monthRevenues) ?>;
    const monthOrders = <?= json_encode($monthOrders) ?>;

    const quarterLabels = <?= json_encode($quarterLabels, JSON_UNESCAPED_UNICODE) ?>;
    const quarterRevenues = <?= json_encode($quarterRevenues) ?>;

    const paymentLabels = <?= json_encode($paymentLabels, JSON_UNESCAPED_UNICODE) ?>;
    const paymentRevenues = <?= json_encode($paymentRevenues) ?>;

    // 1. Khởi tạo Biểu đồ Cột & Đường theo Tháng (Monthly Combo Chart)
    const monthlyCanvas = document.getElementById('monthlySalesChart');
    if (monthlyCanvas && monthLabels.length > 0) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Doanh thu (₫)',
                        data: monthRevenues,
                        backgroundColor: 'rgba(46, 91, 56, 0.85)',
                        hoverBackgroundColor: '#24492d',
                        borderRadius: 6,
                        yAxisID: 'yRevenue',
                        order: 2,
                    },
                    {
                        type: 'line',
                        label: 'Số lượng đơn hàng',
                        data: monthOrders,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.15)',
                        borderWidth: 3,
                        pointBackgroundColor: '#d97706',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.35,
                        yAxisID: 'yOrders',
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 51, 35, 0.95)',
                        padding: 12,
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                        boxPadding: 6,
                        callbacks: {
                            label: function (context) {
                                if (context.dataset.yAxisID === 'yRevenue') {
                                    return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' ₫';
                                }
                                return 'Số đơn hàng: ' + context.raw + ' đơn';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '500' } }
                    },
                    yRevenue: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: '#eef2ea' },
                        ticks: {
                            callback: function (val) {
                                if (val >= 1000000) {
                                    return (val / 1000000).toFixed(1) + ' tr';
                                } else if (val >= 1000) {
                                    return (val / 1000).toFixed(0) + ' k';
                                }
                                return val + ' ₫';
                            }
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        grid: { display: false },
                        ticks: {
                            stepSize: 1,
                            callback: function (val) {
                                return val + ' đơn';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Khởi tạo Biểu đồ Donut (Quarter / Payment Switchable)
    const doughnutCanvas = document.getElementById('doughnutSalesChart');
    let doughnutChart = null;

    const palette = [
        '#2e5b38', '#428854', '#2563eb', '#38bdf8', '#f59e0b', '#ec4899', '#8b5cf6'
    ];

    if (doughnutCanvas) {
        const hasQuarterData = quarterLabels.length > 0 && quarterRevenues.some(v => v > 0);

        doughnutChart = new Chart(doughnutCanvas, {
            type: 'doughnut',
            data: {
                labels: hasQuarterData ? quarterLabels : ['Chưa có dữ liệu'],
                datasets: [{
                    data: hasQuarterData ? quarterRevenues : [1],
                    backgroundColor: hasQuarterData ? palette : ['#e2e8df'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: hasQuarterData ? 8 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 14,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 51, 35, 0.95)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw;
                                const percent = total > 0 ? ((val / total) * 100).toFixed(1) + '%' : '0%';
                                return ' ' + new Intl.NumberFormat('vi-VN').format(val) + ' ₫ (' + percent + ')';
                            }
                        }
                    }
                }
            }
        });
    }

    // Chức năng chuyển đổi dữ liệu Doughnut
    window.switchDoughnutView = function (type) {
        const btnQuarter = document.getElementById('btnViewQuarter');
        const btnPayment = document.getElementById('btnViewPayment');
        const titleEl = document.getElementById('doughnutChartTitle');
        const subtitleEl = document.getElementById('doughnutChartSubtitle');

        if (!doughnutChart) return;

        if (type === 'quarter') {
            btnQuarter.style.background = '#2e5b38';
            btnQuarter.style.color = '#fff';
            btnPayment.style.background = 'transparent';
            btnPayment.style.color = '#555';

            titleEl.textContent = 'Tỷ trọng Doanh thu theo Quý';
            subtitleEl.textContent = 'Phân bổ doanh thu các quý';

            const hasQuarter = quarterLabels.length > 0 && quarterRevenues.some(v => v > 0);
            doughnutChart.data.labels = hasQuarter ? quarterLabels : ['Chưa có dữ liệu'];
            doughnutChart.data.datasets[0].data = hasQuarter ? quarterRevenues : [1];
            doughnutChart.data.datasets[0].backgroundColor = hasQuarter ? palette : ['#e2e8df'];
            doughnutChart.data.datasets[0].hoverOffset = hasQuarter ? 8 : 0;
        } else {
            btnPayment.style.background = '#2e5b38';
            btnPayment.style.color = '#fff';
            btnQuarter.style.background = 'transparent';
            btnQuarter.style.color = '#555';

            titleEl.textContent = 'Doanh thu theo Phương thức TT';
            subtitleEl.textContent = 'Tỷ trọng thanh toán COD vs Chuyển khoản';

            const hasPayment = paymentLabels.length > 0 && paymentRevenues.some(v => v > 0);
            doughnutChart.data.labels = hasPayment ? paymentLabels : ['Chưa có dữ liệu'];
            doughnutChart.data.datasets[0].data = hasPayment ? paymentRevenues : [1];
            doughnutChart.data.datasets[0].backgroundColor = hasPayment ? palette : ['#e2e8df'];
            doughnutChart.data.datasets[0].hoverOffset = hasPayment ? 8 : 0;
        }
        doughnutChart.update();
    };
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
