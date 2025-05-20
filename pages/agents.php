<?php
include '../database/db_config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new agent
    if (isset($_POST['add_agent'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $commission_rate = $conn->real_escape_string($_POST['commission_rate']);
        
        $sql = "INSERT INTO Agents (Name, Phone, Email, CommissionRate) 
                VALUES ('$name', '$phone', '$email', '$commission_rate')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Agent added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Update existing agent
    if (isset($_POST['update_agent'])) {
        $agent_id = $conn->real_escape_string($_POST['agent_id']);
        $name = $conn->real_escape_string($_POST['name']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $commission_rate = $conn->real_escape_string($_POST['commission_rate']);
        
        $sql = "UPDATE Agents SET 
                Name='$name', 
                Phone='$phone', 
                Email='$email', 
                CommissionRate='$commission_rate' 
                WHERE AgentID=$agent_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Agent updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Delete agent
    if (isset($_POST['delete_agent'])) {
        $agent_id = $conn->real_escape_string($_POST['agent_id']);
        
        $sql = "DELETE FROM Agents WHERE AgentID=$agent_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Agent deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Get agent to edit
$editAgent = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM Agents WHERE AgentID=$id");
    if ($result->num_rows > 0) {
        $editAgent = $result->fetch_assoc();
    }
}

// Fetch all agents
$agents = $conn->query("SELECT * FROM Agents ORDER BY Name");

// Calculate agent commissions
$agentCommissions = $conn->query("
    SELECT a.AgentID, a.Name, a.CommissionRate, 
           COUNT(p.PolicyID) as PolicyCount,
           SUM(p.PremiumAmount) as TotalPremium,
           SUM(p.PremiumAmount * a.CommissionRate / 100) as TotalCommission
    FROM Agents a
    LEFT JOIN Policies p ON a.AgentID = p.AgentID AND p.Status = 'Active'
    GROUP BY a.AgentID, a.Name, a.CommissionRate
    ORDER BY TotalCommission DESC
");

include '../includes/header.php';
?>

<h2 class="mb-4">Agent Management</h2>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <?php echo $editAgent ? 'Edit Agent' : 'Add New Agent'; ?>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <?php if ($editAgent): ?>
                <input type="hidden" name="agent_id" value="<?php echo $editAgent['AgentID']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $editAgent ? $editAgent['Name'] : ''; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $editAgent ? $editAgent['Email'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $editAgent ? $editAgent['Phone'] : ''; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="commission_rate">Commission Rate (%)</label>
                    <input type="number" step="0.01" min="0" max="25" class="form-control" id="commission_rate" name="commission_rate" value="<?php echo $editAgent ? $editAgent['CommissionRate'] : '10.00'; ?>" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-secondary" name="<?php echo $editAgent ? 'update_agent' : 'add_agent'; ?>">
                <?php echo $editAgent ? 'Update Agent' : 'Add Agent'; ?>
            </button>
            
            <?php if ($editAgent): ?>
                <a href="agents.php" class="btn btn-light">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Agent List
            </div>
            <div class="card-body">
                <?php if ($agents->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Commission Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($agent = $agents->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $agent['AgentID']; ?></td>
                                        <td><?php echo $agent['Name']; ?></td>
                                        <td><?php echo $agent['Phone']; ?></td>
                                        <td><?php echo $agent['Email']; ?></td>
                                        <td><?php echo $agent['CommissionRate']; ?>%</td>
                                        <td>
                                            <a href="agents.php?action=edit&id=<?php echo $agent['AgentID']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <form method="post" action="" style="display:inline;" id="delete-agent-<?php echo $agent['AgentID']; ?>">
                                                <input type="hidden" name="agent_id" value="<?php echo $agent['AgentID']; ?>">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $agent['AgentID']; ?>, 'agent')">Delete</button>
                                                <input type="hidden" name="delete_agent" value="1">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No agents found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                Agent Commissions
            </div>
            <div class="card-body">
                <?php if ($agentCommissions->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Agent</th>
                                    <th>Rate</th>
                                    <th>Policies</th>
                                    <th>Premium Total</th>
                                    <th>Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($commission = $agentCommissions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $commission['Name']; ?></td>
                                        <td><?php echo $commission['CommissionRate']; ?>%</td>
                                        <td><?php echo $commission['PolicyCount']; ?></td>
                                        <td>$<?php echo number_format($commission['TotalPremium'], 2); ?></td>
                                        <td>$<?php echo number_format($commission['TotalCommission'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No commission data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
