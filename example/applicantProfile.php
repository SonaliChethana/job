<?php
session_start();
require_once 'utils/db-conn.php'; 

// Initialize variables
$loggedInEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$applicantName = '';
$username = '';
$address = '';
$phoneNumber = '';
$email = '';
$password = '';

// Handle profile form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $applicantName = $_POST['applicantName'];
    $username = $_POST['username'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Check if password is set
    $profileImage = '';

    // Handle profile image upload
    if (isset($_FILES['profileImageUpload']) && $_FILES['profileImageUpload']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profileImageUpload"]["name"]);
        
        // Ensure "uploads/" directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }

        // Move uploaded file to "uploads/" directory
        if (move_uploaded_file($_FILES["profileImageUpload"]["tmp_name"], $target_file)) {
            $profileImage = $target_file;
        } else {
            $_SESSION['error_message'] = "Failed to move uploaded file: " . $_FILES["profileImageUpload"]["error"];
            header("Location: ApplicantProfile.php");
            exit();
        }
    }

    // Update or insert into ApplicantProfile table
    $stmt = $conn->prepare("REPLACE INTO applicantprofile (Id, applicantName, username, address, phoneNumber, email, password, profileImage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Check if prepare() succeeded
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("isssssss", $userId, $applicantName, $username, $address, $phoneNumber, $email, $password, $profileImage);

    if ($stmt->execute()) {
        // Redirect to the profile page after successful update
        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: ApplicantProfile.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating profile: " . $stmt->error;
        header("Location: ApplicantProfile.php");
        exit();
    }

    $stmt->close();
}

// Fetch jobs to which the applicant has applied
$jobs = [];
$stmt = $conn->prepare("SELECT p.jobTitle, p.jobCategory, p.deadline, p.salary, p.requirements, p.employmentType, p.upload_CV 
                        FROM postjob p
                        JOIN applications a ON p.jobId = a.jobId
                        WHERE a.applicantId = ?");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("i", $userId); // Assuming `applicantId` is an integer
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="src/css/Employer.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .profile-content {
            display: none;
        }
        .profile-content.active {
            display: block;
        }
        .job-box {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #f9f9f9;
            position: relative;
        }
        .download-cv-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .download-cv-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<nav>
    <div class="logo">
    </div>
    <div class="nav-left">
        <ul>
            <li><a href="ApplicantHome.php">Home</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="viewJobs.php">View Job</a></li>
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
    <div class="profile-container">
        <div class="sidebar">
            <ul>
                <li><a href="javascript:void(0);" class="profile-settings" data-target="profile-settings">Profile Settings</a></li>
                <li><a href="javascript:void(0);" class="my-jobs" data-target="my-jobs">Applied Jobs</a></li>
            </ul>
        </div>
        <div class="profile-content profile-settings active">
            <h2>You are logged in as: <?php echo htmlspecialchars($loggedInEmail); ?> (Applicant)</h2> 
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>
            <form id="profileForm" method="post" action="ApplicantProfile.php" enctype="multipart/form-data">
                <div class="profile-picture">
                    <img src="default-profile.png" alt="Profile Picture" id="profileImage">
                    <input type="file" id="profileImageUpload" name="profileImageUpload" accept="image/*">
                </div>
                <div class="input-container">
                    <label for="applicantName">Applicant Name</label>
                    <input type="text" id="applicantName" name="applicantName" value="<?php echo htmlspecialchars($applicantName); ?>" required disabled>
                    <button type="button" class="edit-button" onclick="enableEdit('applicantName')">Edit</button>
                </div>
                <div class="input-container">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required disabled>
                    <button type="button" class="edit-button" onclick="enableEdit('username')">Edit</button>
                </div>
                <div class="input-container">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required disabled>
                    <button type="button" class="edit-button" onclick="enableEdit('address')">Edit</button>
                </div>
                <div class="input-container">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($phoneNumber); ?>" required disabled>
                    <button type="button" class="edit-button" onclick="enableEdit('phoneNumber')">Edit</button>
                </div>
                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required disabled>
                    <button type="button" class="edit-button" onclick="enableEdit('email')">Edit</button>
                </div>
                <div class="input-container">
                    <label for="password">Password</label>
                    <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required disabled>
                    <button type="button" class="edit-button" onclick="enableEdit('password')">Edit</button>
                </div>
                <div class="button-container">
                    <button type="submit" class="button">Save</button>
                </div>
            </form>
        </div>
        <div class="profile-content my-jobs">
            <h2>Applied Jobs</h2>
            <ul id="jobsList">
                <?php foreach ($jobs as $job): ?>
                    <li class="job-box">
                        <h3><?php echo htmlspecialchars($job['jobTitle']); ?></h3>
                        <p><?php echo htmlspecialchars($job['jobCategory']); ?></p>
                        <p>Deadline: <?php echo htmlspecialchars($job['deadline']); ?></p>
                        <p>Salary: <?php echo htmlspecialchars($job['salary']); ?></p>
                        <p><?php echo htmlspecialchars($job['requirements']); ?></p>
                        <p>Type: <?php echo htmlspecialchars($job['employmentType']); ?></p>
                        <?php if (!empty($job['upload_CV'])): ?>
                            <a href="<?php echo htmlspecialchars($job['upload_CV']); ?>" class="download-cv-button" download>Download CV</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
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

<script src="src/js/profile.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.sidebar a');

        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.dataset.target;
                const contents = document.querySelectorAll('.profile-content');

                contents.forEach(content => {
                    if (content.classList.contains(target)) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });
            });
        });
    });

    function enableEdit(fieldId) {
        document.getElementById(fieldId).disabled = false;
        document.getElementById(fieldId).focus();
    }
</script>

</body>
</html>
