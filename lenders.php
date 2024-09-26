
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
include_once 'db_connect.php';

session_start();
if ($_SESSION['login_type'] != 'lender') {
    header('Location: login.php');
    exit;
}

<h1>Welcome Lender</h1>

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle borrower request response
if (isset($_POST['respond'])) {
    $request_id = $_POST['request_id'];
    $response = $_POST['response'];
    $sql = "UPDATE borrower_requests SET response = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $response, $request_id);
    $stmt->execute();
}

// Fetch borrower requests and join with borrowers to get full name
$sql = "SELECT br.id, br.borrower_email, b.firstname, b.lastname, br.amount, br.request_date, br.response 
        FROM borrower_requests br
        JOIN borrowers b ON br.borrower_name = b.id";
$result = mysqli_query($conn, $sql);
$requests = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch lenders from the database
$lenders_sql = "SELECT * FROM lenders";
$lenders_result = mysqli_query($conn, $lenders_sql);
$lenders = mysqli_fetch_all($lenders_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lender Dashboard</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body id="page-top">

    <div id="wrapper">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <?php include 'topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Lender Dashboard</h1>

                    <!-- Borrower Requests Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Borrower Requests</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="borrower-requests">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Borrower</th>
                                        <th>Email</th>
                                        <th>Loan Amount</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($requests): ?>
                                        <?php $i = 1; foreach ($requests as $row): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($row['borrower_email'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($row['request_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td id="status-<?= $row['id'] ?>"><?= $row['response'] ?: 'Pending' ?></td>
                                                <td class="actions">
                                                    <a href="#" onclick="approveRequest(<?= $row['id'] ?>)">Approve</a>
                                                    <a href="#" onclick="rejectRequest(<?= $row['id'] ?>)">Reject</a>
                                                    <a href="borrower_request_form.php?request_id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7">No pending requests found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Add New Lender Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Add New Lender</h6>
            </div>
            <div class="card-body">
                <button class="btn btn-success" data-toggle="modal" data-target="#addLenderModal">Add Lender</button>
            </div>
        </div>

        <!-- Add New Lender Modal -->
        <div class="modal fade" id="addLenderModal" tabindex="-1" role="dialog" aria-labelledby="addLenderModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLenderModalLabel">Add New Lender</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="manage_lender.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="lender_name">Name</label>
                                <input type="text" class="form-control" name="lender_name" required>
                            </div>
                            <div class="form-group">
                                <label for="lender_email">Email</label>
                                <input type="email" class="form-control" name="lender_email" required>
                            </div>
                            <div class="form-group">
                                <label for="loan_amount">Loan Amount</label>
                                <input type="number" class="form-control" name="loan_amount" required>
                            </div>
                            <div class="form-group">
                                <label for="lender_status">Status</label>
                                <select class="form-control" name="lender_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

                    <!-- Lender List Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Lender List</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Loan Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($lenders): ?>
                                        <?php foreach ($lenders as $lender): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($lender['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($lender['borrower_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($lender['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($lender['amount'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($lender['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="actions">
                                    <a href="#" onclick="approveLender(<?= $lender['lender_id'] ?>)">Approve</a>
                                    <a href="#" onclick="rejectLender(<?= $lender['lender_id'] ?>)">Reject</a>
                                    <a href="lender_details.php?lender_id=<?= $lender['lender_id'] ?>" class="btn btn-info btn-sm">View</a>                                   </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6">No lenders found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Message Box -->
        <div id="messageBox" class="message-box">
            <h5>Send Message</h5>
            <form>
                <div class="form-group">
                    <label for="messageContent">Message:</label>
                    <textarea id="messageContent" rows="4" class="form-control" required></textarea>
                </div>
                <button type="button" class="btn btn-primary" onclick="sendMessage()">Send</button>
                <button type="button" class="btn btn-secondary" onclick="hideMessageBox()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function approveRequest(requestId) {
            document.getElementById('status-' + requestId).innerText = 'Approved';
            showMessageBox();
        }

        function rejectRequest(requestId) {
            document.getElementById('status-' + requestId).innerText = 'Rejected';
            showMessageBox();
        }

        function showMessageBox() {
            document.getElementById('messageBox').style.display = 'block';
        }

        function hideMessageBox() {
            document.getElementById('messageBox').style.display = 'none';
        }

        function sendMessage() {
            const messageContent = document.getElementById('messageContent').value;
            alert('Message sent: ' + messageContent);
            hideMessageBox();
        }

        function approveLender(lenderId) {
            showMessageBox();
        }

        function rejectLender(lenderId) {
            showMessageBox();
        }
    </script>
                </div>
                <!-- End of Page Content -->
            </div>
            <!-- Footer -->
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>
</body>
</html>

