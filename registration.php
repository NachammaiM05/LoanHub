<?php 
session_start();
include('./db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input fields (e.g., username, email, password)
    // Add validation logic here

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        // Registration successful
        // Send notification email
        $to = $email;
        $subject = 'Registration Successful';
        $message = 'Dear ' . $username . ',\n\nThank you for registering with us. Your account has been created successfully.';
        $headers = 'From: noreply@loanhub.com'; // Update with your email
        mail($to, $subject, $message, $headers);

        echo json_encode(array('status' => 'success'));
        // Redirect to login page after successful registration
        header('Location: login.php');
        exit;
    } else {
        // Registration failed
        echo json_encode(array('status' => 'error'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>LoanHub - Registration</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f2f2f2;
    }
    .card {
      width: 50%;
      max-width: 500px;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-control {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      box-sizing: border-box;
    }
    .btn-success {
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 3px;
      cursor: pointer;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="registration-form card">
    <div class="card-body">
      <h2>Registration</h2>
      <form method="post" action="registration.php">
        <div class="form-group">
          <label for="username" class="control-label">Username</label>
          <input type="text" id="username" name="username" class="form-control">
        </div>
        <div class="form-group">
          <label for="email" class="control-label">Email</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="password" class="control-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
          <span class="toggle-password" onclick="togglePassword()">
            <i class="fa fa-eye"></i>
          </span>
        </div>
        <div class="form-group">
          <label for="confirm_password" class="control-label">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="role">Role:</label>
          <select id="role" name="role" class="form-control">
              <option value="admin">Admin</option>
              <option value="lender">Lender</option>
              <option value="borrower">Borrower</option>
              <option value="user">User</option>
          </select>
        </div>
        <button type="submit" name="submit" class="btn-success">Register</button>
      </form>
      <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
  </div>
  
  <img src="https://www.tatacapital.com/blog/wp-content/uploads/2020/10/5-financial-scenarios-1.jpeg" alt="Registration Image" class="registration-image">

  <script>
    function togglePassword() {
      var passwordInput = document.getElementById("password");
      var icon = document.querySelector(".toggle-password i");
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.toggle("fa-eye-slash");
      } else {
        passwordInput.type = "password";
        icon.classList.toggle("fa-eye");
      }
    }
  </script>
</body>
</html>
