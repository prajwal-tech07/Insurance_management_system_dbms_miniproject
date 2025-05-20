# Insurance Management System

## Overview
The Insurance Management System is a comprehensive web-based application designed to streamline and automate insurance operations. This system centralizes customer data, policy management, claims processing, and payment tracking into a unified platform, making insurance operations more efficient and user-friendly.

## Features

### Customer Management
- Store and manage comprehensive client information  
- Quick search and retrieval of customer records  
- Track customer history and policy details  

### Policy Administration
- Manage the entire policy lifecycle from creation to renewal or cancellation  
- Support for multiple insurance types (Auto, Home, Life, Health, Travel, etc.)  
- Automated premium calculations and policy generation  

### Claims Processing
- Streamlined claims submission and review process  
- Real-time tracking of claim status  
- Documentation management for claims  

### Payment Management
- Record and track premium payments  
- Support for multiple payment methods  
- Generate payment receipts and track payment history  

### Agent Management
- Track agent performance and calculate commissions  
- Manage agent-client relationships  
- Monitor agent portfolios  

### Reporting and Analytics
- Generate comprehensive reports on policies, claims, and payments  
- Analyze business performance with visual dashboards  
- Track expiring policies and pending claims  

## Tech Stack
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 4  
- **Backend**: PHP  
- **Database**: MySQL  
- **Server**: Apache  

## Installation and Setup

### Prerequisites
- PHP 7.0 or higher  
- MySQL 5.6 or higher  
- Apache web server  
- Web browser (Chrome, Firefox, Safari, etc.)

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/insurance-management-system.git
   ```

2. **Create a MySQL database named `insurance_management`**

3. **Import the database schema**:
   - Open your MySQL client (phpMyAdmin or MySQL command line)
   - Create a new database named `insurance_management`
   - Run the SQL queries provided in the project to create tables and sample data

4. **Configure database connection**:
   - Open `database/db_config.php`
   - Update the database credentials:
     ```php
     $host = "localhost";
     $username = "your_username";
     $password = "your_password";
     $database = "insurance_management";
     ```

5. **Place the project files in your web server's document root (e.g., `htdocs` for XAMPP)**

6. **Access the application through your web browser**:
   ```
   http://localhost/insurance-management-system
   ```

## Project Structure

```
InsuranceManagementSystem/
│
├── database/
│   └── db_config.php         (Database connection configuration)
│
├── includes/
│   ├── header.php            (Common header for all pages)
│   └── footer.php            (Common footer for all pages)
│
├── css/
│   └── style.css             (CSS styling for the application)
│
├── js/
│   └── script.js             (JavaScript for interactive features)
│
├── pages/
│   ├── customers.php         (Customer management)
│   ├── policies.php          (Policy management)
│   ├── claims.php            (Claims management)
│   ├── payments.php          (Payment management)
│   └── agents.php            (Agent management)
│
├── reports/
│   ├── policy_reports.php    (Policy related reports)
│   ├── claim_reports.php     (Claim related reports)
│   └── payment_reports.php   (Payment related reports)
│
└── index.php                 (Main entry point of the application)
```

## Business Value
- **Improved Productivity**: Automation reduces manual workload by up to 40%  
- **Enhanced Customer Satisfaction**: Faster response times and accurate information  
- **Data-Driven Decision Making**: Comprehensive reporting and analytics  
- **Regulatory Compliance**: Built-in compliance checks and documentation  
- **Scalability**: Easily accommodates business growth  

## Future Enhancements
- User authentication and role-based access control  
- Email notifications for policy renewals and claim updates  
- Integration with payment gateways  
- Mobile application for customers and agents  
- Advanced analytics and business intelligence features  
