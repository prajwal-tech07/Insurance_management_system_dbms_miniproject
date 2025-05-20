<?php
include '../database/db_config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new payment
    if (isset($_POST['add_payment'])) {
        $policy_id = $conn->real_escape_string($_POST['policy_id']);
        $payment_date = $conn->real_escape_string($_POST['payment_date']);
        $amount = $conn->real_escape_string($_POST['amount']);
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        
        $sql = "INSERT INTO Payments (PolicyID, PaymentDate, Amount, PaymentMethod) 
                VALUES ('$policy_id', '$payment_date', '$amount', '$payment_method')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Payment recorded successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Update existing payment
    if (isset($_POST['update_payment'])) {
        $payment_id = $conn->real_escape_string($_POST['payment_id']);
        $policy_id = $conn->real_escape_string($_POST['policy_id']);
        $payment_date = $conn->real_escape_string($_POST['payment_date']);
        $amount = $conn->real_escape_string($_POST['amount']);
        $payment_method = $conn->real_escape_string($_POST['payment_method']);
        
        $sql = "UPDATE Payments SET 
                PolicyID='$policy_id', 
                PaymentDate='$payment_date', 
                Amount='$amount', 
                PaymentMethod='$payment_method' 
                WHERE PaymentID=$payment_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Payment updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Delete payment
    if (isset($_POST['delete_payment'])) {
        $payment_id = $conn->real_escape_string($_POST['payment_id']);
        
        $sql = "DELETE FROM Payments WHERE PaymentID=$payment_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Payment deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Get payment to edit
$editPayment = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM Payments WHERE PaymentID=$id");
    if ($result->num_rows > 0) {
        $editPayment = $result->fetch_assoc();
    }
}

// Fetch all active policies for dropdown
$policies = $conn->query("
    SELECT p.PolicyID, p.PolicyType, p.PremiumAmount, c.Name as CustomerName
    FROM Policies p
    JOIN Customers c ON p.CustomerID = c.CustomerID
    WHERE p.Status = 'Active'
    ORDER BY p.PolicyID DESC
");

// Fetch all payments with policy and customer details
$payments = $conn->query("
    SELECT pm.*, p.PolicyType, c.Name as CustomerName
    FROM Payments pm
    JOIN Policies p ON pm.PolicyID = p.PolicyID
    JOIN Customers c ON p.CustomerID = c.CustomerID
    ORDER BY pm.PaymentDate DESC
");

include '../includes/header.php';
?>

<h2 class="mb-4">Payment Management</h2>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <?php echo $editPayment ? 'Edit Payment' : 'Record New Payment'; ?>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <?php if ($editPayment): ?>
                <input type="hidden" name="payment_id" value="<?php echo $editPayment['PaymentID']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="policy_id">Policy</label>
                    <select class="form-control" id="policy_id" name="policy_id" required>
                        <option value="">Select Policy</option>
                        <?php 
                        // Reset the result pointer if needed
                        if ($policies) {
                            $policies->data_seek(0);
                            while ($policy = $policies->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $policy['PolicyID']; ?>" 
                                <?php echo ($editPayment && $editPayment['PolicyID'] == $policy['PolicyID']) ? 'selected' : ''; ?>
                                data-premium="<?php echo $policy['PremiumAmount']; ?>">
                                Policy #<?php echo $policy['PolicyID']; ?> - <?php echo $policy['PolicyType']; ?> - <?php echo $policy['CustomerName']; ?>
                                ($<?php echo number_format($policy['PremiumAmount'], 2); ?>)
                            </option>
                        <?php 
                            endwhile;
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="payment_date">Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo $editPayment ? $editPayment['PaymentDate'] : date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="amount">Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" value="<?php echo $editPayment ? $editPayment['Amount'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="payment_method">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="Cash" <?php echo ($editPayment && $editPayment['PaymentMethod'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Credit Card" <?php echo ($editPayment && $editPayment['PaymentMethod'] == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="Debit Card" <?php echo ($editPayment && $editPayment['PaymentMethod'] == 'Debit Card') ? 'selected' : ''; ?>>Debit Card</option>
                        <option value="Bank Transfer" <?php echo ($editPayment && $editPayment['PaymentMethod'] == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-info" name="<?php echo $editPayment ? 'update_payment' : 'add_payment'; ?>">
                <?php echo $editPayment ? 'Update Payment' : 'Record Payment'; ?>
            </button>
            
            <?php if ($editPayment): ?>
                <a href="payments.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        Payment History
    </div>
    <div class="card-body">
        <?php if ($payments->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Customer</th>
                            <th>Policy Type</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $payment['PaymentID']; ?></td>
                                <td><?php echo $payment['CustomerName']; ?></td>
                                <td><?php echo $payment['PolicyType']; ?></td>
                                <td><?php echo $payment['PaymentDate']; ?></td>
                                <td>$<?php echo number_format($payment['Amount'], 2); ?></td>
                                <td><?php echo $payment['PaymentMethod']; ?></td>
                                <td>
                                    <a href="payments.php?action=edit&id=<?php echo $payment['PaymentID']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="post" action="" style="display:inline;" id="delete-payment-<?php echo $payment['PaymentID']; ?>">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['PaymentID']; ?>">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $payment['PaymentID']; ?>, 'payment')">Delete</button>
                                        <input type="hidden" name="delete_payment" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No payments found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const policySelect = document.getElementById('policy_id');
    const amountInput = document.getElementById('amount');
    
    if (policySelect && amountInput) {
        policySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const premium = selectedOption.getAttribute('data-premium');
                amountInput.value = premium;
            }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
