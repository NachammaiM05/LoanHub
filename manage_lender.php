<?php 
// manage_lender.php

// Database connection
$host = 'localhost';
$db = 'loanhub_db';
$user = 'root'; // Change if different
$pass = ''; // Change if different

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle request status update
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE borrower_requests SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $request_id);
    if ($stmt->execute()) {
        echo "<p>Request status updated successfully!</p>";
    } else {
        echo "<p>Error updating status: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle adding a new lender
if (isset($_POST['add_lender'])) {
    $lender_name = $_POST['lender_name'];
    $lender_email = $_POST['lender_email'];
    $stmt = $conn->prepare("INSERT INTO lenders (name, email) VALUES (?, ?)");
    $stmt->bind_param('ss', $lender_name, $lender_email);
    if ($stmt->execute()) {
        echo "<p>New lender added successfully!</p>";
    } else {
        echo "<p>Error adding lender: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch loan requests
$lender_id = 1; // Assume logged-in lender ID; replace with dynamic value
$stmt = $conn->prepare("SELECT * FROM borrower_requests WHERE lender_id = ?");
$stmt->bind_param('i', $lender_id);
$stmt->execute();
$requests = $stmt->get_result();

// Fetch notifications
$stmt = $conn->prepare("SELECT * FROM notifications WHERE lender_id = ? AND is_read = 0");
$stmt->bind_param('i', $lender_id);
$stmt->execute();
$notifications = $stmt->get_result();

// Fetch lenders
$stmt = $conn->prepare("SELECT * FROM lenders");
$stmt->execute();
$lenders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lender Dashboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions a {
            margin-right: 5px;
            text-decoration: none;
            color: blue;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .hidden {
            display: none;
        }
        .message-box {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Lender Dashboard</h1>
    </header>
    <main>
        <section>
            <h2>Loan Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Borrower Email</th>
                        <th>Amount</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['borrower_email']); ?></td>
                        <td><?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td class="actions">
                            <a href="view_request.php?request_id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-info btn-sm">View</a>
                            <a href="#" onclick="updateStatus(<?php echo htmlspecialchars($row['id']); ?>, 'approved')" class="btn btn-success btn-sm">Approve</a>
                            <a href="#" onclick="updateStatus(<?php echo htmlspecialchars($row['id']); ?>, 'rejected')" class="btn btn-danger btn-sm">Reject</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Add New Lender</h2>
            <form method="post" action="">
                <label for="lender_name">Lender Name:</label>
                <input type="text" id="lender_name" name="lender_name" required>
                <br>
                <label for="lender_email">Lender Email:</label>
                <input type="email" id="lender_email" name="lender_email" required>
                <br>
                <input type="submit" name="add_lender" value="Add Lender">
            </form>
        </section>

        <section>
        <!-- Lender List Section -->
        <h4>Lender List</h4>
        <table class="table table-bordered" id="lender-list">
        <colgroup>
                <col width="10%">
                <col width="60%">
                <col width="60%">
                <col width="30%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Lender</th>
                    <th class="text-center">Created and Updated Details</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while ($row = $lender_query->fetch_assoc()):
                ?>
                <tr>
                    <td class="text-center"><?= htmlspecialchars($i++, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <p>Name: <b><?= htmlspecialchars(ucwords($row['borrower_name']), ENT_QUOTES, 'UTF-8') ?></b></p>
                        <p><small>Email: <b><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></b></small></p>
                    </td>
                    <td class="text-center">
                        Created: <?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?><br>
                        Updated: <?= htmlspecialchars($row['updated_at'], ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td class="text-center">
                        <button class="btn btn-outline-primary btn-sm edit_lender" type="button" data-id="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-outline-danger btn-sm delete_lender" type="button" data-id="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </section>

        <section>
            <h2>Notifications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Notification ID</th>
                        <th>Message</th>
                        <th>Created At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($notification = $notifications->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($notification['id']); ?></td>
                        <td><?php echo htmlspecialchars($notification['message']); ?></td>
                        <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                        <td>Unread</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script>
        function updateStatus(requestId, status) {
            if (confirm('Are you sure you want to update the status to ' + status + '?')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = '';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'request_id';
                inputId.value = requestId;
                form.appendChild(inputId);

                const inputStatus = document.createElement('input');
                inputStatus.type = 'hidden';
                inputStatus.name = 'status';
                inputStatus.value = status;
                form.appendChild(inputStatus);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
