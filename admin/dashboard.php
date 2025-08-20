<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
   <meta http-equiv="Pragma" content="no-cache">
   <meta http-equiv="Expires" content="0">
   <title>Admin Dashboard - FitGear</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>">

</head>
<body>

<!-- <?php include '../components/admin_header.php'; ?> -->

<section class="dashboard">
   
   <!-- Welcome Section -->
   <div class="welcome-section">
      <div class="welcome-content">
         <h1 class="dashboard-title">Welcome back, <?= $fetch_profile['name']; ?>! 👋</h1>
         <p class="dashboard-subtitle">Here's what's happening with your FitGear store today</p>
      </div>
      <div class="welcome-actions">
         <a href="update_profile.php" class="btn btn-outline">
            <i class="fas fa-user-edit"></i>
            Update Profile
         </a>
      </div>
   </div>

   <!-- Quick Stats Grid -->
   <div class="stats-grid">
     
      <!-- Revenue Stats -->
      <div class="stat-card revenue-card">
         <div class="stat-icon">
            <i class="fas fa-dollar-sign"></i>
         </div>
         <div class="stat-content">
            <?php
               $total_pendings = 0;
               $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
               $select_pendings->execute(['pending']);
               if($select_pendings->rowCount() > 0){
                  while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                     $total_pendings += $fetch_pendings['total_price'];
                  }
               }
            ?>
            <h3 class="stat-number">NRS <?= number_format($total_pendings); ?></h3>
            <p class="stat-label">Pending Revenue</p>
         </div>
         <div class="stat-trend">
            <i class="fas fa-clock"></i>
         </div>
      </div>

      <div class="stat-card revenue-card">
         <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
         </div>
         <div class="stat-content">
            <?php
               $total_completes = 0;
               $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
               $select_completes->execute(['completed']);
               if($select_completes->rowCount() > 0){
                  while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
                     $total_completes += $fetch_completes['total_price'];
                  }
               }
            ?>
            <h3 class="stat-number">NRS <?= number_format($total_completes); ?></h3>
            <p class="stat-label">Completed Revenue</p>
         </div>
         <div class="stat-trend">
            <i class="fas fa-check-circle"></i>
         </div>
      </div>

      <!-- Orders Stats -->
      <div class="stat-card orders-card">
         <div class="stat-icon">
            <i class="fas fa-shopping-bag"></i>
         </div>
         <div class="stat-content">
            <?php
               $select_orders = $conn->prepare("SELECT * FROM `orders`");
               $select_orders->execute();
               $number_of_orders = $select_orders->rowCount()
            ?>
            <h3 class="stat-number"><?= $number_of_orders; ?></h3>
            <p class="stat-label">Total Orders</p>
         </div>
         <div class="stat-trend">
            <i class="fas fa-arrow-up"></i>
         </div>
      </div>

      <!-- Products Stats -->
      <div class="stat-card products-card">
         <div class="stat-icon">
            <i class="fas fa-dumbbell"></i>
         </div>
         <div class="stat-content">
            <?php
               $select_products = $conn->prepare("SELECT * FROM `products`");
               $select_products->execute();
               $number_of_products = $select_products->rowCount()
            ?>
            <h3 class="stat-number"><?= $number_of_products; ?></h3>
            <p class="stat-label">Products</p>
         </div>
         <div class="stat-trend">
            <i class="fas fa-box"></i>
         </div>
      </div>

      <!-- Users Stats -->
      <div class="stat-card users-card">
         <div class="stat-icon">
            <i class="fas fa-users"></i>
         </div>
         <div class="stat-content">
            <?php
               $select_users = $conn->prepare("SELECT * FROM `users`");
               $select_users->execute();
               $number_of_users = $select_users->rowCount()
            ?>
            <h3 class="stat-number"><?= $number_of_users; ?></h3>
            <p class="stat-label">Customers</p>
         </div>
         <div class="stat-trend">
            <i class="fas fa-user-plus"></i>
         </div>
      </div>

      <!-- Messages Stats -->
      <div class="stat-card messages-card">
         <div class="stat-icon">
            <i class="fas fa-envelope"></i>
         </div>
         <div class="stat-content">
            <?php
               $select_messages = $conn->prepare("SELECT * FROM `messages`");
               $select_messages->execute();
               $number_of_messages = $select_messages->rowCount()
            ?>
            <h3 class="stat-number"><?= $number_of_messages; ?></h3>
            <p class="stat-label">New Messages</p>
         </div>
         <div class="stat-trend">
            <i class="fas fa-bell"></i>
         </div>
      </div>

   </div>

   <!-- Management Cards -->
   <div class="management-grid">
     
      <!-- Quick Actions -->
      <div class="management-card quick-actions">
         <div class="card-header">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
         </div>
         <div class="card-content">
            <div class="action-buttons">
               <a href="products.php" class="action-btn">
                  <i class="fas fa-plus"></i>
                  <span>Add Product</span>
               </a>
               <a href="placed_orders.php" class="action-btn">
                  <i class="fas fa-list"></i>
                  <span>View Orders</span>
               </a>
               <a href="messages.php" class="action-btn">
                  <i class="fas fa-comments"></i>
                  <span>Messages</span>
               </a>
               <a href="analytics.php" class="action-btn">
                  <i class="fas fa-chart-bar"></i>
                  <span>Analytics</span>
               </a>
            </div>
         </div>
      </div>

      <!-- Admin Management -->
      <div class="management-card admin-management">
         <div class="card-header">
            <h3><i class="fas fa-user-shield"></i> Admin Management</h3>
         </div>
         <div class="card-content">
            <?php
               $select_admins = $conn->prepare("SELECT * FROM `admins`");
               $select_admins->execute();
               $number_of_admins = $select_admins->rowCount()
            ?>
            <div class="management-stat">
               <div class="stat-info">
                  <h4><?= $number_of_admins; ?></h4>
                  <p>Admin Users</p>
               </div>
               <a href="admin_accounts.php" class="btn btn-primary">Manage Admins</a>
            </div>
         </div>
      </div>

      <!-- Reviews Management -->
      <div class="management-card reviews-management">
         <div class="card-header">
            <h3><i class="fas fa-star"></i> Reviews</h3>
         </div>
         <div class="card-content">
            <?php
               $select_reviews = $conn->prepare("SELECT * FROM reviews");
               $select_reviews->execute();
               $number_of_reviews = $select_reviews->rowCount();
            ?>
            <div class="management-stat">
               <div class="stat-info">
                  <h4><?= $number_of_reviews; ?></h4>
                  <p>Product Reviews</p>
               </div>
               <a href="reviews.php" class="btn btn-primary">View Reviews</a>
            </div>
         </div>
      </div>

      <!-- Coupons Management -->
      <div class="management-card coupons-management">
         <div class="card-header">
            <h3><i class="fas fa-ticket-alt"></i> Coupons</h3>
         </div>
         <div class="card-content">
            <?php
               $select_coupons = $conn->prepare("SELECT * FROM coupons");
               $select_coupons->execute();
               $number_of_coupons = $select_coupons->rowCount();
            ?>
            <div class="management-stat">
               <div class="stat-info">
                  <h4><?= $number_of_coupons; ?></h4>
                  <p>Active Coupons</p>
               </div>
               <a href="coupons.php" class="btn btn-primary">Manage Coupons</a>
            </div>
         </div>
      </div>

   </div>

   <!-- Recent Activity Section -->
   <div class="recent-activity">
      <div class="section-header">
         <h3><i class="fas fa-history"></i> Recent Activity</h3>
         <a href="placed_orders.php" class="view-all">View All</a>
      </div>
      <div class="activity-grid">
         <div class="activity-card">
            <div class="activity-icon">
               <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="activity-content">
               <h4>New Orders</h4>
               <p>Monitor and process incoming orders</p>
            </div>
            <a href="placed_orders.php" class="activity-link">
               <i class="fas fa-arrow-right"></i>
            </a>
         </div>
         
         <div class="activity-card">
            <div class="activity-icon">
               <i class="fas fa-comment"></i>
            </div>
            <div class="activity-content">
               <h4>Customer Messages</h4>
               <p>Respond to customer inquiries</p>
            </div>
            <a href="messages.php" class="activity-link">
               <i class="fas fa-arrow-right"></i>
            </a>
         </div>
         
         <div class="activity-card">
            <div class="activity-icon">
               <i class="fas fa-chart-line"></i>
            </div>
            <div class="activity-content">
               <h4>Sales Analytics</h4>
               <p>View detailed sales reports</p>
            </div>
            <a href="analytics.php" class="activity-link">
               <i class="fas fa-arrow-right"></i>
            </a>
         </div>
      </div>
   </div>

</section>

<script src="../js/admin_script.js"></script>
   
</body>
</html>