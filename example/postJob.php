<?php
session_start();
require_once 'utils/db-conn.php';

$error = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobTitle = $_POST['jobTitle'];
    $jobCategory = $_POST['jobCategory'];
    $deadline = $_POST['deadline'];
    $salary = $_POST['salary'];
    $requirements = $_POST['requirements'];
    $employmentType = $_POST['employmentType'];
    $employerId = $_SESSION['user_id']; // Assuming the employer ID is stored in the session upon login

    // Insert the job details into postjob table
    $stmt = $conn->prepare("INSERT INTO postjob (jobTitle, jobCategory, deadline, salary, requirements, employmentType, employerId) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $jobTitle, $jobCategory, $deadline, $salary, $requirements, $employmentType, $employerId);

    if ($stmt->execute()) {
        // Job post successful
        $success_message = "Job posted successfully.";
        
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job</title>
    <link rel="stylesheet" href="src/css/postJob.css">
    <link rel="stylesheet" href="src/css/nav.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        
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
                <li><a href="EmployerHome.php">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <ul>
                <li class="profile"><a href="EmployerProfile.php"><i class="material-icons">person</i></a></li>
                <li class="logout"><a href="#"><i class="material-icons">exit_to_app</i></a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="banner">
            <h2>Post a Job</h2>
        </div>
        <div class="form-container">
            <form id="postJobForm" method="post" action="postJob.php" onsubmit="return validateForm()">
                <div class="input-container">
                    <label for="jobTitle">Job Title</label>
                    <input type="text" id="jobTitle" name="jobTitle" required>
                </div>
                <div class="input-container">
                    <label for="jobCategory">Job Category</label>
                    <select id="jobCategory" name="jobCategory" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="IT">IT</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Finance">Finance</option>
                    </select>
                </div>
                <div class="input-container">
                    <label for="deadline">Deadline</label>
                    <input type="date" id="deadline" name="deadline" required>
                </div>
                <div class="input-container">
                    <label for="salary">Salary</label>
                    <input type="text" id="salary" name="salary">
                </div>
                <div class="input-container">
                    <label for="requirements">Requirements</label>
                    <textarea id="requirements" name="requirements" rows="4"></textarea>
                </div>
                <div class="input-container">
                    <label>Employment Type</label><br>
                    <label><input type="radio" id="fulltime" name="employmentType" value="fulltime" required> Full Time</label>
                    <label><input type="radio" id="parttime" name="employmentType" value="parttime"> Part Time</label>
                </div>
                <div class="button-container">
                    <button type="submit" class="button">Post</button>
                    <button type="reset" class="button">Reset</button>
                </div>
            </form>
            <?php if ($error) : ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success_message) : ?>
                <div class="success"><?php echo $success_message; ?></div>
            <?php endif; ?>
        </div>
    </div>

    
    <script>
        function validateForm() {
            var jobTitle = document.getElementById('jobTitle').value.trim();
            var jobCategory = document.getElementById('jobCategory').value;
            var deadline = document.getElementById('deadline').value;
            var salary = document.getElementById('salary').value.trim();
            var requirements = document.getElementById('requirements').value.trim();
            var employmentTypeFulltime = document.getElementById('fulltime').checked;
            var employmentTypeParttime = document.getElementById('parttime').checked;

            if (!jobTitle || !jobCategory || !deadline || (!employmentTypeFulltime && !employmentTypeParttime)) {
                alert("Please fill in all required fields.");
                return false;
            }

            if (salary && isNaN(salary)) {
                alert("Salary must be a number.");
                return false;
            }

            return true;
        }
    </script>
</body>

</html>

