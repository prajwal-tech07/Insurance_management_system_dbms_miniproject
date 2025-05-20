<?php
include '../database/db_config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new customer
    if (isset($_POST['add_customer'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $address = $conn->real_escape_string($_POST['address']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $dob = $conn->real_escape_string($_POST['dob']);
        $occupation = $conn->real_escape_string($_POST['occupation']);
        
        $sql = "INSERT INTO Customers (Name, Address, Phone, Email, DateOfBirth, Occupation) 
                VALUES ('$name', '$address', '$phone', '$email', '$dob', '$occupation')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Customer added successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Update existing customer
    if (isset($_POST['update_customer'])) {
        $id = $conn->real_escape_string($_POST['customer_id']);
        $name = $conn->real_escape_string($_POST['name']);
        $address = $conn->real_escape_string($_POST['address']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $dob = $conn->real_escape_string($_POST['dob']);
        $occupation = $conn->real_escape_string($_POST['occupation']);
        
        $sql = "UPDATE Customers SET 
                Name='$name', 
                Address='$address', 
                Phone='$phone', 
                Email='$email', 
                DateOfBirth='$dob', 
                Occupation='$occupation' 
                WHERE CustomerID=$id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Customer updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
    
    // Delete customer
    if (isset($_POST['delete_customer'])) {
        $id = $conn->real_escape_string($_POST['customer_id']);
        
        $sql = "DELETE FROM Customers WHERE CustomerID=$id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Customer deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "danger";
        }
    }
}

// Get customer to edit
$editCustomer = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM Customers WHERE CustomerID=$id");
    if ($result->num_rows > 0) {
        $editCustomer = $result->fetch_assoc();
    }
}

// Fetch all customers
$customers = $conn->query("SELECT * FROM Customers ORDER BY Name");

include '../includes/header.php';
?>

<h2 class="mb-4">Customer Management</h2>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <?php echo $editCustomer ? 'Edit Customer' : 'Add New Customer'; ?>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <?php if ($editCustomer): ?>
                <input type="hidden" name="customer_id" value="<?php echo $editCustomer['CustomerID']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $editCustomer ? $editCustomer['Name'] : ''; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $editCustomer ? $editCustomer['Email'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $editCustomer ? $editCustomer['Phone'] : ''; ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $editCustomer ? $editCustomer['DateOfBirth'] : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required><?php echo $editCustomer ? $editCustomer['Address'] : ''; ?></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="occupation">Occupation</label>
                    <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo $editCustomer ? $editCustomer['Occupation'] : ''; ?>" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" name="<?php echo $editCustomer ? 'update_customer' : 'add_customer'; ?>">
                <?php echo $editCustomer ? 'Update Customer' : 'Add Customer'; ?>
            </button>
            
            <?php if ($editCustomer): ?>
                <a href="customers.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        Customer List
    </div>
    <div class="card-body">
        <?php if ($customers->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Date of Birth</th>
                            <th>Occupation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $customer['CustomerID']; ?></td>
                                <td><?php echo $customer['Name']; ?></td>
                                <td><?php echo $customer['Phone']; ?></td>
                                <td><?php echo $customer['Email']; ?></td>
                                <td><?php echo $customer['DateOfBirth']; ?></td>
                                <td><?php echo $customer['Occupation']; ?></td>
                                <td>
                                    <a href="customers.php?action=edit&id=<?php echo $customer['CustomerID']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="post" action="" style="display:inline;" id="delete-customer-<?php echo $customer['CustomerID']; ?>">
                                        <input type="hidden" name="customer_id" value="<?php echo $customer['CustomerID']; ?>">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $customer['CustomerID']; ?>, 'customer')">Delete</button>
                                        <input type="hidden" name="delete_customer" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
