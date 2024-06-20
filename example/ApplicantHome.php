<?php
session_start();
require_once 'utils/db-conn.php';

if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
  $userLoggedIn = true;
} else {
  $userLoggedIn = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Career Connect</title>
  <link rel="stylesheet" href="src/css/ApplicantHome.css">
  <link rel="stylesheet" href="src/css/nav.css">
  <link rel="stylesheet" href="src/css/footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
.banner {
      background-image: url('src/images/job.jpg');
      background-size: cover;
      background-position: center;
      height: 400px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: #fff;
    }

  </style>
</head>

<body>
  <nav>
    <div class="logo">
      <a href="index.php">
        <img src="src/images/logo.jpg" alt="Career Connect Logo">
      </a>
    </div>
    <div class="nav-left">
      <ul>
        <li><a href="ApplicantHome.php">Home</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="viewJobs.php">View Jobs</a></li>
      </ul>
    </div>
    <div class="nav-right">
      <ul>
        <li class="profile"><a href="ApplicantProfile.php"><i class="material-icons">person</i></a></li>
        <li class="logout"><a href="#"><i class="material-icons">exit_to_app</i></a></li>
      </ul>
    </div>
  </nav>

  <div class="banner">
    <div class="hero-text">
      <h1><mark>Welcome to Career Connect</mark></h1>
    </div>
  </div>

  <div class="container">
    <div class="introduction">
      <h2>About Career Connect</h2>
      <p>Career Connect is your gateway to finding the perfect job or employee. Whether you're looking for your dream job or trying to fill a position, we provide the tools and resources you need to succeed.</p>
    </div>

    <div class="register-buttons">
      <a href="signUp.php" class="button green">Register as Employer</a>
      <a href="signUp.php" class="button blue">Register as Applicant</a>
    </div>

    <div class="features">
      <div class="feature">
        <img src="src/images/Apply.jpg" alt="Feature 1">
        <p>Discover job opportunities tailored to your skills and interests.</p>
      </div>
      <div class="feature">
        <img src="src/images/interview.jpeg" alt="Feature 2">
        <p>Connect with top employers and industry leaders.</p>
      </div>
      <div class="feature">
        <img src="src/images/Get.jpg" alt="Feature 3">
        <p>Access resources and tools to boost your career growth.</p>
      </div>
    </div>
  </div>

  <div class="footer">
    <footer>
      <h2>CAREER CONNECT</h2>
      <nav>
        <a href="index.php">Home</a> |
        <a href="aboutus.php">About Us</a> |
        <a href="login.php">Login</a> |
        <a href="signUp.php">Registration</a>
      </nav>

      <div class="footer-bottom">
        <h3>Follow Us</h3>
        <ul class="social">
          <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
          <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
          <li><a href="#"><i class="fab fa-instagram"></i></a></li>
          <li><a href="#"><i class="fab fa-twitter"></i></a></li>
          <li><a href="mailto:info@company.com"><i class="fas fa-envelope"></i></a></li>
        </ul>
      </div>
      <p>&copy; 2024 JOB SEARCH. All rights reserved.</p>
      <p>Designed by : SONALI CHETHANA</p>
    </footer>
  </div>

  <div class="error-container" id="error-container">
    <?php
    if (isset($_GET['error'])) {
      $errors = $_GET['error'];

      foreach ($errors as $error) {
        echo "<p class='error'>$error</p>";
      }
    }
    if (isset($_GET['success'])) {
      $successes = $_GET['success'];

      foreach ($successes as $success) {
        echo "<p class='success'>$success</p>";
      }
    }
    ?>
  </div>

  <script src="src/js/error.js"></script>
</body>

</html>
