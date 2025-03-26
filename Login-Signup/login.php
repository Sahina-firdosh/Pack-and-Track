<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'Ecommerce');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Initialize variables
$uname = $pass = '';
$uname_err = $pass_err = $empty_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = trim($_POST['uname'] ?? '');
    $pass = trim($_POST['pass'] ?? '');

    if (empty($uname) || empty($pass)) {
        $empty_err = 'Both fields are required';
    } else {
        // Check if username exists
        $stmt = $conn->prepare('SELECT user_id, username, password FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $uname, $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                // Login successful
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                header('Location: ../Product-Category/all_product_category.php');
                exit();
            } else {
                $pass_err = 'Invalid password';
            }
        } else {
            $uname_err = 'No account found with that username or email';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pack & Trek - Login</title>
    <link rel="stylesheet" href="login.css" />
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="curved-bg"></div>
            <div class="logo">
                <span class="logo-icon">P&T</span>
            </div>
            <h2>Welcome Back!</h2>
            <p>To stay connected with us please login with your personal info</p>
            <a href="signup.php" class="sign-in-btn">SIGN UP</a>
        </div>
        <div class="right-panel">
            <h2>Welcome</h2>
            <p>Login in to your account to continue</p>
            <div class="signup_form">
                <form action="" method="post">
                    <div class="single_inp">
                        <input type="text" name="uname" placeholder="Username" value="<?php echo htmlspecialchars($uname ?? ''); ?>">
                    </div>
                    <div class="php_err"><?php echo isset($uname_err) ? $uname_err : ''; ?></div>
                    
                    <div class="single_inp">
                        <input type="password" name="pass" placeholder="Password">
                    </div>
                    <div class="php_err"><?php echo isset($pass_err) ? $pass_err : ''; ?></div>
                    
                    <div class="empty_err"><?php echo isset($empty_err) ? $empty_err : ''; ?></div>
                    
                    <div class="forgot-password">
                        <a href="#">Forgot your password?</a>
                    </div>
                    
                    <button type="submit" id="login">LOG IN</button>
                    <p id="new_acc">Don't have an account? <a href="signup.php">sign up</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>