<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. '. $_POST['flat'] .', '. $_POST['province'] .', '. $_POST['city'];
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];
   $coupon_code = isset($_POST['coupon_code']) ? strtoupper(trim($_POST['coupon_code'])) : '';
   $discount = 0;
   $coupon_message = '';
   if($coupon_code != ''){
      $coupon = $conn->prepare("SELECT * FROM coupons WHERE code = ?");
      $coupon->execute([$coupon_code]);
      if($coupon->rowCount() > 0){
         $c = $coupon->fetch(PDO::FETCH_ASSOC);
         $now = date('Y-m-d H:i:s');
         $valid = true;
         if($c['expires_at'] && $now > $c['expires_at']) $valid = false;
         if($c['max_uses'] && $c['uses'] >= $c['max_uses']) $valid = false;
         if($total_price < $c['min_order']) $valid = false;
         if($valid){
            if($c['discount_type'] == 'percent'){
               $discount = round($total_price * ($c['discount_value']/100), 2);
            }else{
               $discount = $c['discount_value'];
            }
            if($discount > $total_price) $discount = $total_price;
            $total_price = $total_price - $discount;
            $coupon_message = 'Coupon applied! Discount: NRS '.$discount;
            // increment uses
            $conn->prepare("UPDATE coupons SET uses = uses + 1 WHERE id = ?")->execute([$c['id']]);
         }else{
            $coupon_message = 'Coupon is not valid for this order.';
         }
      }else{
         $coupon_message = 'Invalid coupon code.';
      }
   }

   // Check if user wants to save these details for future
   $save_details = isset($_POST['save_details']) ? 1 : 0;
   
   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);
   
   // If user wants to save details, store them in the database
   if($save_details) {
      // First, remove any existing default address
      $remove_default = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
      $remove_default->execute([$user_id]);
      
      // Insert new address
      $insert_address = $conn->prepare("INSERT INTO user_addresses (user_id, name, number, email, flat, province, city, is_default) VALUES (?,?,?,?,?,?,?,1) ON DUPLICATE KEY UPDATE name=VALUES(name), number=VALUES(number), email=VALUES(email), flat=VALUES(flat), province=VALUES(province), city=VALUES(city), is_default=1");
      $insert_address->execute([$user_id, $name, $number, $email, $_POST['flat'], $_POST['province'], $_POST['city']]);
   }

   $insufficient_stock = false;
   $cart_items = $check_cart->fetchAll(PDO::FETCH_ASSOC);
   foreach($cart_items as $item) {
      $pid = $item['pid'];
      $qty = $item['quantity'];
      $check_stock = $conn->prepare("SELECT stock FROM products WHERE id = ?");
      $check_stock->execute([$pid]);
      $product = $check_stock->fetch(PDO::FETCH_ASSOC);
      if(!$product || $product['stock'] < $qty) {
         $insufficient_stock = true;
         break;
      }
   }

   if($check_cart->rowCount() > 0 && !$insufficient_stock){
      // Update stock for each product
      foreach($cart_items as $item) {
         $pid = $item['pid'];
         $qty = $item['quantity'];
         $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
         $update_stock->execute([$qty, $pid]);
      }

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
   }else if($insufficient_stock){
      $message[] = 'One or more products in your cart do not have enough stock.';
   }else{
      $message[] = 'your cart is empty';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- Khalti Checkout Script -->
   <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
   
   <!-- eSewa Integration Script -->
   <script src="https://esewa.com.np/esewa.js"></script>
   
   <style>
      .payment-modal {
         display: none;
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background: rgba(0,0,0,0.7);
         z-index: 1000;
         justify-content: center;
         align-items: center;
      }
      .payment-content {
         background: white;
         padding: 2rem;
         border-radius: 5px;
         max-width: 500px;
         width: 90%;
         text-align: center;
      }
      .payment-content h3 {
         margin-bottom: 1.5rem;
         color: var(--black);
      }
      .payment-content p {
         margin: 1rem 0;
         color: var(--light-color);
      }
   </style>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <!-- Payment Modal -->
   <div class="payment-modal" id="paymentModal">
      <div class="payment-content">
         <h3 id="paymentTitle">Processing Payment</h3>
         <p id="paymentMessage">Please wait while we process your payment...</p>
         <div id="paymentContainer"></div>
         <button type="button" class="btn" onclick="document.getElementById('paymentModal').style.display = 'none';">Close</button>
      </div>
   </div>
   
   <form action="" method="POST" id="checkoutForm">
   <h3>your orders</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
         <?php
         // Initialize coupon variables
         $discount = 0;
         $discounted_total = $grand_total;
         $coupon_message = '';
         $applied_coupon = '';
         
         // Process coupon if submitted
         if(isset($_POST['apply_coupon']) && !empty($_POST['coupon_code'])) {
            $coupon_code = strtoupper(trim($_POST['coupon_code']));
            $coupon = $conn->prepare("SELECT * FROM coupons WHERE code = ?");
            $coupon->execute([$coupon_code]);
            
            if($coupon->rowCount() > 0) {
               $c = $coupon->fetch(PDO::FETCH_ASSOC);
               $now = date('Y-m-d H:i:s');
               $valid = true;
               $validation_errors = [];
               
               // Validate coupon
               if($c['expires_at'] && $now > $c['expires_at']) {
                  $valid = false;
                  $validation_errors[] = 'This coupon has expired.';
               }
               if($c['max_uses'] && $c['uses'] >= $c['max_uses']) {
                  $valid = false;
                  $validation_errors[] = 'This coupon has reached its maximum usage limit.';
               }
               if($grand_total < $c['min_order']) {
                  $valid = false;
                  $validation_errors[] = 'Minimum order amount of NRS ' . number_format($c['min_order'], 2) . ' required for this coupon.';
               }
               
               if($valid) {
                  // Calculate discount
                  if($c['discount_type'] == 'percent') {
                     $discount = round($grand_total * ($c['discount_value'] / 100), 2);
                  } else {
                     $discount = min($c['discount_value'], $grand_total);
                  }
                  
                  $discounted_total = $grand_total - $discount;
                  $applied_coupon = $coupon_code;
                  $coupon_message = 'Coupon applied! Discount: NRS ' . number_format($discount, 2);
               } else {
                  $coupon_message = implode(' ', $validation_errors);
               }
            } else {
               $coupon_message = 'Invalid coupon code.';
            }
         }
         ?>
         
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $discounted_total; ?>">
         
         <!-- Display order summary -->
         <div class="grand-total">
            <?php if($discount > 0): ?>
               <p>Subtotal: <span>NRS<?= number_format($grand_total, 2); ?>/-</span></p>
               <p>Discount: <span style="color: red;">-NRS<?= number_format($discount, 2); ?>/-</span></p>
            <?php endif; ?>
            <p>Total: <span>NRS<?= number_format($discounted_total, 2); ?>/-</span></p>
         </div>
         
         <!-- Coupon code input -->
         <div class="flex" style="margin-top: 1rem;">
            <div class="inputBox" style="flex: 1;">
               <span>Coupon Code:</span>
               <div style="display: flex; gap: 0.5rem; align-items: center;">
                  <input type="text" name="coupon_code" class="box" 
                         value="<?= htmlspecialchars($applied_coupon) ?>" 
                         placeholder="Enter coupon code"
                         <?= $discount > 0 ? 'readonly' : '' ?>>
                  <?php if($discount > 0): ?>
                     <button type="submit" name="remove_coupon" class="btn" style="margin: 0; background: #dc3545;">Remove</button>
                  <?php else: ?>
                     <button type="submit" name="apply_coupon" class="btn" style="margin: 0;">Apply</button>
                  <?php endif; ?>
               </div>
               <?php if(!empty($coupon_message)): ?>
                  <p style="color: <?= strpos($coupon_message, 'applied') !== false ? 'green' : 'red'; ?>; margin: 0.5rem 0 0; font-size: 1.4rem;">
                     <?= $coupon_message ?>
                  </p>
               <?php endif; ?>
            </div>
         </div>
         
         <?php if(!empty($coupon_message)): ?>
             <?php $message_class = strpos($coupon_message, 'applied') !== false ? 'success' : 'error'; ?>
             <div class="message <?= $message_class ?>" style="margin: 1rem 0; padding: 1rem; border-radius: 0.5rem; font-size: 1.4rem;">
                <?= $coupon_message ?>
             </div>
          <?php endif; ?>
          
          <input type="hidden" name="applied_coupon" value="<?= htmlspecialchars($applied_coupon) ?>">
      </div>

      <h3>place your orders</h3>

      <?php
      // Check if user has saved addresses
      $saved_address = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1");
      $saved_address->execute([$user_id]);
      $address = $saved_address->fetch(PDO::FETCH_ASSOC);
      ?>
      
      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" maxlength="20" value="<?= $address['name'] ?? '' ?>" required>
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" placeholder="enter your number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" value="<?= $address['number'] ?? '' ?>" required>
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" placeholder="enter your email" class="box" maxlength="50" value="<?= $address['email'] ?? '' ?>" required>
         </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <option value="khalti">khalti</option>
               <option value="esewa">esewa</option>
            </select>
         </div>
         
         <?php if(isset($user_id)): ?>
         <div class="inputBox" style="width: 100%; margin-top: 1rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
               <input type="checkbox" name="save_details" id="save_details" <?= isset($address) ? 'checked' : '' ?>>
               <span>Save these details for future orders</span>
            </label>
         </div>
         <?php endif; ?>
         <div class="inputBox">
            <span>province :</span>
            <input type="text" name="province" placeholder="e.g. province" class="box" maxlength="50" value="<?= $address['province'] ?? '' ?>" required>
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" placeholder="e.g. kathmandu" class="box" maxlength="50" value="<?= $address['city'] ?? '' ?>" required>
         </div>
        
         <div class="inputBox">
            <span>flat no. :</span>
            <input type="text" name="flat" placeholder="e.g. flat no." class="box" maxlength="50" value="<?= $address['flat'] ?? '' ?>" required>
         </div>
        
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

   <script>
      // Khalti Test Credentials
      var config = {
         "publicKey":"live_public_key_546eb6da05544d7d88961db04fdb9721",
         "productIdentity": "1234567890",
         "productName": "Online Store Purchase",
         "productUrl": window.location.href,
         "eventHandler": {
            onSuccess(payload) {
               // Hit merchant API for initiating verification
               console.log(payload);
               document.getElementById('paymentMessage').innerHTML = 'Payment successful! Processing your order...';
               // Submit the form after successful payment
               setTimeout(() => {
                  document.getElementById('checkoutForm').submit();
               }, 2000);
            },
            onError(error) {
               console.log(error);
               document.getElementById('paymentMessage').innerHTML = 'Payment failed. Please try again.';
            },
            onClose() {
               console.log('Widget is closing');
            }
         }
      };

      var checkout = new KhaltiCheckout(config);
      
      // Handle form submission
      document.getElementById('checkoutForm').addEventListener('submit', function(e) {
         var paymentMethod = document.querySelector('select[name="method"]').value;
         var totalAmount = <?= $grand_total * 100; ?>; // Convert to paisa for Khalti
         
         if (paymentMethod === 'khalti') {
            e.preventDefault();
            document.getElementById('paymentTitle').textContent = 'Khalti Payment';
            document.getElementById('paymentMessage').textContent = 'You will be redirected to Khalti payment gateway...';
            document.getElementById('paymentModal').style.display = 'flex';
            
            // Show Khalti payment widget
            checkout.show({amount: totalAmount});
            
         } else if (paymentMethod === 'esewa') {
            e.preventDefault();
            document.getElementById('paymentTitle').textContent = 'eSewa Payment';
            document.getElementById('paymentMessage').textContent = 'You will be redirected to eSewa payment gateway...';
            document.getElementById('paymentModal').style.display = 'flex';
            
            // eSewa integration
            var path = "https://uat.esewa.com.np/epay/main";
            var params = {
               amt: <?= $grand_total; ?>,
               psc: 0,
               pdc: 0,
               txAmt: 0,
               tAmt: <?= $grand_total; ?>,
               pid: '<?= uniqid(); ?>',
               scd: 'EPAYTEST',
               su: window.location.href + '?q=su',
               fu: window.location.href + '?q=fu'
            }
            
            // Submit to eSewa
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", path);
            
            for(var key in params) {
               var hiddenField = document.createElement("input");
               hiddenField.setAttribute("type", "hidden");
               hiddenField.setAttribute("name", key);
               hiddenField.setAttribute("value", params[key]);
               form.appendChild(hiddenField);
            }
            
            document.body.appendChild(form);
            form.submit();
            
         } else {
            // For cash on delivery, let the form submit normally
            return true;
         }
      });
      
      // Handle eSewa return URLs
      window.onload = function() {
         const urlParams = new URLSearchParams(window.location.search);
         const q = urlParams.get('q');
         
         if (q === 'su') {
            // Success URL
            alert('Payment successful! Your order has been placed.');
            // Submit the form to process the order
            document.getElementById('checkoutForm').submit();
         } else if (q === 'fu') {
            // Failure URL
            alert('Payment failed. Please try again.');
            window.location.href = window.location.pathname; // Remove query params
         }
      };
   </script>

</section>













<?php include 'components/footer.php'; ?>

<script>
function applyCoupon() {
   const couponCode = document.getElementById('coupon_code').value.trim();
   if (!couponCode) {
      alert('Please enter a coupon code');
      return;
   }
   
   // Submit the form to apply the coupon
   document.getElementById('coupon_code').value = couponCode.toUpperCase();
   document.getElementById('checkoutForm').submit();
}

// Auto-submit the form when pressing Enter in the coupon code field
document.getElementById('coupon_code').addEventListener('keypress', function(e) {
   if (e.key === 'Enter') {
      e.preventDefault();
      applyCoupon();
   }
});
</script>

<script src="js/script.js"></script>

</body>
</html>