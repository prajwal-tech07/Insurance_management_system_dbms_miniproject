<?php
include 'database/db_config.php';

// Count statistics
$customerCount = $conn->query("SELECT COUNT(*) as count FROM Customers")->fetch_assoc()['count'];
$policyCount = $conn->query("SELECT COUNT(*) as count FROM Policies")->fetch_assoc()['count'];
$activeCount = $conn->query("SELECT COUNT(*) as count FROM Policies WHERE Status='Active'")->fetch_assoc()['count'];
$claimCount = $conn->query("SELECT COUNT(*) as count FROM Claims")->fetch_assoc()['count'];
$paymentCount = $conn->query("SELECT SUM(Amount) as total FROM Payments")->fetch_assoc()['total'];

// Get expiring policies (next 30 days)
$expiringPolicies = $conn->query("
    SELECT p.PolicyID, p.PolicyType, p.EndDate, c.Name as CustomerName
    FROM Policies p
    JOIN Customers c ON p.CustomerID = c.CustomerID
    WHERE p.Status = 'Active' AND p.EndDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY p.EndDate ASC
");

// Get recent claims
$recentClaims = $conn->query("
    SELECT cl.ClaimID, cl.ClaimDate, cl.Amount, cl.Status, c.Name as CustomerName, p.PolicyType
    FROM Claims cl
    JOIN Policies p ON cl.PolicyID = p.PolicyID
    JOIN Customers c ON p.CustomerID = c.CustomerID
    ORDER BY cl.ClaimDate DESC
    LIMIT 5
");

include 'includes/header.php';
?>

<div class="jumbotron">
    <h1 class="display-4">Insurance Management System</h1>
    <p class="lead">A comprehensive system for managing insurance policies, claims, and payments.</p>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3 dashboard-card">
            <div class="card-header">Customers</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $customerCount; ?></h5>
                <p class="card-text">Total registered customers</p>
                <a href="pages/customers.php" class="btn btn-light btn-sm">Manage Customers</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3 dashboard-card">
            <div class="card-header">Policies</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $policyCount; ?> (<?php echo $activeCount; ?> active)</h5>
                <p class="card-text">Total insurance policies</p>
                <a href="pages/policies.php" class="btn btn-light btn-sm">Manage Policies</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3 dashboard-card">
            <div class="card-header">Claims</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $claimCount; ?></h5>
                <p class="card-text">Total insurance claims</p>
                <a href="pages/claims.php" class="btn btn-light btn-sm">Manage Claims</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3 dashboard-card">
            <div class="card-header">Payments</div>
            <div class="card-body">
                <h5 class="card-title">$<?php echo number_format($paymentCount, 2); ?></h5>
                <p class="card-text">Total premium payments</p>
                <a href="pages/payments.php" class="btn btn-light btn-sm">Manage Payments</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                Policies Expiring Soon (Next 30 Days)
            </div>
            <div class="card-body">
                <?php if ($expiringPolicies->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Policy ID</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($policy = $expiringPolicies->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $policy['PolicyID']; ?></td>
                            <td><?php echo $policy['CustomerName']; ?></td>
                            <td><?php echo $policy['PolicyType']; ?></td>
                            <td><?php echo $policy['EndDate']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No policies expiring in the next 30 days.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Recent Claims
            </div>
            <div class="card-body">
                <?php if ($recentClaims->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($claim = $recentClaims->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $claim['ClaimID']; ?></td>
                            <td><?php echo $claim['CustomerName']; ?></td>
                            <td>$<?php echo number_format($claim['Amount'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $claim['Status'] == 'Approved' ? 'success' : 
                                        ($claim['Status'] == 'Denied' ? 'danger' : 
                                        ($claim['Status'] == 'Under Review' ? 'warning' : 'info')); 
                                ?>">
                                    <?php echo $claim['Status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No recent claims.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Quick Actions
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="pages/customers.php?action=add" class="btn btn-outline-primary btn-block">Add New Customer</a>
                    </div>
                    <div class="col-md-3">
                        <a href="pages/policies.php?action=add" class="btn btn-outline-success btn-block">Create New Policy</a>
                    </div>
                    <div class="col-md-3">
                        <a href="pages/claims.php?action=add" class="btn btn-outline-warning btn-block">File New Claim</a>
                    </div>
                    <div class="col-md-3">
                        <a href="pages/payments.php?action=add" class="btn btn-outline-info btn-block">Record Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
