<?php
include '../database/db_config.php';

// Get policy statistics by type
$policyByType = $conn->query("
    SELECT PolicyType, 
           COUNT(*) as Count,
           SUM(CASE WHEN Status = 'Active' THEN 1 ELSE 0 END) as ActiveCount,
           SUM(PremiumAmount) as TotalPremium,
           SUM(CoverageAmount) as TotalCoverage,
           AVG(PremiumAmount) as AvgPremium
    FROM Policies
    GROUP BY PolicyType
    ORDER BY Count DESC
");

// Get expiring policies in next 30 days
$expiringPolicies = $conn->query("
    SELECT p.PolicyID, p.PolicyType, p.StartDate, p.EndDate, p.PremiumAmount, p.Status,
           c.Name as CustomerName, a.Name as AgentName
    FROM Policies p
    JOIN Customers c ON p.CustomerID = c.CustomerID
    LEFT JOIN Agents a ON p.AgentID = a.AgentID
    WHERE p.Status = 'Active' AND p.EndDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY p.EndDate ASC
");

// Get policies by status
$policyByStatus = $conn->query("
    SELECT Status, COUNT(*) as Count, SUM(PremiumAmount) as TotalPremium
    FROM Policies
    GROUP BY Status
");

include '../includes/header.php';
?>

<h2 class="mb-4">Policy Reports</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Policies by Type
            </div>
            <div class="card-body">
                <?php if ($policyByType->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Policy Type</th>
                                    <th>Total Policies</th>
                                    <th>Active Policies</th>
                                    <th>Total Premium</th>
                                    <th>Avg Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalPolicies = 0;
                                $totalActivePolicies = 0;
                                $totalPremiumAmount = 0;
                                
                                while ($row = $policyByType->fetch_assoc()): 
                                    $totalPolicies += $row['Count'];
                                    $totalActivePolicies += $row['ActiveCount'];
                                    $totalPremiumAmount += $row['TotalPremium'];
                                ?>
                                    <tr>
                                        <td><?php echo $row['PolicyType']; ?></td>
                                        <td><?php echo $row['Count']; ?></td>
                                        <td><?php echo $row['ActiveCount']; ?></td>
                                        <td>$<?php echo number_format($row['TotalPremium'], 2); ?></td>
                                        <td>$<?php echo number_format($row['AvgPremium'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="table-info">
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $totalPolicies; ?></strong></td>
                                    <td><strong><?php echo $totalActivePolicies; ?></strong></td>
                                    <td><strong>$<?php echo number_format($totalPremiumAmount, 2); ?></strong></td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No policy data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                Policies by Status
            </div>
            <div class="card-body">
                <?php if ($policyByStatus->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Total Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $policyByStatus->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $row['Status'] == 'Active' ? 'success' : 
                                                    ($row['Status'] == 'Expired' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo $row['Status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['Count']; ?></td>
                                        <td>$<?php echo number_format($row['TotalPremium'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No policy data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        Policies Expiring in Next 30 Days
    </div>
    <div class="card-body">
        <?php if ($expiringPolicies->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Policy ID</th>
                            <th>Customer</th>
                            <th>Agent</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Premium</th>
                            <th>Days Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $today = new DateTime();
                        while ($policy = $expiringPolicies->fetch_assoc()): 
                            $endDate = new DateTime($policy['EndDate']);
                            $daysLeft = $today->diff($endDate)->days;
                        ?>
                            <tr>
                                <td><?php echo $policy['PolicyID']; ?></td>
                                <td><?php echo $policy['CustomerName']; ?></td>
                                <td><?php echo $policy['AgentName']; ?></td>
                                <td><?php echo $policy['PolicyType']; ?></td>
                                <td><?php echo $policy['StartDate']; ?></td>
                                <td><?php echo $policy['EndDate']; ?></td>
                                <td>$<?php echo number_format($policy['PremiumAmount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $daysLeft <= 7 ? 'danger' : 'warning'; ?>">
                                        <?php echo $daysLeft; ?> days
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No policies expiring in the next 30 days.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
