<?php
include '../database/db_config.php';

// Get payment statistics by month
$paymentsByMonth = $conn->query("
    SELECT DATE_FORMAT(PaymentDate, '%Y-%m') as Month, 
           COUNT(*) as Count,
           SUM(Amount) as TotalAmount
    FROM Payments
    WHERE PaymentDate >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY Month
    ORDER BY Month DESC
");

// Get payment statistics by method
$paymentsByMethod = $conn->query("
    SELECT PaymentMethod, 
           COUNT(*) as Count,
           SUM(Amount) as TotalAmount
    FROM Payments
    GROUP BY PaymentMethod
    ORDER BY Count DESC
");

// Get payment statistics by policy type
$paymentsByPolicyType = $conn->query("
    SELECT p.PolicyType, 
           COUNT(pm.PaymentID) as Count,
           SUM(pm.Amount) as TotalAmount
    FROM Payments pm
    JOIN Policies p ON pm.PolicyID = p.PolicyID
    GROUP BY p.PolicyType
    ORDER BY Count DESC
");

include '../includes/header.php';
?>

<h2 class="mb-4">Payment Reports</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Payments by Method
            </div>
            <div class="card-body">
                <?php if ($paymentsByMethod->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalPayments = 0;
                                $totalPaymentAmount = 0;
                                
                                while ($row = $paymentsByMethod->fetch_assoc()): 
                                    $totalPayments += $row['Count'];
                                    $totalPaymentAmount += $row['TotalAmount'];
                                ?>
                                    <tr>
                                        <td><?php echo $row['PaymentMethod']; ?></td>
                                        <td><?php echo $row['Count']; ?></td>
                                        <td>$<?php echo number_format($row['TotalAmount'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="table-info">
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $totalPayments; ?></strong></td>
                                    <td><strong>$<?php echo number_format($totalPaymentAmount, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No payment data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                Payments by Policy Type
            </div>
            <div class="card-body">
                <?php if ($paymentsByPolicyType->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Policy Type</th>
                                    <th>Payments</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $paymentsByPolicyType->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['PolicyType']; ?></td>
                                        <td><?php echo $row['Count']; ?></td>
                                        <td>$<?php echo number_format($row['TotalAmount'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No payment data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        Monthly Payment Summary (Last 12 Months)
    </div>
    <div class="card-body">
        <?php if ($paymentsByMonth->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Number of Payments</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($month = $paymentsByMonth->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('F Y', strtotime($month['Month'] . '-01')); ?></td>
                                <td><?php echo $month['Count']; ?></td>
                                <td>$<?php echo number_format($month['TotalAmount'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No monthly payment data available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
