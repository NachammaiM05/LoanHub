<?php include 'db_connect.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Loan List</title>

    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Custom CSS for SB Admin 2 -->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Loan List</h6>
                <button class="btn btn-primary btn-sm" type="button" id="new_application">
                    <i class="fa fa-plus"></i> Create New Application
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <colgroup>
                        <col width="10%">
                        <col width="25%">
                        <col width="25%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Borrower</th>
                            <th class="text-center">Loan Details</th>
                            <th class="text-center">Next Payment Details</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $type = $conn->query("SELECT * FROM loan_types");
                        $type_arr = [];
                        while ($row = $type->fetch_assoc()) {
                            $type_arr[$row['id']] = $row['type_name'];
                        }

                        $plan = $conn->query("SELECT * FROM loan_plan");
                        $plan_arr = [];
                        while ($row = $plan->fetch_assoc()) {
                            $plan_arr[$row['id']] = $row;
                        }

                        $qry = $conn->query("SELECT l.*, CONCAT(b.firstname, ' ', b.lastname) AS borrower_name, b.mobile_no, b.address FROM loan_list l INNER JOIN borrowers b ON b.id = l.borrower_id ORDER BY l.id ASC");
                        while ($row = $qry->fetch_assoc()):
                            $monthly = ($row['amount'] + ($row['amount'] * ($plan_arr[$row['plan_id']]['interest_percentage'] / 100))) / $plan_arr[$row['plan_id']]['months'];
                            $penalty = $monthly * ($plan_arr[$row['plan_id']]['penalty_rate'] / 100);
                            $payments = $conn->query("SELECT * FROM payments WHERE loan_id = " . $row['id']);
                            $paid = $payments->num_rows;
                            $offset = $paid > 0 ? " OFFSET $paid " : "";
                            if ($row['status'] == 2):
                                $next = $conn->query("SELECT * FROM loan_schedules WHERE loan_id = '" . $row['id'] . "' ORDER BY date(date_due) ASC LIMIT 1 $offset")->fetch_assoc()['date_due'];
                            endif;
                            $sum_paid = 0;
                            while ($p = $payments->fetch_assoc()) {
                                $sum_paid += ($p['amount'] - $p['penalty_amount']);
                            }
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++ ?></td>
                            <td>
                                <p>Name: <b><?php echo $row['borrower_name'] ?></b></p>
                                <p><small>Mobile: <b><?php echo $row['mobile_no'] ?></b></small></p>
                                <p><small>Address: <b><?php echo $row['address'] ?></b></small></p>
                            </td>
                            <td>
                                <p>Reference: <b><?php echo $row['ref_no'] ?></b></p>
                                <p><small>Loan Type: <b><?php echo $type_arr[$row['loan_type_id']] ?></b></small></p>
                                <p><small>Plan: <b><?php echo $plan_arr[$row['plan_id']]['months'] . ' month/s [ ' . $plan_arr[$row['plan_id']]['interest_percentage'] . '%, ' . $plan_arr[$row['plan_id']]['penalty_rate'] . '% ]' ?></b></small></p>
                                <p><small>Amount: <b><?php echo number_format($row['amount'], 2) ?></b></small></p>
                                <p><small>Total Payable: <b><?php echo number_format($monthly * $plan_arr[$row['plan_id']]['months'], 2) ?></b></small></p>
                            </td>
                            <td>
                                <?php if ($row['status'] == 2): ?>
                                   <p>Next Payment: <b><?php echo date('M d, Y', strtotime($next)) ?></b></p>

                                    <p><small>Monthly: <b><?php echo number_format($monthly, 2) ?></b></small></p>
                                    <p><small>Penalty: <b><?php echo (date('Ymd', strtotime($next)) < date("Ymd")) ? number_format($penalty, 2) : '0.00' ?></b></small></p>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                switch ($row['status']) {
                                    case 0:
                                        echo '<span class="badge badge-warning">For Approval</span>';
                                        break;
                                    case 1:
                                        echo '<span class="badge badge-info">Approved</span>';
                                        break;
                                    case 2:
                                        echo '<span class="badge badge-primary">Released</span>';
                                        break;
                                    case 3:
                                        echo '<span class="badge badge-success">Completed</span>';
                                        break;
                                    case 4:
                                        echo '<span class="badge badge-danger">Denied</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-primary btn-sm edit_loan" type="button" data-id="<?php echo $row['id'] ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm delete_loan" type="button" data-id="<?php echo $row['id'] ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
   $(document).ready(function() {
       $('#loan-list').dataTable();
   });

   $('#new_application').click(function() {
       uni_modal("New Loan Application", "manage_loan.php", 'mid-large');
   });

   $('.edit_loan').click(function() {
       uni_modal("Edit Loan", "manage_loan.php?id=" + $(this).attr('data-id'), 'mid-large');
   });

   $('.delete_loan').click(function() {
       _conf("Are you sure to delete this loan?", "delete_loan", [$(this).attr('data-id')]);
   });

   function delete_loan($id) {
       start_load();
       $.ajax({
           url: 'ajax.php?action=delete_loan',
           method: 'POST',
           data: { id: $id },
           success: function(resp) {
               if (resp == 1) {
                   alert_toast("Loan successfully deleted", 'success');
                   setTimeout(function() {
                       location.reload();
                   }, 1500);
               }
           }
       });
   }
</script>

</body>
</html>
