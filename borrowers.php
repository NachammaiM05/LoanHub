
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
<?php include 'db_connect.php'; ?>
<?php
session_start();
if ($_SESSION['login_type'] != 'borrower') {
    header('Location: login.php');
    exit;
}
?>
<h1>Welcome Borrower</h1>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <large class="card-title">
                    <b>Borrower List</b>
                </large>
                <button class="btn btn-primary btn-block col-md-2 float-right" type="button" id="new_borrower"><i class="fa fa-plus"></i> New Borrower</button>
                <button class="btn btn-primary btn-block col-md-2 float-middle" type="button" id="request_borrower"><i class="fa fa-plus"></i> Request Borrower</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="borrower-list">
                    <colgroup>
                        <col width="10%">
                        <col width="35%">
                        <col width="30%">
                        <col width="15%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Borrower</th>
                            <th class="text-center">Current Loan</th>
                            <th class="text-center">Next Payment Schedule</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM borrowers ORDER BY id DESC");
                        while ($row = $qry->fetch_assoc()) :
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td>
                                    <p>Name: <b><?php echo ucwords($row['firstname'] . ' ' . $row['lastname'] . " "); ?></b></p>
                                    <p><small>Address: <b><?php echo $row['address']; ?></b></small></p>
                                    <p><small>Mobile No: <b><?php echo $row['mobile_no']; ?></b></small></p>
                                    <p><small>Email: <b><?php echo $row['email']; ?></b></small></p>
                                    <p><small>Tax ID: <b><?php echo $row['tax_id']; ?></b></small></p>
                                </td>
                                <td class="">Progress</td>
                                <td class="">Due Date</td>
                                <td class="text-center">
                                    <button class="btn btn-outline-primary btn-sm edit_borrower" type="button" data-id="<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-outline-danger btn-sm delete_borrower" type="button" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Borrower Request Form Modal -->
<div class="modal fade" id="borrowerRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Borrower Request Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="borrowerRequestForm" action="borrower_request_form.php" method="post">
                <input type="hidden" name="action" value="add_request">
                    <div class="form-group">
                        <label for="borrower_name">Borrower Name:</label>
                        <input type="text" id="borrower_name" name="borrower_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="borrower_email">Borrower Email:</label>
                        <input type="email" id="borrower_email" name="borrower_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="loan_amount">Loan Amount:</label>
                        <input type="number" id="loan_amount" name="loan_amount" class="form-control" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary" value="Submit Request">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    td p {
        margin: unset;
    }
    td img {
        width: 8vw;
        height: 12vh;
    }
    td {
        vertical-align: middle !important;
    }
</style>

<script>
    $(document).ready(function() {
        $('#borrower-list').DataTable({
            "order": [],
            "paging": true,
            "searching": true,
            "info": true,
            "bLengthChange": true,
            "bFilter": true,
            "bSort": false
        });

        $('#new_borrower').click(function() {
            uni_modal("New Borrower", "manage_borrower.php", 'mid-large');
        });

        $('#request_borrower').click(function() {
            $('#borrowerRequestModal').modal('show'); // Show the modal when the button is clicked
        });

        $('.edit_borrower').click(function() {
            uni_modal("Edit Borrower", "manage_borrower.php?id=" + $(this).attr('data-id'), 'mid-large');
        });

        $('.delete_borrower').click(function() {
            _conf("Are you sure to delete this borrower?", "delete_borrower", [$(this).attr('data-id')]);
        });
    });

    function delete_borrower($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_borrower',
            method: 'POST',
            data: { id: $id },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Borrower successfully deleted", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert_toast("Failed to delete borrower", 'danger');
                }
            },
            error: function() {
                alert_toast("An error occurred", 'danger');
            },
            complete: function() {
                end_load();
            }
        });
    }
</script>
