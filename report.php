<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

try {
    $dbPath = __DIR__ . DIRECTORY_SEPARATOR . "inventory.db";
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get filter parameter
$filter = $_GET['filter'] ?? 'day';

// Prepare SQL based on filter
switch($filter) {
    case 'week':
        $dateFormat = '%Y-W%W';
        $groupBy = "strftime('%Y-W%W', sale_date)";
        $labelFormat = "Week";
        break;
    case 'month':
        $dateFormat = '%Y-%m';
        $groupBy = "strftime('%Y-%m', sale_date)";
        $labelFormat = "Month";
        break;
    case 'year':
        $dateFormat = '%Y';
        $groupBy = "strftime('%Y', sale_date)";
        $labelFormat = "Year";
        break;
    default: // day
        $dateFormat = '%Y-%m-%d';
        $groupBy = "DATE(sale_date)";
        $labelFormat = "Day";
}

// Get sales data
$salesQuery = "
    SELECT 
        $groupBy as period,
        SUM(total_amount) as revenue,
        COUNT(*) as transactions
    FROM sales 
    WHERE status = 'completed'
    GROUP BY period
    ORDER BY period DESC
    LIMIT 30
";
$salesData = $db->query($salesQuery)->fetchAll(PDO::FETCH_ASSOC);
$salesData = array_reverse($salesData);

// Get top products
$topProductsQuery = "
    SELECT 
        product_name,
        SUM(quantity) as units_sold,
        SUM(subtotal) as revenue
    FROM sales_items
    GROUP BY product_name
    ORDER BY units_sold DESC
    LIMIT 10
";
$topProducts = $db->query($topProductsQuery)->fetchAll(PDO::FETCH_ASSOC);

// Get summary stats
$statsQuery = "
    SELECT 
        SUM(total_amount) as total_revenue,
        COUNT(*) as total_transactions,
        AVG(total_amount) as avg_transaction
    FROM sales
    WHERE status = 'completed'
";
$stats = $db->query($statsQuery)->fetch(PDO::FETCH_ASSOC);

// Prepare data for charts
$labels = array_column($salesData, 'period');
$revenues = array_column($salesData, 'revenue');
$transactions = array_column($salesData, 'transactions');

$productLabels = array_column($topProducts, 'product_name');
$productSales = array_column($topProducts, 'units_sold');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link rel="stylesheet" href="styles/report.css">
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="report-container">
        <img src="assets/logo.svg" class="report-logo">
        
        <div class="report-header">
            <h1>Sales Analytics Report</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn-secondary">Dashboard</a>
                <a href="pos.php" class="btn-secondary">POS</a>
                <a href="inventory.php" class="btn-secondary">Inventory</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

<!-- Filter Buttons -->
<div class="filter-section">
    <h3>Filter by:</h3>
    <div class="filter-buttons">
        <a href="?filter=day" class="filter-btn <?php echo $filter === 'day' ? 'active' : ''; ?>">Day</a>
        <a href="?filter=week" class="filter-btn <?php echo $filter === 'week' ? 'active' : ''; ?>">Week</a>
        <a href="?filter=month" class="filter-btn <?php echo $filter === 'month' ? 'active' : ''; ?>">Month</a>
        <a href="?filter=year" class="filter-btn <?php echo $filter === 'year' ? 'active' : ''; ?>">Year</a>
    </div>
    <button onclick="printReport()" class="print-btn">
        <span>📄</span> Print / Export PDF
    </button>
</div>

        <!-- Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Transactions</h3>
                <p class="stat-value"><?php echo number_format($stats['total_transactions']); ?></p>
            </div>
            <div class="stat-card">
                <h3>Average Sale</h3>
                <p class="stat-value">₱<?php echo number_format($stats['avg_transaction'], 2); ?></p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-container">
                <h2>Revenue Trend (<?php echo ucfirst($filter); ?>)</h2>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="chart-container">
                <h2>Top 10 Products</h2>
                <canvas id="productsChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="transactions-section">
            <h2>Recent Transactions</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentQuery = "SELECT * FROM sales ORDER BY sale_date DESC LIMIT 20";
                        $recent = $db->query($recentQuery)->fetchAll(PDO::FETCH_ASSOC);
                        foreach($recent as $sale):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['transaction_id']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($sale['sale_date'])); ?></td>
                            <td>₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($sale['cashier_name']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Revenue Line Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?php echo json_encode($revenues); ?>,
                    borderColor: '#67a6ff',
                    backgroundColor: 'rgba(103, 166, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: 'white', font: { size: 14 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                }
            }
        });

        // Products Bar Chart
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productLabels); ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?php echo json_encode($productSales); ?>,
                    backgroundColor: '#67a6ff',
                    borderColor: '#0466c8',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: 'white', font: { size: 14 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                }
            }
        });
        // Print Report Function
function printReport() {
    window.print();
}
    </script>
</body>
</html>