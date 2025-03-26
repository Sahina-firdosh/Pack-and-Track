<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login-Signup/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'Ecommerce');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product removal
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    header('Location: cart.php');
    exit();
}

// Update quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = max(1, (int)$quantity); // Ensure quantity is at least 1
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param('iii', $quantity, $user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: cart.php');
    exit();
}

// Retrieve cart products with order type and rent days
$query = "SELECT p.product_id, p.product_name, p.price, p.image_url, c.quantity, c.order_type, c.rent_days 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart - Pack & Trek</title>
    <link rel="stylesheet" href="cart.css">
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

    <div class="main_container">
        <main>
            <h1>Shopping Cart</h1>

            <?php if (empty($products)) : ?>
                <p>Your cart is empty.</p>
            <?php else : ?>
                <form method="post">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Image</th>
                                <th>Product</th>
                                <th>Order Type</th>
                                <th>Rent Days</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $grandTotal = 0; ?>
                            <?php foreach ($products as $product) : ?>
                                <?php
                                $total = $product['price'] * $product['quantity'];
                                if ($product['order_type'] === 'rent' && $product['rent_days'] > 0) {
                                    $total *= $product['rent_days'];
                                }
                                $grandTotal += $total;
                                ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" width="100"></td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['order_type']); ?></td>
                                    <td>
                                        <?php
                                        echo $product['order_type'] === 'rent'? htmlspecialchars($product['rent_days']) . ' days' : '-';
                                        ?>
                                    </td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantity[<?php echo $product['product_id']; ?>]" value="<?php echo $product['quantity']; ?>" min="1">
                                    </td>
                                    <td>$<?php echo number_format($total, 2); ?></td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $product['product_id']; ?>">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h3>Grand Total: $<?php echo number_format($grandTotal, 2); ?></h3>
                    <button type="submit" name="update_cart">Update Cart</button>
                    <a href="checkout.php" class="button_link">Proceed to Checkout</a>
                </form>
            <?php endif; ?>

            <p><a href="..\Product-Category\all_product_category.php">Continue Shopping</a></p>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 Outdoor Adventure Gear. All Rights Reserved.</p>
        <p><a href="contact.html">Contact Us</a> | <a href="policies.html">Policies</a></p>
    </footer>
</body>
</html>
