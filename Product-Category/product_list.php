<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Ecommerce";
$php_error = $php_msg = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $php_error = "Connection failed: " . $conn->connect_error;
}

// Validate GET product ID
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $sql = "SELECT * FROM Products WHERE product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $php_error = "Product not found.";
        exit;
    }
} else {
    $php_error = "Invalid product ID.";
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $order_type = $_POST['order_type'];
    $rent_days = isset($_POST['rent_days']) ? intval($_POST['rent_days']) : NULL;
    $quantity = 1;

    $hasError = false;

    if ($order_type === 'rent' && (!$rent_days || $rent_days <= 0)) {
        $php_error = "Please enter valid rent days.";
        $hasError = true;
    }

    // Proceed only if there's no error
    if (!$hasError) {
        if ($order_type === 'rent') {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, order_type, rent_days, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisii", $user_id, $product_id, $order_type, $rent_days, $quantity);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, order_type, quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisi", $user_id, $product_id, $order_type, $quantity);
        }

        if ($stmt->execute()) {
            header('Location: ..\Cart&Order\cart.php');
            exit();
        } else {
            $php_error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($row['product_name']); ?> - Pack & Trek</title>
    <link rel="stylesheet" href="../basic/basic.css">
    <link rel="stylesheet" href="product_list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="home">
        <header>
            <div class="logo"><a href="..\Home\home.html">Pack & Trek</a></div>
            <div class="search-cart">
                <form class="search-bar">
                    <input type="text" placeholder="Search products...">
                    <i class="fas fa-search search-icon"></i>
                </form>
            </div>
            <nav>
                <ul>
                    <a href="..\Home\home.html">
                        <li>Home</li>
                    </a>
                    <a href="../Product-Category/all_product_category.php">
                        <li>Categories</li>
                    </a>
                    <a href="../Cart&Order\cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-text">Cart</span>
                    </a>
                    <a href="../Login-Signup\login.php">
                        <li>Login</li>
                    </a>
                    <a href="../Organisation\contact.html">
                        <li>Contact Us</li>
                    </a>
                </ul>
            </nav>
        </header>
    </div>

<main>
    <div class="container">
        <h2><?php echo htmlspecialchars($row['product_name']); ?></h2>
        <div class="product-container"> 
            <div class="product_img">
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
            </div>
            <div class="prod_description">
                <div class="price-rent">
                    <p><strong>Price:</strong> $<?php echo number_format($row['price'], 2); ?></p>
                    <p><strong>Rent (Per Day):</strong> $<?php echo number_format($row['rent'], 2); ?></p>
                </div>
                <p><strong>Description: </strong> <?php echo nl2br(htmlspecialchars($row['long_description'])); ?></p>
                <p><strong>Brand:</strong> <?php echo htmlspecialchars($row['brand']); ?></p>
                <p><strong>Product Dimensions:</strong> <?php echo htmlspecialchars($row['product_dimension']); ?></p>
                
                <form action="" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                    <label>
                        <input type="radio" name="order_type" value="buy" checked> Buy
                    </label>
                    <label>
                        <input type="radio" name="order_type" value="rent"> Rent
                    </label>
                    <input type="number" name="rent_days" min="1" placeholder="Days to Rent" style="display: none;" id="rent_days_input">
                    <br>
                    <button type="submit" class="add-to-cart">Add to Cart</button>
                    <p class="php_error"><?php echo $php_error; ?></p>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    const rentRadio = document.querySelector('input[value="rent"]');
    const rentDaysInput = document.getElementById('rent_days_input');

    rentRadio.addEventListener('change', function() {
        rentDaysInput.style.display = 'block';
    });

    document.querySelector('input[value="buy"]').addEventListener('change', function() {
        rentDaysInput.style.display = 'none';
    });
</script>

<footer>
    <p>&copy; 2025 Outdoor Adventure Gear. All Rights Reserved.</p>
    <p><a href="contact.html">Contact Us</a> | <a href="policies.html">Policies</a></p>
</footer>
</body>
</html>
