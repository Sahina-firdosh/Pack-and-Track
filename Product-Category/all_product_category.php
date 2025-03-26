<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login-Signup/login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'Ecommerce');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories and products
$categoriesQuery = "SELECT * FROM categories";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];

while ($category = $categoriesResult->fetch_assoc()) {
    $categoryId = $category['category_id'];
    $productQuery = $conn->prepare("SELECT * FROM products WHERE category_id = ? LIMIT 6");
    $productQuery->bind_param('i', $categoryId);
    $productQuery->execute();
    $result = $productQuery->get_result();
    $category['products'] = $result->fetch_all(MYSQLI_ASSOC);
    $categories[] = $category;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>All Products - Pack & Trek</title>
  <link rel="stylesheet" href="../basic/basic.css"> 
<link rel="stylesheet" href="all_product_category.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
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
        <h1>Welcome to Pack & Trek</h1>

        <?php foreach ($categories as $category) : ?>
            <section>
                <h2><?php echo htmlspecialchars($category['category_name']); ?></h2>
                <div class="slider-container">
                    <button class="prev" onclick="moveSlide(-1, <?php echo $category['category_id']; ?>)">&#10094;</button>
                    <div class="slider" id="slider-<?php echo $category['category_id']; ?>">
                        <?php foreach ($category['products'] as $product) : ?>
                            <div class="slide">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
                                <div class="name_price">
                                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                                </div>
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                                <a href="product_list.php?product_id=<?php echo $product['product_id']; ?>">View Details</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="next" onclick="moveSlide(1, <?php echo $category['category_id']; ?>)">&#10095;</button>
                </div>
            </section>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>&copy; 2025 Outdoor Adventure Gear. All Rights Reserved.</p>
        <p><a href="contact.html">Contact Us</a> | <a href="policies.html">Policies</a></p>
    </footer>

    <script>
        function moveSlide(direction, categoryId) {
            const slider = document.getElementById('slider-' + categoryId);
            const slides = slider.querySelectorAll('.slide');
            const slideWidth = slides[0].offsetWidth + 20; // Slide width + margin
            slider.scrollBy({ left: direction * slideWidth, behavior: 'smooth' });
        }
    </script>

</body>
</html>
