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
  <link rel="stylesheet" href="src/css/style.css">
  <link rel="stylesheet" href="src/css/nav.css">
  <link rel="stylesheet" href="src/css/footer.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    /* Additional CSS for enhanced design */
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0; /* Light gray background */
    }

    .banner {
      background-image: url('src/images/banner.jpg');
      background-size: cover;
      background-position: center;
      height: 400px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: #fff;
    }

    .banner h1 {
      font-size: 3em;
      margin-bottom: 10px;
    }

    .banner p {
      font-size: 1.2em;
      margin-bottom: 20px;
    }

    .about-section {
      background-color: #fff;
      padding: 50px 20px;
      text-align: center;
    }

    .about-section h2 {
      font-size: 2.5em;
      margin-bottom: 20px;
    }

    .about-section p {
      font-size: 1.1em;
      line-height: 1.6;
    }

    .contact-section {
      background-color: #333;
      color: #fff; /* Text color */
      padding: 50px 20px;
      text-align: center;
    }

    .contact-section h2 {
      font-size: 2.5em;
      margin-bottom: 20px;
    }

    .contact-section p {
      font-size: 1.1em;
      line-height: 1.6;
    }

    .contact-info {
      margin-top: 30px;
      display: flex;
      justify-content: center;
    }

    .contact-info div {
      margin: 0 20px;
      text-align: left;
    }

    .contact-info div i {
      font-size: 1.5em;
      margin-right: 10px;
      color: #ffd700; /* Icon color - gold */
    }

    .contact-info div p {
      color: #fff; /* Text color */
    }

    .contact-info div p:nth-child(2) {
      color: #ffd700; /* Second line color - gold */
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
        <li><a href="index.php">Home</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#contact">Contact Us</a></li>
      </ul>
    </div>
    <div class="nav-right">
      <ul>
        <li class="profile"><a href="EmployerProfile.php"><i class="material-icons">person</i></a></li>
        <li class="logout"><a href="#"><i class="material-icons">exit_to_app</i></a></li>
      </ul>
    </div>
  </nav>

  <div class="banner">
    <div class="hero-text">
      <h1>Welcome to <mark>Career Connect</mark></h1>
      <p>Your gateway to finding the perfect job or employee.</p>
    </div>
  </div>

  <div id="about" class="about-section">
    <div class="container">
      <h2>About Career Connect</h2>
      <p>Career Connect is your trusted platform for connecting employers with top talent and helping job seekers find their ideal career opportunities. We offer comprehensive tools and resources to streamline the hiring process and enhance job search efficiency.</p>
    </div>
  </div>

  <div class="container">
    <div class="register-buttons">
      <a href="signUp.php" class="button green">Register as Employer</a>
      <a href="signUp.php" class="button blue">Register as Applicant</a>
    </div>
  </div>

  <div id="contact" class="contact-section">
    <div class="container">
      <h2>Contact Us</h2>
      <p>Have questions or feedback? Reach out to us through the following channels:</p>
      <div class="contact-info">
        <div>
          <i class="fas fa-map-marker-alt"></i>
          <p>123 Job Seeker Street<br>City, Country</p>
        </div>
        <div>
          <i class="fas fa-phone-alt"></i>
          <p>(123) 456-7890</p>
        </div>
        <div>
          <i class="fas fa-envelope"></i>
          <p>info@careerconnect.com</p>
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    <footer>
      <h2>CAREER CONNECT</h2>
      <nav>
        <a href="index.php">Home</a> |
        <a href="#about">About Us</a> |
        <a href="login.php">Login</a> |
        <a href="signUp.php">Registration</a> |
      </nav>

      <div class="footer-bottom">
        <h3>Follow Us</h3>
        <ul class="social">
          <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
          <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
          <li><a href="#"><i class="fab fa-instagram"></i></a></li>
          <li><a href="#"><i class="fab fa-twitter"></i></a></li>
          <li><a href="mailto:info@careerconnect.com"><i class="fas fa-envelope"></i></a></li>
        </ul>
      </div>
      <p>&copy; 2024 JOB SEARCH. All rights reserved.</p>
      <p>Designed by: SONALI CHETHANA</p>
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
