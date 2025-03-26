<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'Ecommerce');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Initialize variables
$username = $email = $first_name = $last_name = $password = $confirm_password = $phone_number = $address = '';
$username_err = $email_err = $password_err = $confirm_password_err = $empty_err = '';

$fields = [
    ['type' => 'text', 'name' => 'username', 'placeholder' => 'Username'],
    ['type' => 'email', 'name' => 'email', 'placeholder' => 'Email'],
    ['type' => 'text', 'name' => 'first_name', 'placeholder' => 'First Name'],
    ['type' => 'text', 'name' => 'last_name', 'placeholder' => 'Last Name'],
    ['type' => 'password', 'name' => 'password', 'placeholder' => 'Password'],
    ['type' => 'password', 'name' => 'confirm_password', 'placeholder' => 'Confirm Password'],
    ['type' => 'tel', 'name' => 'phone_number', 'placeholder' => 'Phone Number'],
    ['type' => 'text', 'name' => 'address', 'placeholder' => 'Address']
];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        $$key = trim($value);
    }

    if (empty($username) || empty($email) || empty($first_name) || empty($last_name) || empty($password) || empty($confirm_password) || empty($phone_number) || empty($address)) {
        $empty_err = 'All fields are required';
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = 'Invalid email format';
        }

        if ($password !== $confirm_password) {
            $confirm_password_err = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $password_err = 'Password must be at least 6 characters';
        }

        $stmt = $conn->prepare('SELECT user_id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $username_err = 'Username already taken';
        }
        $stmt->close();
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($empty_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, password, email, first_name, last_name, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssss', $username, $hashed_password, $email, $first_name, $last_name, $phone_number, $address);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Account created successfully. Please sign in.';
            header('Location: /login.php');
            exit();
        } else {
            $empty_err = 'Something went wrong. Please try again later.';
        }
        $stmt->close();
    }
}

function renderInputField($field) {
    $name = $field['name'];
    $value = htmlspecialchars($GLOBALS[$name] ?? '');
    $error = $GLOBALS[$name . '_err'] ?? '';

    echo "<div class='form-col'>
            <div class='single_inp'>
                <input type='{$field['type']}' name='$name' placeholder='{$field['placeholder']}' value='$value'>
            </div>
            <div class='php_err'>$error</div>
        </div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pack & Trek - Sign Up</title>
    <link rel="stylesheet" href="signup.css" />
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <span class="logo-icon">P&T</span>
            </div>
            <h2>Welcome!</h2>
            <p>Join our community and discover amazing adventures around the world</p>
            <a href="login.php" class="sign-in-btn">SIGN IN</a>
        </div>
        <div class="right-panel">
            <h2>Create Account</h2>
            <p>Please fill in your details to get started</p>
            <div class="signup_form">
                <form action="" method="post">
                    <?php for ($i = 0; $i < count($fields); $i += 2): ?>
                        <div class="form-row">
                            <?php renderInputField($fields[$i]); ?>
                            <?php if (isset($fields[$i + 1])) renderInputField($fields[$i + 1]); ?>
                        </div>
                    <?php endfor; ?>

                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id ?? ''); ?>">
                    <div class="empty_err"><?php echo isset($empty_err) ? $empty_err : ''; ?></div>
                    <button type="submit" id="signup">CREATE ACCOUNT</button>
                    <p id="existing_acc">Already have an account? <a href="login.php">Sign in</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
