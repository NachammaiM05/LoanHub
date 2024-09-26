
<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
} else {
    // Redirect to the appropriate dashboard based on user role
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin_dashboard.php');
    } elseif ($_SESSION['role'] == 'lender') {
        header('Location: lender_dashboard.php');
    } elseif ($_SESSION['role'] == 'borrower') {
        header('Location: borrower_dashboard.php');
    }
    exit;
}
?>
<?php
// Start session and check if the admin is logged in
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Dashboard </title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SB Admin 2 styles -->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Animate.css for Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .animated {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .card-icon {
            font-size: 2.5rem;
            color: #5a5c69;
        }
        .navbar-brand img {
            width: 30px;
            margin-right: 10px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-toggle" id="accordionSidebar">
            <li class="nav-item active">
                <a class="nav-link" href="home.php">Dashboard</a>
            </li>
            <?php if ($_SESSION['login_type'] == 1): // Admin ?>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_loans.php">Manage Loans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_payments.php">Manage Payments</a>
                </li>
            <?php elseif ($_SESSION['login_type'] == 2): // Lender ?>
                <li class="nav-item">
                    <a class="nav-link" href="my_loans.php">My Loans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payments_received.php">Payments Received</a>
                </li>
            <?php elseif ($_SESSION['login_type'] == 3): // Borrower ?>
                <li class="nav-item">
                    <a class="nav-link" href="my_loans.php">My Loans</a>
                </li>
                
            <?php endif; ?>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
            <?php echo "Welcome back ".($_SESSION['login_type'] == 3 ? "Dr. ".$_SESSION['login_name'].','.$_SESSION['login_name_pref'] : $_SESSION['login_name'])."!"  ?>
									
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin Name</span>
                                <img class="img-profile rounded-circle" src="assets/img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                <h2>Welcome, Admin</h2>
                    <!-- Uni Modal -->
                    <?php include 'uni_modal.php'; ?>
                    <!-- Dashboard Overview Section -->
                    <h1 class="h3 mb-4 text-gray-800">Dashboard Overview</h1>

                    <!-- Cards for Quick Stats -->
                    <div class="row">
                        <!-- Users -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2 animated">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                    $users = $conn->query("SELECT count(id) as total FROM users");
                                                    echo $users && $users->num_rows > 0 ? $users->fetch_array()['total'] : "0";
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-primary stretched-link" href="index.php?page=users">View Users</a>
                                    <div class="small text-primary">
                                        <i class="fas fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Loans -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2 animated">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Loans</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                               <?php 
                                                   $loans = $conn->query("SELECT * FROM loan_list WHERE status = 2");
                                                   echo $loans && $loans->num_rows > 0 ? $loans->num_rows : "0";
                                               ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-success stretched-link" href="index.php?page=loans">View Loan List</a>
                                    <div class="small text-success">
                                        <i class="fas fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Borrowers -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2 animated">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Borrowers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                    $borrowers = $conn->query("SELECT * FROM borrowers");
                                                    echo $borrowers && $borrowers->num_rows > 0 ? $borrowers->num_rows : "0";
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa fa-user-friends card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-warning stretched-link" href="index.php?page=borrowers">View Borrowers</a>
                                    <div class="small text-warning">
                                        <i class="fas fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Receivable -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2 animated">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Receivable</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                $payments = $conn->query("SELECT sum(amount - penalty_amount) as total FROM payments WHERE date(date_created) = '".date("Y-m-d")."'");
                                                $loans = $conn->query("SELECT sum(l.amount + (l.amount * (p.interest_percentage/100))) as total FROM loan_list l INNER JOIN loan_plan p ON p.id = l.plan_id WHERE l.status = 2");
                                                $total_loans = $loans && $loans->num_rows > 0 ? $loans->fetch_array()['total'] : "0";
                                                $total_payments = $payments && $payments->num_rows > 0 ? $payments->fetch_array()['total'] : "0";
                                                echo number_format($total_loans - $total_payments, 2);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa fa-money-bill-wave card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-info stretched-link" href="index.php?page=receivables">View Receivables</a>
                                    <div class="small text-info">
                                        <i class="fas fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Lenders -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2 animated">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Lenders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                $lenders = $conn->query("SELECT count(id) as total FROM lenders");
                                                echo $lenders && $lenders->num_rows > 0 ? $lenders->fetch_array()['total'] : "0";
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa fa-building card-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-primary stretched-link" href="index.php?page=lenders">View Lenders</a>
                                    <div class="small text-primary">
                                        <i class="fas fa-angle-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End of Page Content -->
                    </div>
                </div>
                <!-- End of Content Wrapper -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer Section -->
    <footer class="footer bg-light text-center py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-left">
                <img src="assets/img/logo.png" alt="loanhub">
                </div>
                <div class="col-md-6 text-right">
                    <p>© 2024 Your Company. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
       

    <!-- SB Admin 2 JavaScript -->
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom scripts for charts or modals -->
    <script>
        // Your custom scripts for dashboard features
    </script>
</body>
</html>
