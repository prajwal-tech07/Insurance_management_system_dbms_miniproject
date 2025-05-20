<?php
include '../database/db_config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new policy
    if (isset($_POST['add_policy'])) {
        $customer_id = $conn->real_escape_string($_POST['customer_id']);
        $agent_id = $conn->real_escape_string($_POST['agent_id']);
        $policy_type = $conn->real_escape_string($_POST['policy_type']);
        $start_date = $conn->real_escape_string($_POST['start_date']);
        $end_date = $conn->real_escape_string($_POST['end_date']);
        $premium = $conn->real_escape_string($_POST['premium']);
        $coverage = $conn->real_escape_string($_POST['coverage']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "INSERT INTO Policies (CustomerID, AgentID, PolicyType, StartDate, EndDate, PremiumAmount, CoverageAmount, Status) 
                VALUES ('$customer_id', '$agent_id', '$policy_type', '$start_date', '$end_date', '$premium', '$coverage', '$status')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Policy added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Update existing policy
    if (isset($_POST['update_policy'])) {
        $policy_id = $conn->real_escape_string($_POST['policy_id']);
        $customer_id = $conn->real_escape_string($_POST['customer_id']);
        $agent_id = $conn->real_escape_string($_POST['agent_id']);
        $policy_type = $conn->real_escape_string($_POST['policy_type']);
        $start_date = $conn->real_escape_string($_POST['start_date']);
        $end_date = $conn->real_escape_string($_POST['end_date']);
        $premium = $conn->real_escape_string($_POST['premium']);
        $coverage = $conn->real_escape_string($_POST['coverage']);
        $status = $conn->real_escape_string($_POST['status']);
        
        $sql = "UPDATE Policies SET 
                CustomerID='$customer_id', 
                AgentID='$agent_id', 
                PolicyType='$policy_type', 
                StartDate='$start_date', 
                EndDate='$end_date', 
                PremiumAmount='$premium', 
                CoverageAmount='$coverage', 
                Status='$status' 
                WHERE PolicyID=$policy_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Policy updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Delete policy
    if (isset($_POST['delete_policy'])) {
        $policy_id = $conn->real_escape_string($_POST['policy_id']);
        
        $sql = "DELETE FROM Policies WHERE PolicyID=$policy_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Policy deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Get policy to edit
$editPolicy = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM Policies WHERE PolicyID=$id");
    if ($result->num_rows > 0) {
        $editPolicy = $result->fetch_assoc();
    }
}

// Fetch all customers for dropdown
$customers = $conn->query("SELECT CustomerID, Name FROM Customers ORDER BY Name");

// Fetch all agents for dropdown
$agents = $conn->query("SELECT AgentID, Name FROM Agents ORDER BY Name");

// Fetch all policies with customer and agent names
$policies = $conn->query("
    SELECT p.*, c.Name as CustomerName, a.Name as AgentName 
    FROM Policies p
    JOIN Customers c ON p.CustomerID = c.CustomerID
    LEFT JOIN Agents a ON p.AgentID = a.AgentID
    ORDER BY p.StartDate DESC
");

include '../includes/header.php';
?>

<h2 class="mb-4">Policy Management</h2>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <?php echo $editPolicy ? 'Edit Policy' : 'Add New Policy'; ?>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <?php if ($editPolicy): ?>
                <input type="hidden" name="policy_id" value="<?php echo $editPolicy['PolicyID']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <!-- Customer and Agent dropdowns -->
                <div class="form-group col-md-6">
                    <label for="customer_id">Customer</label>
                    <select class="form-control" id="customer_id" name="customer_id" required>
                        <option value="">Select Customer</option>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                            <option value="<?php echo $customer['CustomerID']; ?>" <?php echo ($editPolicy && $editPolicy['CustomerID'] == $customer['CustomerID']) ? 'selected' : ''; ?>>
                                <?php echo $customer['Name']; ?> (ID: <?php echo $customer['CustomerID']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="agent_id">Agent</label>
                    <select class="form-control" id="agent_id" name="agent_id" required>
                        <option value="">Select Agent</option>
                        <?php 
                        // Reset the result pointer
                        $agents->data_seek(0);
                        while ($agent = $agents->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $agent['AgentID']; ?>" <?php echo ($editPolicy && $editPolicy['AgentID'] == $agent['AgentID']) ? 'selected' : ''; ?>>
                                <?php echo $agent['Name']; ?> (ID: <?php echo $agent['AgentID']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="policy_type">Policy Type</label>
                    <select class="form-control" id="policy_type" name="policy_type" required>
                        <option value="">Select Type</option>
                        <option value="Auto" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Auto') ? 'selected' : ''; ?>>Auto</option>
                        <option value="Home" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Home') ? 'selected' : ''; ?>>Home</option>
                        <option value="Life" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Life') ? 'selected' : ''; ?>>Life</option>
                        <option value="Health" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Health') ? 'selected' : ''; ?>>Health</option>
                        <option value="Travel" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Travel') ? 'selected' : ''; ?>>Travel</option>
                        <option value="Business" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Business') ? 'selected' : ''; ?>>Business</option>
                        <option value="Liability" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Liability') ? 'selected' : ''; ?>>Liability</option>
                        <option value="Disability" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Disability') ? 'selected' : ''; ?>>Disability</option>
                        <option value="Pet" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Pet') ? 'selected' : ''; ?>>Pet</option>
                        <option value="Flood" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Flood') ? 'selected' : ''; ?>>Flood</option>
                        <option value="Earthquake" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Earthquake') ? 'selected' : ''; ?>>Earthquake</option>
                        <option value="Umbrella" <?php echo ($editPolicy && $editPolicy['PolicyType'] == 'Umbrella') ? 'selected' : ''; ?>>Umbrella</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control" id="start-date" name="start_date" value="<?php echo $editPolicy ? $editPolicy['StartDate'] : ''; ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control" id="end-date" name="end_date" value="<?php echo $editPolicy ? $editPolicy['EndDate'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="premium">Premium Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="premium" name="premium" value="<?php echo $editPolicy ? $editPolicy['PremiumAmount'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="coverage">Coverage Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="coverage" name="coverage" value="<?php echo $editPolicy ? $editPolicy['CoverageAmount'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Active" <?php echo ($editPolicy && $editPolicy['Status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                        <option value="Expired" <?php echo ($editPolicy && $editPolicy['Status'] == 'Expired') ? 'selected' : ''; ?>>Expired</option>
                        <option value="Cancelled" <?php echo ($editPolicy && $editPolicy['Status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success" name="<?php echo $editPolicy ? 'update_policy' : 'add_policy'; ?>">
                <?php echo $editPolicy ? 'Update Policy' : 'Add Policy'; ?>
            </button>
            
            <?php if ($editPolicy): ?>
                <a href="policies.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white">
        Policy List
    </div>
    <div class="card-body">
        <?php if ($policies->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Agent</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Premium</th>
                            <th>Coverage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($policy = $policies->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $policy['PolicyID']; ?></td>
                                <td><?php echo $policy['CustomerName']; ?></td>
                                <td><?php echo $policy['AgentName']; ?></td>
                                <td><?php echo $policy['PolicyType']; ?></td>
                                <td><?php echo $policy['StartDate']; ?></td>
                                <td><?php echo $policy['EndDate']; ?></td>
                                <td>$<?php echo number_format($policy['PremiumAmount'], 2); ?></td>
                                <td>$<?php echo number_format($policy['CoverageAmount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $policy['Status'] == 'Active' ? 'success' : 
                                            ($policy['Status'] == 'Expired' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo $policy['Status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="policies.php?action=edit&id=<?php echo $policy['PolicyID']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="post" action="" style="display:inline;" id="delete-policy-<?php echo $policy['PolicyID']; ?>">
                                        <input type="hidden" name="policy_id" value="<?php echo $policy['PolicyID']; ?>">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $policy['PolicyID']; ?>, 'policy')">Delete</button>
                                        <input type="hidden" name="delete_policy" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No policies found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
