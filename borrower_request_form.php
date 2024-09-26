<?php
session_start();
include('db_connect.php');

// Validate session (assume session holds borrower_id)
if (!isset($_SESSION['borrower_id'])) {
    die("Unauthorized access.");
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $borrower_name = htmlspecialchars($_POST['borrower_name']);
    $loan_amount = floatval($_POST['loan_amount']);
    $borrower_id = $_SESSION['borrower_id']; // Get from session
    $lender_id = intval($_POST['lender_id']); // Get from form

    // Input validation
    if ($loan_amount <= 0 || empty($borrower_name)) {
        echo "<script>alert('Invalid input. Please check your name or loan amount.');</script>";
    } else {
        // Insert the borrower request
        $stmt = $conn->prepare("INSERT INTO borrower_requests (borrower_id, lender_id, amount, request_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iid", $borrower_id, $lender_id, $loan_amount);
        if ($stmt->execute()) {
            // Send notification to the lender
            $message = "You have a new loan request from Borrower: $borrower_name for an amount of $$loan_amount.";
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, user_type, message, created_at) VALUES (?, 'lender', ?, NOW())");
            $stmt->bind_param("is", $lender_id, $message);
            $stmt->execute();
            echo "<script>alert('Request sent successfully.');</script>";
        } else {
            echo "<script>alert('Failed to send request.');</script>";
        }
    }
}

// Fetch borrower requests
$requests_query = $conn->query("
   SELECT br.*, b.name as borrower_name, b.email as borrower_email, br.amount as loan_amount, br.status
   FROM borrower_requests br
   JOIN borrowers b ON br.borrower_id = b.id
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Request Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Borrower Request Form</h2>
            <form action="borrower_request_form.php" method="post">
                <div class="form-group">
                    <label for="borrower_name">Your Name:</label>
                    <input type="text" id="borrower_name" name="borrower_name" required>
                </div>
                <div class="form-group">
                    <label for="loan_amount">Loan Amount:</label>
                    <input type="number" id="loan_amount" name="loan_amount" min="1" required>
                </div>
                <div class="form-group">
                    <label for="lender_id">Select Lender:</label>
                    <select id="lender_id" name="lender_id">
                        <!-- Add lender options dynamically from database -->
                    </select>
                </div>
                <button type="submit">Submit Request</button>
            </form>
        </div>
    </div>
    
    <h3>Borrower Requests</h3>
    <table border="1">
        <tr>
            <th>Borrower Name</th>
            <th>Email</th>
            <th>Loan Amount</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $requests_query->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['borrower_name']); ?></td>
            <td><?php echo htmlspecialchars($row['borrower_email']); ?></td>
            <td><?php echo htmlspecialchars($row['loan_amount']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
