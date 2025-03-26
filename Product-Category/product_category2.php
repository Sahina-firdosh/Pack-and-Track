<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'Ecommerce');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT product_id, product_name, description, price, image_url FROM Products WHERE category_id = 2";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Hiking Equipment - Pack & Trek</title>
  <link rel="stylesheet" href="../basic/basic.css"> 
  <link rel="stylesheet" href="product_category.css"> 
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
    <section class="category-page">
      <h1>Hiking Equipment</h1>

      <div class="product-grid">
        <?php while ($row = $result->fetch_assoc()) { ?>
          <div class="product">
            <a href="product.html?id=<?php echo $row['product_id']; ?>">
              <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>">
              <h3><?php echo $row['product_name']; ?> <p>$<?php echo number_format($row['price'], 2); ?></p></h3>
              <p class="product-description"><?php echo $row['description']; ?></p>
              <button class="add-to-cart">Add to Cart</button>
            </a>
          </div>
        <?php } ?>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Outdoor Adventure Gear. All Rights Reserved.</p>
    <p><a href="contact.html">Contact Us</a> | <a href="policies.html">Policies</a></p>
  </footer>

</body>
</html>

<?php
$conn->close();
?>
