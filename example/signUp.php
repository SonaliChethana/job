<?php
session_start();
require_once 'utils/db-conn.php';

$error = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mode = $_POST['mode']; // Ensure this captures the correct mode value

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email is already registered
    $stmt_check_email = $conn->prepare("SELECT id FROM UserRegister WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        $error = "Error: Email already exists.";
    } else {
        // Insert into respective table based on mode
        if ($mode === 'applicant') {
            $stmt_insert = $conn->prepare("INSERT INTO applicantprofile (username, email, password) VALUES (?, ?, ?)");
        } elseif ($mode === 'employer') {
            $stmt_insert = $conn->prepare("INSERT INTO employerprofile (username, email, password) VALUES (?, ?, ?)");
        }

        if ($stmt_insert) {
            $stmt_insert->bind_param("sss", $username, $email, $hashedPassword);
            if ($stmt_insert->execute()) {
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $stmt_insert->insert_id;
                $success_message = "Account created successfully.";
                // Clear form data to prevent resubmission on refresh
                $_POST = array();
            } else {
                $error = "Error: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
            $error = "Error: " . $conn->error;
        }
    }

    $stmt_check_email->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="src/css/signup.css">
    <link rel="stylesheet" href="src/css/nav.css">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-image: url('src/images/signin.jpg'); /* Replace with your background image path */
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
            max-width: 90%;
            text-align: center;
        }
        </style>

</head>
    <body>
         <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="mode-switcher">
                <button id="applicantMode" class="mode active" onclick="switchMode('applicant')">Applicant</button>
                <button id="employerMode" class="mode" onclick="switchMode('employer')">Employer</button>
            </div>

            <h2>Create Account</h2>
            <form id="signupForm" method="post" action="signup.php" onsubmit="return validateForm()">
                <input type="hidden" id="mode" name="mode" value="applicant"> <!-- Default mode is applicant -->

                <div class="input-container">
                    <label for="username"><i class="material-icons">person</i></label>
                    <input type="text" id="username" name="username" placeholder="Username" required
                        value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
                </div>
                <div class="input-container">
                    <label for="email"><i class="material-icons">email</i></label>
                    <input type="email" id="email" name="email" placeholder="Email" required
                        value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                </div>
                <div class="input-container">
                    <label for="password"><i class="material-icons">lock</i></label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-container">
                    <label for="confirmPassword"><i class="material-icons">lock</i></label>
                    <input type="password" id="confirmPassword" name="confirmPassword"
                        placeholder="Confirm Password" required>
                </div>

                <div class="button-container">
                    <button type="button" onclick="goBack()" class="button">Back</button>
                    <button type="submit" class="button">Create Account</button>
                </div>
                <p id="error-message" class="error-message"><?php echo $error; ?></p>
                <?php if (!empty($success_message)): ?>
                    <p id="success-message" class="success-message"><?php echo $success_message; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            var username = document.getElementById('username').value.trim();
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value.trim();
            var confirmPassword = document.getElementById('confirmPassword').value.trim();

            // Validate email format
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                displayError("Please enter a valid email address.");
                return false;
            }

            // Validate password complexity
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordPattern.test(password)) {
                displayError("Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character.");
                return false;
            }

            // Validate password and confirm password match
            if (password !== confirmPassword) {
                displayError("Passwords do not match.");
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

        function goBack() {
            window.history.back();
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
