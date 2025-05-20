<?php
include '../database/db_config.php';

// Get claim statistics by status
$claimsByStatus = $conn->query("
    SELECT Status, 
           COUNT(*) as Count,
           SUM(Amount) as TotalAmount,
           AVG(Amount) as AvgAmount
    FROM Claims
    GROUP BY Status
    ORDER BY Count DESC
");

// Get claim statistics by policy type
$claimsByPolicyType = $conn->query("
    SELECT p.PolicyType, 
           COUNT(cl.ClaimID) as Count,
           SUM(cl.Amount) as TotalAmount,
           AVG(cl.Amount) as AvgAmount
    FROM Claims cl
    JOIN Policies p ON cl.PolicyID = p.PolicyID
    GROUP BY p.PolicyType
    ORDER BY Count DESC
");

// Get recent claims
$recentClaims = $conn->query("
    SELECT cl.ClaimID, cl.ClaimDate, cl.Amount, cl.Status, cl.Description,
           p.PolicyType, c.Name as CustomerName
    FROM Claims cl
    JOIN Policies p ON cl.PolicyID = p.PolicyID
    JOIN Customers c ON p.CustomerID = c.CustomerID
    ORDER BY cl.ClaimDate DESC
    LIMIT 10
");

include '../includes/header.php';
?>

<h2 class="mb-4">Claim Reports</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                Claims by Status
            </div>
            <div class="card-body">
                <?php if ($claimsByStatus->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Avg Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalClaims = 0;
                                $totalClaimAmount = 0;
                                
                                while ($row = $claimsByStatus->fetch_assoc()): 
                                    $totalClaims += $row['Count'];
                                    $totalClaimAmount += $row['TotalAmount'];
                                ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $row['Status'] == 'Approved' ? 'success' : 
                                                    ($row['Status'] == 'Denied' ? 'danger' : 
                                                    ($row['Status'] == 'Under Review' ? 'warning' : 'info')); 
                                            ?>">
                                                <?php echo $row['Status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['Count']; ?></td>
                                        <td>$<?php echo number_format($row['TotalAmount'], 2); ?></td>
                                        <td>$<?php echo number_format($row['AvgAmount'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="table-info">
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $totalClaims; ?></strong></td>
                                    <td><strong>$<?php echo number_format($totalClaimAmount, 2); ?></strong></td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No claim data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Claims by Policy Type
            </div>
            <div class="card-body">
                <?php if ($claimsByPolicyType->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Policy Type</th>
                                    <th>Claims</th>
                                    <th>Total Amount</th>
                                    <th>Avg Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $claimsByPolicyType->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['PolicyType']; ?></td>
                                        <td><?php echo $row['Count']; ?></td>
                                        <td>$<?php echo number_format($row['TotalAmount'], 2); ?></td>
                                        <td>$<?php echo number_format($row['AvgAmount'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No claim data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        Recent Claims
    </div>
    <div class="card-body">
        <?php if ($recentClaims->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Policy Type</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($claim = $recentClaims->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $claim['ClaimID']; ?></td>
                                <td><?php echo $claim['ClaimDate']; ?></td>
                                <td><?php echo $claim['CustomerName']; ?></td>
                                <td><?php echo $claim['PolicyType']; ?></td>
                                <td><?php echo substr($claim['Description'], 0, 50) . (strlen($claim['Description']) > 50 ? '...' : ''); ?></td>
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
            </div>
        <?php else: ?>
            <p>No recent claims found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
