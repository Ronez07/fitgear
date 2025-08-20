<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>FitGear - Premium Fitness Equipment</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <script src="https://kit.fontawesome.com/56206acb38.js" crossorigin="anonymous"></script>
   
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
   <div class="hero-container">
      <div class="hero-content">
         <div class="hero-text">
            <h1 class="hero-title">
               Transform Your <span class="gradient-text">Fitness Journey</span>
            </h1>
            <p class="hero-subtitle">
               Discover premium fitness equipment and accessories designed to elevate your workout experience. From cardio machines to strength training, we have everything you need to achieve your fitness goals.
            </p>
            <div class="hero-buttons">
               <a href="shop.php" class="btn btn-primary">Shop Now</a>
               <a href="about.php" class="btn btn-secondary">Learn More</a>
            </div>
            <div class="hero-stats">
               <div class="stat-item">
                  <span class="stat-number">10+</span>
                  <span class="stat-label">Happy Customers</span>
               </div>
               <div class="stat-item">
                  <span class="stat-number">50+</span>
                  <span class="stat-label">Products</span>
               </div>
               <div class="stat-item">
                  <span class="stat-number">24/7</span>
                  <span class="stat-label">Support</span>
               </div>
            </div>
         </div>
         <div class="hero-image">
            <img src="images/1000.png" alt="Fitness Equipment" class="main-hero-img">
            <div class="floating-card card-1">
               <i class="fas fa-dumbbell"></i>
               <span>Premium Quality</span>
            </div>
            <div class="floating-card card-2">
               <i class="fas fa-shipping-fast"></i>
               <span>Fast Delivery</span>
            </div>
         </div>
      </div>
   </div>
   <div class="hero-bg-shapes">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
   </div>
</section>

<!-- Features Section -->
<section class="features-section">
   <div class="container">
      <div class="section-header">
         <h2>Why Choose FitGear?</h2>
         <p>We're committed to providing the best fitness experience</p>
      </div>
      <div class="features-grid">
         <div class="feature-card">
            <div class="feature-icon">
               <i class="fas fa-medal"></i>
            </div>
            <h3>Premium Quality</h3>
            <p>All our products are made with the highest quality materials and craftsmanship.</p>
         </div>
         <div class="feature-card">
            <div class="feature-icon">
               <i class="fas fa-truck"></i>
            </div>
            <h3>Fast Delivery</h3>
            <p>Get your fitness equipment delivered to your doorstep within 24-48 hours.</p>
         </div>
         <div class="feature-card">
            <div class="feature-icon">
               <i class="fas fa-headset"></i>
            </div>
            <h3>24/7 Support</h3>
            <p>Our customer support team is always ready to help you with any questions.</p>
         </div>
         <div class="feature-card">
            <div class="feature-icon">
               <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Warranty</h3>
            <p>All products come with comprehensive warranty and return policies.</p>
         </div>
      </div>
   </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
   <div class="container">
      <div class="section-header">
         <h2>Shop by Category</h2>
         <p>Find the perfect equipment for your fitness routine</p>
      </div>
      <div class="categories-grid">
         <a href="category.php?category=shoes" class="category-card">
            <div class="category-image">
               <img src="images/1000.png" alt="Footwear">
            </div>
            <div class="category-content">
               <h3>Footwear</h3>
               <p>Premium athletic shoes for every workout</p>
               <span class="category-arrow">→</span>
            </div>
         </a>
         <a href="category.php?category=acc" class="category-card">
            <div class="category-image">
               <img src="images/10000.png" alt="Accessories">
            </div>
            <div class="category-content">
               <h3>Accessories</h3>
               <p>Essential fitness accessories and gear</p>
               <span class="category-arrow">→</span>
            </div>
         </a>
         <a href="category.php?category=Nutri" class="category-card">
            <div class="category-image">
               <img src="images/999.png" alt="Nutrition">
            </div>
            <div class="category-content">
               <h3>Nutrition & Supplements</h3>
               <p>Fuel your body with quality supplements</p>
               <span class="category-arrow">→</span>
            </div>
         </a>
         <a href="category.php?category=recovery" class="category-card">
            <div class="category-image">
               <img src="images/888.png" alt="Recovery">
            </div>
            <div class="category-content">
               <h3>Recovery & Wellness</h3>
               <p>Tools for optimal recovery and wellness</p>
               <span class="category-arrow">→</span>
            </div>
         </a>
         <a href="category.php?category=cardio" class="category-card">
            <div class="category-image">
               <img src="images/992.png" alt="Cardio">
            </div>
            <div class="category-content">
               <h3>Cardio Machines</h3>
               <p>Professional cardio equipment for home</p>
               <span class="category-arrow">→</span>
            </div>
         </a>
         <a href="category.php?category=equipment" class="category-card">
            <div class="category-image">
               <img src="images/2154.png" alt="Equipment">
            </div>
            <div class="category-content">
               <h3>Fitness Equipment</h3>
               <p>Complete range of fitness equipment</p>
               <span class="category-arrow">→</span>
            </div>
         </a>
      </div>
   </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products">
   <div class="container">
      <div class="section-header">
         <h2>Featured Products</h2>
         <p>Handpicked products for your fitness journey</p>
      </div>
      <div class="products-grid">
         <?php
           $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 8");
           $select_products->execute();
           if($select_products->rowCount() > 0){
            while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="product-card">
            <form action="" method="post" class="product-form">
               <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
               <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
               <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
               <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
               
               <div class="product-image">
                  <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="<?= $fetch_product['name']; ?>">
                  <div class="product-actions">
                     <button class="action-btn wishlist-btn" type="submit" name="add_to_wishlist">
                        <i class="fas fa-heart"></i>
                     </button>
                     <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="action-btn view-btn">
                        <i class="fas fa-eye"></i>
                     </a>
                  </div>
                  <?php if($fetch_product['stock'] <= 0): ?>
                  <div class="out-of-stock-badge">Out of Stock</div>
                  <?php endif; ?>
               </div>
               
               <div class="product-info">
                  <h3 class="product-name"><?= $fetch_product['name']; ?></h3>
                  <div class="product-meta">
                     <span class="stock-info">Stock: <strong><?= $fetch_product['stock']; ?></strong></span>
                  </div>
                  <div class="product-price">
                     <span class="currency">NRS</span>
                     <span class="amount"><?= $fetch_product['price']; ?></span>
                     <span class="suffix">/-</span>
                  </div>
                 
                  <div class="product-actions-bottom">
                     <?php if($fetch_product['stock'] > 0): ?>
                     <div class="quantity-selector">
                        <input type="number" name="qty" class="qty-input" min="1" max="<?= $fetch_product['stock']; ?>" value="1">
                     </div>
                     <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                     </button>
                     <?php else: ?>
                     <button class="add-to-cart-btn disabled" disabled>
                        <i class="fas fa-times"></i>
                        Out of Stock
                     </button>
                     <?php endif; ?>
                  </div>
               </div>
            </form>
         </div>
         <?php
            }
         }else{
            echo '<div class="no-products"><p>No products available at the moment.</p></div>';
         }
         ?>
      </div>
      <div class="view-all-container">
         <a href="shop.php" class="btn btn-outline">View All Products</a>
      </div>
   </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
   <div class="container">
      <div class="section-header">
         <h2>What Our Customers Say</h2>
         <p>Real feedback from fitness enthusiasts</p>
      </div>
      <div class="testimonials-grid">
         <div class="testimonial-card">
            <div class="testimonial-content">
               <p>"FitGear has transformed my home workout routine. The quality of their equipment is outstanding!"</p>
            </div>
            <div class="testimonial-author">
               <div class="author-avatar">
                  <i class="fas fa-user"></i>
               </div>
               <div class="author-info">
                  <h4>Yubik</h4>
                  <span>Fitness Enthusiast</span>
               </div>
               <div class="rating">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
               </div>
            </div>
         </div>
         <div class="testimonial-card">
            <div class="testimonial-content">
               <p>"Fast delivery and excellent customer service. Highly recommend FitGear for all fitness needs!"</p>
            </div>
            <div class="testimonial-author">
               <div class="author-avatar">
                  <i class="fas fa-user"></i>
               </div>
               <div class="author-info">
                  <h4>Sujal</h4>
                  <span>Personal Trainer</span>
               </div>
               <div class="rating">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
               </div>
            </div>
         </div>
         <div class="testimonial-card">
            <div class="testimonial-content">
               <p>"The best fitness equipment I've ever purchased. Durable, reliable, and perfect for my home gym."</p>
            </div>
            <div class="testimonial-author">
               <div class="author-avatar">
                  <i class="fas fa-user"></i>
               </div>
               <div class="author-info">
                  <h4>Biparsan</h4>
                  <span>National player</span>
               </div>
               <div class="rating">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
   <div class="container">
      <div class="cta-content">
         <h2>Ready to Start Your Fitness Journey?</h2>
         <p>Join thousands of customers who have transformed their lives with FitGear</p>
         <div class="cta-buttons">
            <a href="shop.php" class="btn btn-primary">Shop Now</a>
            <a href="contact.php" class="btn btn-secondary">Contact Us</a>
         </div>
      </div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>