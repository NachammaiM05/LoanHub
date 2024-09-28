
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoanHub</title>
    <!-- Icons (FontAwesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- CSS for Animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
        }

        /* Header */
        header {
            background: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
        }

        nav ul {
            list-style: none;
            display: flex;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        /* Hero Section */
        .hero {
           background: url('images/photobacksitepgcab-1507209696998-3c532be9b2b5.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            text-align: center;
        }

        .hero h2 {
            font-size: 48px;
            animation: fadeInDown 2s ease-in-out;
        }

        .hero h4 {
            font-size: 48px;
            animation: fadeInDown 2s ease-in-out;
        }
        /* Role Selection Form */
        .role-form {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
            text-align: left;
        }

        .role-form h3 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .role-form ul {
            list-style: none;
            padding: 0;
        }

        .role-form ul li {
            margin: 15px 0;
        }

        .role-form ul li a {
            display: block;
            padding: 15px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .role-form ul li a i {
            margin-right: 10px;
        }

        /* Footer */
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .footer .social-icons {
            margin: 20px 0;
        }

        .footer .social-icons a {
            color: white;
            margin: 0 10px;
            font-size: 20px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <h1>LoanHub</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li> 
                <li><a href="loans.php">Loans</a></li> 
                <li><a href="contact_us.php">Contact Us</a></li> 
            </ul>
         </nav> 
        </header>
                <!-- Hero Section -->
<section class="hero">
    <div class="content">
        <h2 class="animate__animated animate__fadeInDown">Welcome to LoanHub</h2>
        <p>Find the best loan plans for your needs</p>
    </div>
</section>

<!-- Role Selection Form -->
<section class="role-form">
    <h3>Select Your Role</h3>
    <ul>
        <li><a href="register.php"><i class="fas fa-user"></i> Register</a></li>
        <li><a href="login.php?role=admin"><i class="fas fa-fw fa-users"></i> Admin Login</a></li>
            <li><a href="login.php?role=lender"><i class="fas fa-fw fa-building"></i> Lender Login</a></li>
            <li><a href="login.php?role=borrower"><i class="fas fa-fw fa-user-friends"></i> Borrower Login</a></li>
        </ul>
    </section>
    <!-- Information Section for Non-Logged-In Users -->
    <blockquote style="font-style: italic; font-size: 20px; margin-bottom: 5px;">
              "Empowering your financial journey, one loan at a time."
    </blockquote>
    <section class="info-section">
    <h3>Why Choose LoanHub?</h3>
  
    <!-- Descriptive Content -->
    <p>
        LoanHub offers a streamlined process for managing loans, whether you are a borrower, lender, or administrator.
        As a borrower, easily apply for loans and track your payments. Lenders can manage their loan portfolios and communicate 
        with borrowers efficiently. Administrators have access to powerful tools for managing the entire loan system.
    </p>
</section>

<!-- Footer -->
<footer class="footer">
    <p>© 2024 LoanHub. All rights reserved.</p>
    <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
    </div>
</footer>
</body>
</html>
