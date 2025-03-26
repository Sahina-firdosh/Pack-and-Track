<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'Ecommerce');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$stmt = $conn->prepare("SELECT first_name, last_name, phone_number, address FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch cart details
$query = "SELECT p.product_id, p.product_name, p.price, p.image_url, c.quantity 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_name = trim($_POST['recipient_name']);
    $recipient_phone = trim($_POST['recipient_phone']);
    $recipient_address = trim($_POST['recipient_address']);
    $payment_method = $_POST['payment_method'];
    $total_price = array_sum(array_column($products, 'price'));

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, order_date, status, recipient_name, recipient_phone, recipient_address, payment_method, payment_status) VALUES (?, ?, NOW(), 'Pending', ?, ?, ?, ?, 'Unpaid')");
    $stmt->bind_param('idssss', $user_id, $total_price, $recipient_name, $recipient_phone, $recipient_address, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items and clear cart
    foreach ($products as $product) {
        $stmt = $conn->prepare("INSERT INTO OrderDetails (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $order_id, $product['product_id'], $product['quantity'], $product['price']);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    header('Location: order_confirmation.php?order_id=' . $order_id);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Pack & Trek</title>
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="../basic/basic.css">
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
                    <a href="cart.php" class="cart-icon">
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
        <div class="checkout-container">
            <!-- Left Section: Form -->
            <form class="checkout-form" method="post">
                <h2>Shipping Details</h2>
                <label>Recipient Name:</label>
                <input type="text" name="recipient_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" required><br>

                <label>Phone Number:</label>
                <input type="text" name="recipient_phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required><br>

                <label>Address:</label>
                <textarea name="recipient_address" required><?php echo htmlspecialchars($user['address']); ?></textarea><br>

                <label>Payment Method:</label>
                <select name="payment_method">
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select><br>

                <button type="submit">Confirm Order</button>
            </form>

            <!-- Right Section: Order Summary -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                <ul>
                    <?php foreach ($products as $product) : ?>
                    <li>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" width="80">
                        <span><?php echo htmlspecialchars($product['product_name']); ?></span>
                        <span>$<?php echo number_format($product['price'], 2); ?> x <?php echo $product['quantity']; ?> = $<?php echo number_format($product['price'] * $product['quantity'], 2); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <h3>Total: $<?php echo number_format(array_sum(array_map(function($product){ return $product['price'] * $product['quantity']; }, $products)), 2); ?></h3>
                <a href="cart.php">Back to Cart</a>
            </div>
        </div>
    </main>


    <footer>
        <p>&copy; 2025 Outdoor Adventure Gear. All Rights Reserved.</p>
        <p><a href="contact.html">Contact Us</a> | <a href="policies.html">Policies</a></p>
    </footer>
</body>
</html>
	
