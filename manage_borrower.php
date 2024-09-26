<?php include 'db_connect.php'; // Added missing semicolon

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $mobile_no = $_POST['mobile_no'];
    $email = $_POST['email'];
    $tax_id = $_POST['tax_id'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format';
        exit;
    }

    // Validate mobile number (example regex for +XX-XXXXXXXXXX format)
    if (!preg_match('/^\+\d{1,3}-\d{9,10}$/', $mobile_no)) {
        echo 'Invalid mobile number format';
        exit;
    }

    // Use a prepared statement to insert new borrower
    $stmt = $conn->prepare("INSERT INTO borrowers (firstname, lastname, address, mobile_no, email, tax_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $address, $mobile_no, $email, $tax_id);

    if ($stmt->execute()) {
        echo 1;
    } else {
        echo 0;
    }
    $stmt->close();
}

if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM borrowers WHERE id=" . $_GET['id']);
    foreach ($qry->fetch_array() as $k => $val) {
        $$k = $val;
    }
}
?>
<div class="container-fluid">
    <div class="col-lg-12">
        <form id="manage-borrower">
            <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="" class="control-label">First Name</label>
                        <input name="firstname" class="form-control" required="" value="<?php echo isset($firstname) ? $firstname : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Last Name</label>
                        <input name="lastname" class="form-control" required="" value="<?php echo isset($lastname) ? $lastname : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <label for="">Address</label>
                    <textarea name="address" id="" cols="30" rows="2" class="form-control" required=""><?php echo isset($address) ? $address : '' ?></textarea>
                </div>
                <div class="col-md-5">
                    <div class="">
                        <label for="">Mobile No</label>
                        <input type="text" class="form-control" name="mobile_no" value="<?php echo isset($mobile_no) ? $mobile_no : '' ?>">
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <label for="">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo isset($email) ? $email : '' ?>">
                </div>
                <div class="col-md-5">
                    <div class="">
                        <label for="">Tax ID</label>
                        <input type="text" class="form-control" name="tax_id" value="<?php echo isset($tax_id) ? $tax_id : '' ?>">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#manage-borrower').submit(function (e) {
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_borrower',
            method: 'POST',
            data: $(this).serialize(),
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Borrower data successfully saved.", "success");
                    setTimeout(function (e) {
                        location.reload();
                    }, 1500);
                }
            }
        });
    });
</script>
