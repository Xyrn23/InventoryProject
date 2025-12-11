<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../index.php");
    exit();
}

require_once '../../lib/database.php';
$db = getDatabaseConnection();

$pageTitle = "Transaction Report";
$pageCss = "transaction.css";
require_once '../../components/header.php';

// The rest of the PHP code from the original transaction.php file
// ...
?>

<div class="report-container">
    <div class="transactions-section">
        <div class="header-wrapper">
            <h2>Recent Transactions</h2>
            <button class="back-btn"><a href="../../report.php">↩</a></button>
        </div>
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

<?php require_once '../../components/footer.php'; ?>