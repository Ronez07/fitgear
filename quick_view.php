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
   <title>Quick View</title>
   <!-- Font Awesome -->
   <script src="https://kit.fontawesome.com/56206acb38.js" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="css/style.css">
   <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
      
      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }
      
      body {
         font-family: 'Inter', sans-serif;
         background: #f8fafc;
         color: #1e293b;
         line-height: 1.6;
      }
      
      .container {
         max-width: 1200px;
         margin: 0 auto;
         padding: 0 20px;
      }
      
      .back-link {
         display: inline-flex;
         align-items: center;
         gap: 8px;
         color: #2563eb;
         text-decoration: none;
         font-weight: 500;
         margin-bottom: 24px;
         transition: color 0.3s ease;
      }
      
      .back-link:hover {
         color: #1d4ed8;
      }
      
      .product-grid {
         display: grid;
         grid-template-columns: 1fr;
         gap: 32px;
         margin-bottom: 32px;
      }
      
      @media (min-width: 1024px) {
         .product-grid {
            grid-template-columns: 1fr 1fr;
         }
      }
      
      .card {
         background: white;
         border-radius: 12px;
         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
         border: 1px solid #e2e8f0;
         overflow: hidden;
      }
      
      .card-content {
         padding: 24px;
      }
      
      .product-image-container {
         margin-bottom: 16px;
         border-radius: 8px;
         overflow: hidden;
      }
      
      .main-image {
         width: 100%;
         height: 400px;
         object-fit: contain;
         display: block;
      }
      
      .thumbnail-container {
         display: flex;
         gap: 8px;
         margin-top: 16px;
      }
      
      .thumbnail {
         width: 80px;
         height: 80px;
         border: 2px solid #e2e8f0;
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s ease;
         overflow: hidden;
      }
      
      .thumbnail:hover {
         border-color: #2563eb;
      }
      
      .thumbnail.active {
         border-color: #2563eb;
      }
      
      .thumbnail img {
         width: 100%;
         height: 100%;
         object-fit: cover;
      }
      
      .product-title {
         font-size: 24px;
         font-weight: 700;
         color: #1e293b;
         margin-bottom: 16px;
      }
      
      .rating-container {
         display: flex;
         align-items: center;
         margin-bottom: 16px;
      }
      
      .stars {
         display: flex;
         margin-right: 8px;
      }
      
      .star {
         color: #fbbf24;
         font-size: 16px;
      }
      
      .star.empty {
         color: #d1d5db;
      }
      
      .rating-text {
         color: #64748b;
         font-size: 14px;
      }
      
      .stock-status {
         margin-bottom: 16px;
      }
      
      .stock-label {
         color: #64748b;
         margin-right: 8px;
      }
      
      .stock-value {
         font-weight: 500;
      }
      
      .stock-value.in-stock {
         color: #059669;
      }
      
      .stock-value.out-of-stock {
         color: #dc2626;
      }
      
      .price {
         font-size: 32px;
         font-weight: 700;
         color: #1e293b;
         margin-bottom: 24px;
      }
      
      .description-section {
         margin-bottom: 24px;
      }
      
      .description-title {
         font-size: 18px;
         font-weight: 600;
         margin-bottom: 8px;
         color: #1e293b;
      }
      
      .description-text {
         color: #64748b;
         line-height: 1.7;
      }
      
      .actions-container {
         display: flex;
         flex-wrap: wrap;
         align-items: center;
         gap: 16px;
         margin-bottom: 24px;
      }
      
      .quantity-controls {
         display: flex;
         align-items: center;
         border: 1px solid #d1d5db;
         border-radius: 8px;
         overflow: hidden;
      }
      
      .qty-btn {
         padding: 8px 12px;
         background: #f1f5f9;
         border: none;
         color: #64748b;
         cursor: pointer;
         transition: background-color 0.3s ease;
      }
      
      .qty-btn:hover {
         background: #e2e8f0;
      }
      
      .qty-input {
         width: 60px;
         text-align: center;
         border: none;
         padding: 8px;
         font-size: 16px;
         outline: none;
      }
      
      .btn-group {
         display: flex;
         gap: 8px;
      }
      
      .btn {
         display: inline-flex;
         align-items: center;
         gap: 8px;
         padding: 12px 16px;
         border: none;
         border-radius: 8px;
         font-weight: 500;
         text-decoration: none;
         cursor: pointer;
         transition: all 0.3s ease;
         font-size: 14px;
      }
      
      .btn-outline {
         background: white;
         border: 2px solid black;
         color:rgb(26, 26, 27);
      }
      
      .btn-outline:hover {
         background: #eff6ff;
      }
      
      .btn-primary {
         background:rgb(0, 0, 0);
         color: white;
      }
      
      .btn-primary:hover {
         background: black;
        
      }
      
      .btn-disabled {
         background: #d1d5db;
         color: #6b7280;
         cursor: not-allowed;
      }
      
      .reviews-section {
         background: white;
         border-radius: 12px;
         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
         border: 1px solid #e2e8f0;
         padding: 24px;
      }
      
      .reviews-title {
         font-size: 18px;
         font-weight: 600;
         color: #1e293b;
         margin-bottom: 24px;
         display: flex;
         align-items: center;
         gap: 8px;
      }
      
      .reviews-title i {
         color: #fbbf24;
      }
      
      .review-form-container {
         background: #f8fafc;
         border-radius: 12px;
         padding: 24px;
         margin-bottom: 32px;
      }
      
      .review-form-title {
         font-size: 18px;
         font-weight: 600;
         margin-bottom: 16px;
         color: #1e293b;
      }
      
      .form-group {
         margin-bottom: 16px;
      }
      
      .form-label {
         display: block;
         color: #374151;
         margin-bottom: 8px;
         font-weight: 500;
      }
      
      .star-rating {
         display: flex;
         align-items: center;
      }
      
      .star-rating input {
         display: none;
      }
      
      .star-rating label {
         font-size: 24px;
         color: #d1d5db;
         cursor: pointer;
         transition: color 0.2s ease;
         margin-right: 4px;
      }
      
      .star-rating input:checked ~ label,
      .star-rating label:hover,
      .star-rating label:hover ~ label {
         color: #fbbf24;
      }
      
      .form-textarea {
         width: 100%;
         padding: 12px;
         border: 1px solid #d1d5db;
         border-radius: 8px;
         font-family: inherit;
         font-size: 14px;
         resize: vertical;
         outline: none;
         transition: border-color 0.3s ease;
      }
      
      .form-textarea:focus {
         border-color: #2563eb;
         box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      }
      
      .login-prompt {
         background: #f8fafc;
         border-radius: 12px;
         padding: 24px;
         margin-bottom: 32px;
         text-align: center;
      }
      
      .login-prompt a {
         color: #2563eb;
         text-decoration: none;
         font-weight: 500;
      }
      
      .login-prompt a:hover {
         text-decoration: underline;
      }
      
      .reviews-list {
         display: flex;
         flex-direction: column;
         gap: 16px;
      }
      
      .review-item {
         border: 1px solid #e2e8f0;
         border-radius: 8px;
         padding: 16px;
      }
      
      .review-header {
         display: flex;
         justify-content: space-between;
         align-items: flex-start;
         margin-bottom: 8px;
      }
      
      .reviewer-name {
         font-weight: 600;
         color: #1e293b;
      }
      
      .review-meta {
         display: flex;
         align-items: center;
         margin-top: 4px;
      }
      
      .review-stars {
         display: flex;
         margin-right: 8px;
      }
      
      .review-stars .star {
         font-size: 12px;
      }
      
      .review-date {
         color: #64748b;
         font-size: 12px;
      }
      
      .review-text {
         color: #64748b;
         line-height: 1.6;
         margin-top: 8px;
      }
      
      .no-reviews {
         text-align: center;
         padding: 32px;
         color: #64748b;
      }
      
      .not-found {
         background: white;
         border-radius: 12px;
         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
         border: 1px solid #e2e8f0;
         padding: 24px;
         text-align: center;
         color: #64748b;
      }
      
      @media (max-width: 768px) {
         .container {
            padding: 0 16px;
         }
         
         .product-grid {
            gap: 24px;
         }
         
         .card-content {
            padding: 16px;
         }
         
         .main-image {
            height: 300px;
         }
         
         .thumbnail {
            width: 60px;
            height: 60px;
         }
         
         .product-title {
            font-size: 20px;
         }
         
         .price {
            font-size: 24px;
         }
         
         .actions-container {
            flex-direction: column;
            align-items: stretch;
         }
         
         .btn-group {
            flex-direction: column;
         }
         
         .btn {
            justify-content: center;
         }
      }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="container">
   

   <?php
     $pid = $_GET['pid'];
     $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?"); 
     $select_products->execute([$pid]);
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
        
        // Calculate average rating
        $avg_rating = 0;
        $review_count = 0;
        $rating_query = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = ?");
        $rating_query->execute([$pid]);
        $rating_data = $rating_query->fetch(PDO::FETCH_ASSOC);
        if($rating_data) {
            $avg_rating = round($rating_data['avg_rating'], 1);
            $review_count = $rating_data['count'];
        }
   ?>
   
   <!-- Product Details Section -->
   <div class="product-grid">
      <!-- Product Images -->
      <div class="card">
         <div class="card-content">
            <div class="product-image-container">
               <img id="mainImage" src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="<?= $fetch_product['name']; ?>" class="main-image">
            </div>
            <div class="thumbnail-container">
               <div onclick="changeImage('uploaded_img/<?= $fetch_product['image_01']; ?>')" class="thumbnail active">
                  <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="Thumbnail 1">
               </div>
               <?php if(!empty($fetch_product['image_02'])): ?>
               <div onclick="changeImage('uploaded_img/<?= $fetch_product['image_02']; ?>')" class="thumbnail">
                  <img src="uploaded_img/<?= $fetch_product['image_02']; ?>" alt="Thumbnail 2">
               </div>
               <?php endif; ?>
               <?php if(!empty($fetch_product['image_03'])): ?>
               <div onclick="changeImage('uploaded_img/<?= $fetch_product['image_03']; ?>')" class="thumbnail">
                  <img src="uploaded_img/<?= $fetch_product['image_03']; ?>" alt="Thumbnail 3">
               </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
      
      <!-- Product Info -->
      <div class="card">
         <div class="card-content">
            <form action="" method="post">
               <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
               <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
               <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
               <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
               
               <h2 class="product-title"><?= $fetch_product['name']; ?></h2>
               
               <!-- Rating Display -->
               <div class="rating-container">
                  <div class="stars">
                     <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star star <?= $i <= round($avg_rating) ? '' : 'empty' ?>"></i>
                     <?php endfor; ?>
                  </div>
                  <span class="rating-text"><?= $avg_rating ?> (<?= $review_count ?> reviews)</span>
               </div>
               
               <!-- Stock Status -->
               <div class="stock-status">
                  <span class="stock-label">Availability:</span>
                  <span class="stock-value <?= $fetch_product['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                     <?= $fetch_product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                  </span>
               </div>
               
               <!-- Price -->
               <div class="price">NRS <?= number_format($fetch_product['price']) ?> /-</div>
               
               <!-- Description -->
               <div class="description-section">
                  <h3 class="description-title">Description</h3>
                  <p class="description-text"><?= nl2br($fetch_product['details']) ?></p>
               </div>
               
               <!-- Quantity and Actions -->
               <div class="actions-container">
                  <?php if($fetch_product['stock'] > 0): ?>
                  <div class="quantity-controls">
                     <button type="button" onclick="decreaseQty()" class="qty-btn">-</button>
                     <input type="number" name="qty" id="qtyInput" class="qty-input" min="1" max="<?= $fetch_product['stock']; ?>" value="1">
                     <button type="button" onclick="increaseQty()" class="qty-btn">+</button>
                  </div>
                  <?php endif; ?>
                  
                  <div class="btn-group">
                     <button type="submit" name="add_to_wishlist" class="btn btn-outline">
                        <i class="far fa-heart"></i> Wishlist
                     </button>
                     
                     <?php if($fetch_product['stock'] > 0): ?>
                     <button type="submit" name="add_to_cart" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                     </button>
                     <?php else: ?>
                     <button class="btn btn-disabled" disabled>
                        Out of Stock
                     </button>
                     <?php endif; ?>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Reviews Section -->
   <div class="reviews-section">
      <h2 class="reviews-title">
         <i class="fas fa-star"></i>
         Customer Reviews
      </h2>
      
      <!-- Review Form -->
      <?php if($user_id): ?>
      <div class="review-form-container">
         <h3 class="review-form-title">Write a Review</h3>
         <form action="" method="post">
            <input type="hidden" name="review_pid" value="<?= $fetch_product['id']; ?>">
            
            <div class="form-group">
               <label class="form-label">Your Rating</label>
               <div class="star-rating">
                  <input type="radio" id="star5" name="rating" value="5" required>
                  <label for="star5">★</label>
                  <input type="radio" id="star4" name="rating" value="4">
                  <label for="star4">★</label>
                  <input type="radio" id="star3" name="rating" value="3">
                  <label for="star3">★</label>
                  <input type="radio" id="star2" name="rating" value="2">
                  <label for="star2">★</label>
                  <input type="radio" id="star1" name="rating" value="1">
                  <label for="star1">★</label>
               </div>
            </div>
            
            <div class="form-group">
               <label for="review" class="form-label">Your Review</label>
               <textarea name="review" id="review" rows="4" class="form-textarea" required placeholder="Share your experience with this product..."></textarea>
            </div>
            
            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
         </form>
      </div>
      <?php else: ?>
      <div class="login-prompt">
         <p>Please <a href="user_login.php">login</a> to leave a review.</p>
      </div>
      <?php endif; ?>
      
      <!-- Reviews List -->
      <div class="reviews-list">
         <?php
            $select_reviews = $conn->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
            $select_reviews->execute([$fetch_product['id']]);
            if($select_reviews->rowCount() > 0){
               while($review = $select_reviews->fetch(PDO::FETCH_ASSOC)){
         ?>
         <div class="review-item">
            <div class="review-header">
               <div>
                  <div class="reviewer-name"><?= htmlspecialchars($review['user_name']) ?></div>
                  <div class="review-meta">
                     <div class="review-stars">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                           <i class="fas fa-star star <?= $i <= $review['rating'] ? '' : 'empty' ?>"></i>
                        <?php endfor; ?>
                     </div>
                     <span class="review-date"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                  </div>
               </div>
            </div>
            <div class="review-text"><?= nl2br(htmlspecialchars($review['review'])) ?></div>
         </div>
         <?php
               }
            }else{
               echo '<div class="no-reviews"><p>No reviews yet. Be the first to review!</p></div>';
            }
         ?>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<div class="not-found"><p>Product not found!</p></div>';
   }
   ?>
</div>

<?php include 'components/footer.php'; ?>

<script>
   // Change main image when thumbnail clicked
   function changeImage(src) {
      document.getElementById('mainImage').src = src;
      // Update active thumbnail
      document.querySelectorAll('.thumbnail').forEach(el => {
         el.classList.remove('active');
      });
      event.currentTarget.classList.add('active');
   }
   
   // Quantity controls
   function increaseQty() {
      const qtyInput = document.getElementById('qtyInput');
      const max = parseInt(qtyInput.max);
      if(parseInt(qtyInput.value) < max) {
         qtyInput.value = parseInt(qtyInput.value) + 1;
      }
   }
   
   function decreaseQty() {
      const qtyInput = document.getElementById('qtyInput');
      if(parseInt(qtyInput.value) > 1) {
         qtyInput.value = parseInt(qtyInput.value) - 1;
      }
   }
</script>

</body>
</html>

<?php
// Handle review submission
if(isset($_POST['submit_review']) && $user_id && isset($_POST['review_pid'])){
   $review_pid = $_POST['review_pid'];
   $rating = intval($_POST['rating']);
   $review_text = trim($_POST['review']);
   if($rating >= 1 && $rating <= 5 && $review_text !== ''){
      $insert_review = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
      $insert_review->execute([$review_pid, $user_id, $rating, $review_text]);
      echo "<script>location.reload();</script>";
   }
}
?>