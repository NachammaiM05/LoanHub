<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login | LoanHub</title>
    <?php include('./header.php'); ?>
    <?php
    session_start();
    include 'db_connect.php';

    // Handle the login form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $login_type = $_POST['login_type']; // Capture the selected role

        // Check the credentials
        $qry = $conn->query("SELECT * FROM users WHERE username = '$username' AND password = '" . md5($password) . "' AND role = '$login_type'");

        if ($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            $_SESSION['login_name'] = $user['username'];
            $_SESSION['login_type'] = $login_type;

            // Redirect based on the user's role
            if ($_SESSION['login_type'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($_SESSION['login_type'] == 'lender') {
                header("Location: lenders.php");
            } elseif ($_SESSION['login_type'] == 'borrower') {
                header("Location: borrowers.php");
            }
            exit;
        } else {
            // Invalid credentials message
            $error_message = "Invalid credentials or role.";
        }
    }
    // Capture role from landing page if provided
    $selected_role = isset($_GET['role']) ? $_GET['role'] : '';
    ?>
</head>
<style>
    body {
        width: 100%;
        height: calc(100%);
    }

    main#main {
        width: 100%;
        height: calc(100%);
        background: white;
    }

    #login-right {
        position: absolute;
        right: 0;
        width: 40%;
        height: calc(100%);
        background: white;
        display: flex;
        align-items: center;
    }

   #login-left {
        position: absolute;
        left: 0;
        width: 60%;
        height: calc(100%);
        background: #59b6ec61;
        display: flex;
        align-items: center;
        background: url(assets/img/loan-cover.jpg);
        background-repeat: no-repeat;
        background-size: cover;
    }


    .logo {
        margin: auto;
        font-size: 8rem;
        background: white;
        border-radius: 50%;
        height: 29vh;
        width: 13vw;
        display: flex;
        align-items: center;
    }
</style>

<body>
    <main id="main" class="bg-dark">
        <div id="login-left"></div>
        <div id="login-right">
            <div class="w-100">
                <h4 class="text-center">Login as <?php echo ucfirst($selected_role); ?></h4>
                <div class="card col-md-8">
                    <div class="card-body">
                        <form id="login-form" method="POST">
                            <input type="hidden" name="login_type" value="<?php echo $selected_role; ?>">
                            <div class="form-group">
                                <label for="username" class="control-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="control-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary btn-sm">Login</button>
                        </form>
                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
<script>
    // Optional: Add any login-specific JS here
</script>
</html>
