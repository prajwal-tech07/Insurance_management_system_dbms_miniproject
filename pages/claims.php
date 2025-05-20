<?php
include '../database/db_config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new claim
    if (isset($_POST['add_claim'])) {
        $policy_id = $conn->real_escape_string($_POST['policy_id']);
        $claim_date = $conn->real_escape_string($_POST['claim_date']);
        $description = $conn->real_escape_string($_POST['description']);
        $amount = $conn->real_escape_string($_POST['amount']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "INSERT INTO Claims (PolicyID, ClaimDate, Description, Amount, Status) 
                VALUES ('$policy_id', '$claim_date', '$description', '$amount', '$status')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Claim added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Update existing claim
    if (isset($_POST['update_claim'])) {
        $claim_id = $conn->real_escape_string($_POST['claim_id']);
        $policy_id = $conn->real_escape_string($_POST['policy_id']);
        $claim_date = $conn->real_escape_string($_POST['claim_date']);
        $description = $conn->real_escape_string($_POST['description']);
        $amount = $conn->real_escape_string($_POST['amount']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "UPDATE Claims SET 
                PolicyID='$policy_id', 
                ClaimDate='$claim_date', 
                Description='$description', 
                Amount='$amount', 
                Status='$status' 
                WHERE ClaimID=$claim_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Claim updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Delete claim
    if (isset($_POST['delete_claim'])) {
        $claim_id = $conn->real_escape_string($_POST['claim_id']);
        
        $sql = "DELETE FROM Claims WHERE ClaimID=$claim_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Claim deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Get claim to edit
$editClaim = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM Claims WHERE ClaimID=$id");
    if ($result->num_rows > 0) {
        $editClaim = $result->fetch_assoc();
    }
}

// Fetch all active policies for dropdown
$policies = $conn->query("
    SELECT p.PolicyID, p.PolicyType, c.Name as CustomerName
    FROM Policies p
    JOIN Customers c ON p.CustomerID = c.CustomerID
    WHERE p.Status = 'Active'
    ORDER BY p.PolicyID DESC
");

// Fetch all claims with policy and customer details
$claims = $conn->query("
    SELECT cl.*, p.PolicyType, c.Name as CustomerName
    FROM Claims cl
    JOIN Policies p ON cl.PolicyID = p.PolicyID
    JOIN Customers c ON p.CustomerID = c.CustomerID
    ORDER BY cl.ClaimDate DESC
");

include '../includes/header.php';
?>

<h2 class="mb-4">Claim Management</h2>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-warning text-white">
        <?php echo $editClaim ? 'Edit Claim' : 'File New Claim'; ?>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <?php if ($editClaim): ?>
                <input type="hidden" name="claim_id" value="<?php echo $editClaim['ClaimID']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="policy_id">Policy</label>
                    <select class="form-control" id="policy_id" name="policy_id" required>
                        <option value="">Select Policy</option>
                        <?php while ($policy = $policies->fetch_assoc()): ?>
                            <option value="<?php echo $policy['PolicyID']; ?>" <?php echo ($editClaim && $editClaim['PolicyID'] == $policy['PolicyID']) ? 'selected' : ''; ?>>
                                Policy #<?php echo $policy['PolicyID']; ?> - <?php echo $policy['PolicyType']; ?> - <?php echo $policy['CustomerName']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="claim_date">Claim Date</label>
                    <input type="date" class="form-control" id="claim_date" name="claim_date" value="<?php echo $editClaim ? $editClaim['ClaimDate'] : date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $editClaim ? $editClaim['Description'] : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="amount">Claim Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" value="<?php echo $editClaim ? $editClaim['Amount'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Filed" <?php echo ($editClaim && $editClaim['Status'] == 'Filed') ? 'selected' : ''; ?>>Filed</option>
                        <option value="Under Review" <?php echo ($editClaim && $editClaim['Status'] == 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
                        <option value="Approved" <?php echo ($editClaim && $editClaim['Status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="Denied" <?php echo ($editClaim && $editClaim['Status'] == 'Denied') ? 'selected' : ''; ?>>Denied</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-warning" name="<?php echo $editClaim ? 'update_claim' : 'add_claim'; ?>">
                <?php echo $editClaim ? 'Update Claim' : 'File Claim'; ?>
            </button>
            
            <?php if ($editClaim): ?>
                <a href="claims.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning text-white">
        Claims List
    </div>
    <div class="card-body">
        <?php if ($claims->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Customer</th>
                            <th>Policy Type</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($claim = $claims->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $claim['ClaimID']; ?></td>
                                <td><?php echo $claim['CustomerName']; ?></td>
                                <td><?php echo $claim['PolicyType']; ?></td>
                                <td><?php echo $claim['ClaimDate']; ?></td>
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
                                <td>
                                    <a href="claims.php?action=edit&id=<?php echo $claim['ClaimID']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="post" action="" style="display:inline;" id="delete-claim-<?php echo $claim['ClaimID']; ?>">
                                        <input type="hidden" name="claim_id" value="<?php echo $claim['ClaimID']; ?>">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $claim['ClaimID']; ?>, 'claim')">Delete</button>
                                        <input type="hidden" name="delete_claim" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No claims found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
