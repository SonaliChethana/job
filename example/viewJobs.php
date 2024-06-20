<?php
session_start();
require_once 'utils/db-conn.php';

$searchQuery = '';
$jobs = [];
$cvMessage = '';

// Check if user is logged in
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Handle job search
  if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    
    // Fetch jobs matching the search query
    $stmt = $conn->prepare("SELECT jobId, jobTitle, jobCategory, deadline, salary, requirements, employmentType FROM postjob WHERE jobCategory LIKE ?");
    
    // Check if the statement was prepared correctly
    if (!$stmt) {
      die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    
    $likeQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
      $jobs[] = $row;
    }
    
    $stmt->close();
  }

  // Handle CV upload for each job
  if (isset($_FILES['cvUpload']) && isset($_POST['jobId']) && $userId) {
    $jobId = $_POST['jobId'];
    $cvDir = "uploads/cv/";
    $cvFile = $cvDir . basename($_FILES["cvUpload"]["name"]);
    
    // Ensure "uploads/cv/" directory exists
    if (!file_exists($cvDir)) {
      mkdir($cvDir, 0777, true); // Create directory if it doesn't exist
    }

    // Move uploaded file to "uploads/cv/" directory
    if (move_uploaded_file($_FILES["cvUpload"]["tmp_name"], $cvFile)) {
      // Update the upload_CV column in the postjob table
      $stmt = $conn->prepare("UPDATE postjob SET upload_CV = ? WHERE jobId = ?");
      
      // Check if the statement was prepared correctly
      if (!$stmt) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
      }

      $stmt->bind_param("si", $cvFile, $jobId);
      
      if ($stmt->execute()) {
        $cvMessage = "CV uploaded successfully!";
      } else {
        $cvMessage = "Failed to update CV in the database.";
      }
      
      $stmt->close();
    } else {
      $cvMessage = "Failed to upload CV.";
    }
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Job Recruitment</title>
  <link rel="stylesheet" href="src/css/viewJobs.css">
  <link rel="stylesheet" href="src/css/nav.css">
  <link rel="stylesheet" href="src/css/footer.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Additional CSS for job boxes and CV upload */
    .job-box {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid #ccc;
      padding: 20px;
      margin: 10px 0;
      border-radius: 5px;
      background-color: #f9f9f9;
    }

    .job-box-details {
      flex: 1;
    }

    .job-box h3 {
      margin-top: 0;
    }

    .job-box p {
      margin-bottom: 5px;
    }

    .cv-upload-container {
      display: flex;
      align-items: center;
    }

    .cv-upload-container input[type="file"] {
      margin-right: 10px;
    }

    .cv-upload-container button {
      padding: 10px 20px;
      border: none;
      background-color: var(--dark-blue);
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }

    .cv-upload-container button:hover {
      background-color: var(--light-blue);
    }

    .cv-message {
      color: green;
      margin-top: 10px;
    }

    .cv-error {
      color: red;
      margin-top: 10px;
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
        <li><a href="#">Contact Us</a></li>
      </ul>
    </div>
    <div class="nav-right">
      <ul>
        <li class="profile"><a href="ApplicantProfile.php"><i class="material-icons">person</i></a></li>
        <li class="logout"><a href="#"><i class="material-icons">exit_to_app</i></a></li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <div class="banner">
      <h2>Job Recruitment</h2>
    </div>
    <div class="search-container">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="text" name="search" placeholder="Search jobs by category..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="button">Search</button>
      </form>
    </div>
    <div class="results-container">
      <?php if (empty($jobs)): ?>
        <p>No jobs found for category '<?php echo htmlspecialchars($searchQuery); ?>'.</p>
      <?php else: ?>
        <?php foreach ($jobs as $job): ?>
          <div class="job-box">
            <div class="job-box-details">
              <h3><?php echo htmlspecialchars($job['jobTitle']); ?></h3>
              <p><strong>Category:</strong> <?php echo htmlspecialchars($job['jobCategory']); ?></p>
              <p><strong>Deadline:</strong> <?php echo htmlspecialchars($job['deadline']); ?></p>
              <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
              <p><strong>Requirements:</strong> <?php echo htmlspecialchars($job['requirements']); ?></p>
              <p><strong>Employment Type:</strong> <?php echo htmlspecialchars($job['employmentType']); ?></p>
            </div>
            <div class="cv-upload-container">
              <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="jobId" value="<?php echo htmlspecialchars($job['jobId']); ?>">
                <input type="file" name="cvUpload" required>
                <input type="submit" value="Submit CV">
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php if ($cvMessage): ?>
        <p class="cv-message"><?php echo htmlspecialchars($cvMessage); ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="footer">
    <footer>
      <h2>CAREER CONNECT</h2>
      <nav>|
        <a href="index.php">Home</a>|
        <a href="aboutus.php">About Us</a>|
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
</body>

</html>
