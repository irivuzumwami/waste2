<?php
// admin/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/database.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM customer");
$totalCustomers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM workers");
$totalWorkers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(amount) as total FROM payment");
$totalRevenue = $stmt->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EcoWaste</title>
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #fbbf24;
            --glass-bg: rgba(255, 255, 255, 0.12);
            --bg-dark: #0f172a;
            --footer-bg: #0b1120;
            --text-muted: #94a3b8;
            --teal-accent: #00c49a;
            --sidebar-width: 260px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-dark);
            color: #fff;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100%;
            background: rgba(11, 17, 32, 0.98);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255,255,255,0.1);
            padding: 2rem 1rem;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--secondary);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo span {
            color: var(--teal-accent);
        }
        
        .user-info {
            text-align: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 2rem;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-menu li {
            margin-bottom: 0.5rem;
        }
        
        .nav-menu a {
            display: block;
            padding: 0.8rem 1rem;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }
        
        .nav-menu a:hover, .nav-menu a.active {
            background: var(--primary);
            color: var(--secondary);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .logout-btn {
            background: rgba(255,0,0,0.2);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            color: #ff4444;
            text-decoration: none;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255,255,255,0.05);
            padding: 1.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--secondary);
        }
        
        .stat-card h3 {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--secondary);
        }
        
        /* Tables */
        .section-card {
            background: rgba(255,255,255,0.05);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        th {
            color: var(--secondary);
        }
        
        .btn {
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        
        .btn-danger {
            background: #ff4444;
            color: #fff;
        }
        
        .btn-sm {
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
        }
        
        .add-btn {
            background: var(--teal-accent);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Eco<span>Waste</span></div>
        <div class="user-info">
            <p>Welcome,</p>
            <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
            <p style="font-size: 0.8rem; color: var(--secondary);">Administrator</p>
        </div>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="customers.php">👥 Customers</a></li>
            <li><a href="workers.php">👷 Workers</a></li>
            <li><a href="orders.php">📦 Orders</a></li>
            <li><a href="payments.php">💰 Payments</a></li>
            <li><a href="support.php">💬 Support Tickets</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="stat-number"><?php echo $totalCustomers; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="stat-number"><?php echo $totalOrders; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Workers</h3>
                <div class="stat-number"><?php echo $totalWorkers; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="stat-number">RWF <?php echo number_format($totalRevenue); ?></div>
            </div>
        </div>
        
        <div class="section-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title">Recent Orders</h2>
                <a href="orders.php" class="btn btn-primary">View All</a>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Customer ID</th><th>Pickup Date</th><th>Created At</th></tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
                    while($row = $stmt->fetch()) {
                        echo "<tr><td>{$row['id']}</td><td>{$row['customer_id']}</td><td>{$row['pickup_date']}</td><td>{$row['created_at']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="section-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title">Recent Payments</h2>
                <a href="payments.php" class="btn btn-primary">View All</a>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Order ID</th><th>Amount</th><th>Payment Date</th></tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM payment ORDER BY id DESC LIMIT 5");
                    while($row = $stmt->fetch()) {
                        echo "<tr><td>{$row['id']}</td><td>{$row['order_id']}</td><td>RWF " . number_format($row['amount']) . "</td><td>{$row['payment_date']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>