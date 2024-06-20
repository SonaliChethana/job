<?php
session_start();
require_once 'utils/db-conn.php';

// Check if user is logged in
if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
    $userLoggedIn = true;
} else {
    $userLoggedIn = false;
}

// Fetch jobs posted by the logged-in employer
if ($userLoggedIn) {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

    // Fetch jobs from postjob table
    $jobs = [];
    $stmt = $conn->prepare("SELECT jobTitle, jobCategory, deadline, salary, requirements, employmentType FROM postjob WHERE employerId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Connect</title>
    <link rel="stylesheet" href="src/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <nav>
        <div class="logo">
        </div>
        <div class="nav-left">
            <ul>
                <li><a href="EmployerHome.php">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="postJob.php">Post Job</a></li>
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
        </div>
    </div>

    <div class="container">
        <div class="introduction">
            <h2>About Career Connect</h2>
            <p>Career Connect is your gateway to finding the perfect job or employee. Whether you're looking for your dream job or trying to fill a position, we provide the tools and resources you need to succeed.</p>
        </div>

        <div class="register-buttons">
            <a href="signUp.php" class="button green">Register as Employe</a>
            <a href="signUp.php" class="button blue">Register as Applicant</a>
        </div>

        <!-- My Jobs Section -->
        <div class="profile-container">
            <div class="profile-content" id="myJobs">
                <h2>My Jobs</h2>
                <ul id="jobsList">
                    <?php if ($userLoggedIn && !empty($jobs)) : ?>
                        <?php foreach ($jobs as $job) : ?>
                            <li>
                                <h3><?php echo htmlspecialchars($job['jobTitle']); ?></h3>
                                <p><?php echo htmlspecialchars($job['jobCategory']); ?></p>
                                <p>Deadline: <?php echo htmlspecialchars($job['deadline']); ?></p>
                                <p>Salary: <?php echo htmlspecialchars($job['salary']); ?></p>
                                <p><?php echo htmlspecialchars($job['requirements']); ?></p>
                                <p>Type: <?php echo htmlspecialchars($job['employmentType']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li>No jobs found.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
            <footer>
              <h2>CAREER CONNECT</h2>
              <nav>|
                  <a href="index.php">Home</a>|
                  <a href="anoutus.php">About Us</a>|
                  <a href="login.php">Login</a>|
                  <a href="signUp.php">Registration</a>|
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
