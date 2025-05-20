<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <?php
    // Define base URL for all links
    $base_url = '';
    $current_path = $_SERVER['PHP_SELF'];
    
    if (strpos($current_path, '/pages/') !== false || 
        strpos($current_path, '/reports/') !== false) {
        $base_url = '../';
        $css_path = '../css/style.css';
        $js_path = '../js/script.js';
    } else {
        $base_url = './';
        $css_path = './css/style.css';
        $js_path = './js/script.js';
    }
    ?>
    
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">Insurance Management System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>pages/customers.php">Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>pages/policies.php">Policies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>pages/claims.php">Claims</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>pages/payments.php">Payments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>pages/agents.php">Agents</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-toggle="dropdown">
                            Reports
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo $base_url; ?>reports/policy_reports.php">Policy Reports</a>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>reports/claim_reports.php">Claim Reports</a>
                            <a class="dropdown-item" href="<?php echo $base_url; ?>reports/payment_reports.php">Payment Reports</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
