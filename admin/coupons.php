<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
}
// Handle add coupon
if(isset($_POST['add_coupon'])){
   $code = strtoupper(trim($_POST['code']));
   $discount_type = $_POST['discount_type'];
   $discount_value = floatval($_POST['discount_value']);
   $min_order = floatval($_POST['min_order']);
   $max_uses = $_POST['max_uses'] !== '' ? intval($_POST['max_uses']) : null;
   $expires_at = $_POST['expires_at'] !== '' ? $_POST['expires_at'] : null;
   $check = $conn->prepare("SELECT * FROM coupons WHERE code = ?");
   $check->execute([$code]);
   if($check->rowCount() > 0){
      $message = 'Coupon code already exists!';
   }else{
      $insert = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_order, max_uses, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
      $insert->execute([$code, $discount_type, $discount_value, $min_order, $max_uses, $expires_at]);
      $message = 'Coupon added!';
   }
}
// Handle delete coupon
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $conn->prepare("DELETE FROM coupons WHERE id = ?")->execute([$delete_id]);
   header('location:coupons.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Coupons</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <style>
      .coupons {
         padding: 2rem 9%;
      }

      .coupons .box-container {
         margin-top: 2rem;
      }

      .coupon-form {
         background: var(--white);
         border-radius: .5rem;
         padding: 2rem;
         margin-bottom: 3rem;
         border: var(--border);
         box-shadow: var(--box-shadow);
      }

      .coupon-form h3 {
         font-size: 2rem;
         color: var(--black);
         margin-bottom: 1.5rem;
         padding-bottom: 1rem;
         border-bottom: var(--border);
      }

      .coupon-form .flex {
         display: flex;
         flex-wrap: wrap;
         gap: 1.5rem;
         margin-bottom: 1.5rem;
      }

      .coupon-form .inputBox {
         flex: 1 1 30rem;
      }

      .coupon-form .inputBox span {
         display: block;
         font-size: 1.6rem;
         color: var(--light-color);
         margin-bottom: 1rem;
      }

      .coupon-form .box {
         width: 100%;
         padding: 1.2rem 1.4rem;
         border: var(--border);
         border-radius: .5rem;
         font-size: 1.6rem;
         margin: 1rem 0;
         background: var(--light-bg);
      }

      .coupon-form .btn {
         margin-top: 1rem;
         width: 100%;
         text-align: center;
      }

      .coupons-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 2rem;
         background: var(--white);
         border-radius: .5rem;
         overflow: hidden;
         box-shadow: var(--box-shadow);
      }

      .coupons-table th,
      .coupons-table td {
         padding: 1.5rem;
         text-align: left;
         border-bottom: var(--border);
      }

      .coupons-table th {
         background: var(--black);
         color: var(--white);
         font-weight: 500;
         text-transform: uppercase;
         font-size: 1.4rem;
      }

      .coupons-table tr:hover {
         background: var(--light-bg);
      }

      .coupons-table .status {
         display: inline-block;
         padding: .5rem 1rem;
         border-radius: .5rem;
         font-size: 1.2rem;
         font-weight: 500;
      }

      .coupons-table .status.active {
         background: #d4edda;
         color: #155724;
      }

      .coupons-table .status.expired {
         background: #f8d7da;
         color: #721c24;
      }

      .delete-btn {
         display: inline-block;
         padding: .5rem 1.5rem;
         background: var(--red);
         color: var(--white);
         border-radius: .5rem;
         cursor: pointer;
         font-size: 1.4rem;
         transition: .2s linear;
      }

      .delete-btn:hover {
         background: #c0392b;
      }

      .empty {
         text-align: center;
         font-size: 1.8rem;
         color: var(--light-color);
         padding: 3rem 0;
      }
   </style>
</head>
<body>
<?php include '../components/admin_header.php'; ?>

<section class="coupons">
   <h1 class="heading">Manage Coupons</h1>

   <div class="box-container">
      <form action="" method="post" class="coupon-form">
         <h3>Add New Coupon</h3>
         <div class="flex">
            <div class="inputBox">
               <span>Coupon Code</span>
               <input type="text" name="code" class="box" placeholder="Enter coupon code" maxlength="50" required style="text-transform:uppercase;">
            </div>
            <div class="inputBox">
               <span>Discount Type</span>
               <select name="discount_type" class="box" required>
                  <option value="percent">Percentage (%)</option>
                  <option value="fixed">Fixed Amount (NRS)</option>
               </select>
            </div>
         </div>
         <div class="flex">
            <div class="inputBox">
               <span>Discount Value</span>
               <input type="number" name="discount_value" class="box" placeholder="Enter discount value" min="1" step="0.01" required>
            </div>
            <div class="inputBox">
               <span>Minimum Order (optional)</span>
               <input type="number" name="min_order" class="box" placeholder="Enter minimum order amount" min="0" step="0.01">
            </div>
         </div>
         <div class="flex">
            <div class="inputBox">
               <span>Max Uses (optional)</span>
               <input type="number" name="max_uses" class="box" placeholder="Enter maximum number of uses" min="1">
            </div>
            <div class="inputBox">
               <span>Expiry Date (optional)</span>
               <input type="datetime-local" name="expires_at" class="box">
            </div>
         </div>
         <input type="submit" name="add_coupon" value="Add Coupon" class="btn">
      </form>

      <h2 class="heading">All Coupons</h2>
      <?php if(isset($message)): ?>
         <p class="message" style="color: var(--red); font-size: 1.6rem; margin-bottom: 2rem; text-align: center;"><?= $message ?></p>
      <?php endif; ?>
      
      <table class="coupons-table">
         <thead>
            <tr>
               <th>Code</th>
               <th>Type</th>
               <th>Value</th>
               <th>Min Order</th>
               <th>Max Uses</th>
               <th>Used</th>
               <th>Expires</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
      <tbody>
      <?php
         $coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
         if($coupons->rowCount() > 0){
            while($c = $coupons->fetch(PDO::FETCH_ASSOC)){
               $is_expired = !empty($c['expires_at']) && strtotime($c['expires_at']) < time();
               $status_class = $is_expired ? 'expired' : 'active';
               $status_text = $is_expired ? 'Expired' : 'Active';
      ?>
         <tr>
            <td><strong><?= htmlspecialchars($c['code']) ?></strong></td>
            <td><?= ucfirst($c['discount_type']) ?></td>
            <td>
               <?= $c['discount_type'] == 'percent' ? number_format($c['discount_value'], 0).'%' : 'NRS '.number_format($c['discount_value'], 2) ?>
            </td>
            <td>NRS <?= number_format($c['min_order'], 2) ?></td>
            <td><?= $c['max_uses'] ?: '∞' ?></td>
            <td><?= $c['uses'] ?? 0 ?></td>
            <td><?= $c['expires_at'] ? date('M j, Y H:i', strtotime($c['expires_at'])) : 'No expiry' ?></td>
            <td>
               <span class="status <?= $status_class ?>"><?= $status_text ?></span>
            </td>
            <td>
               <a href="coupons.php?delete=<?= $c['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this coupon?');">
                  <i class="fas fa-trash"></i> Delete
               </a>
            </td>
         </tr>
      <?php
            }
         }else{
      ?>
         <tr>
            <td colspan="9" class="empty">
               <p>No coupons found. Add your first coupon using the form above.</p>
            </td>
         </tr>
      <?php } ?>
      </tbody>
   </table>
   
   <script>
      // Add confirmation for delete action
      document.addEventListener('DOMContentLoaded', function() {
         const deleteButtons = document.querySelectorAll('.delete-btn');
         deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
               if (!confirm('Are you sure you want to delete this coupon?')) {
                  e.preventDefault();
               }
            });
         });
      });
   </script>
</div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>