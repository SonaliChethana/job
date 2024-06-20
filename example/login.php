<?php
session_start();
require_once 'utils/db-conn.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Ensure $mode is set and valid
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';
    
    // Validate $mode to prevent unexpected values
    if ($mode !== 'applicant' && $mode !== 'employer') {
        $error = "Invalid mode selected.";
    } else {
        // Query to select user based on mode
        if ($mode === 'applicant') {
            $stmt = $conn->prepare("SELECT id, username, password FROM applicantprofile WHERE email = ?");
        } elseif ($mode === 'employer') {
            $stmt = $conn->prepare("SELECT id, username, password FROM employerprofile WHERE email = ?");
        }

        if (isset($stmt)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $username, $hashedPassword);
                $stmt->fetch();

                // Verify hashed password
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['login'] = true;
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;

                    // Redirect to respective profile page
                    if ($mode === 'employer') {
                        header("Location: EmployerHome.php");
                        exit();
                    } else {
                        header("Location: ApplicantHome.php");
                        exit();
                    }
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "Email not found. Please create an account.";
            }

            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="src/css/login.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-image: url('src/images/career.jpg'); /* Replace with your background image path */
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif; /* Default font */
        }

        /* Additional styling for the login form */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
            width: 350px;
            max-width: 80%;
            text-align: center;
        }

        /* Rest of your existing CSS can remain unchanged */
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="index1.php">Home</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="signup.php">Sign Up</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="mode-switcher">
                <button id="applicantMode" class="mode active" onclick="switchMode('applicant')">Applicant</button>
                <button id="employerMode" class="mode" onclick="switchMode('employer')">Employer</button>
            </div>

            <h2>Login</h2><br>
            <form id="loginForm" method="post" action="login.php" onsubmit="return validateForm()">
                <input type="hidden" id="mode" name="mode" value="applicant"> <!-- Default mode is applicant -->

                <div class="input-container">
                    <label for="email"><i class="material-icons">email</i></label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <label for="password"><i class="material-icons">lock</i></label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="button-container">
                    <button type="submit" class="button">Login</button>
                </div>
                <p id="error-message" class="error-message"><?php echo $error; ?></p>
            </form>
            <div class="create-account">
                <p>Don't have an account? <a href="signup.php">Create account</a></p>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value.trim();

            // Validate email format (optional, can be done server-side as well)
            var emailPattern = "";
            if (!emailPattern.test(email)) {
                displayError("Please enter a valid email address.");
                return false;
            }

            // Form is valid
            return true;
        }

        function displayError(message) {
            var errorMessage = document.getElementById('error-message');
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }

        /* Switch mode */
        function switchMode(mode) {
            var applicantMode = document.getElementById('applicantMode');
            var employerMode = document.getElementById('employerMode');
            var modeInput = document.getElementById('mode');

            if (mode === 'applicant') {
                applicantMode.classList.add('active');
                employerMode.classList.remove('active');
            } else if (mode === 'employer') {
                employerMode.classList.add('active');
                applicantMode.classList.remove('active');
            }

            modeInput.value = mode;
        }
    </script>
</body>
</html>
