<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Product Reviews</title>
   <link rel="stylesheet" href="../css/admin_style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <style>
      .reviews {
         padding: 2rem 9%;
      }

      .reviews .box-container {
         margin-top: 2rem;
         background: var(--white);
         border-radius: .5rem;
         padding: 2rem;
         border: var(--border);
         box-shadow: var(--box-shadow);
      }

      .reviews table {
         width: 100%;
         border-collapse: collapse;
      }

      .reviews th, 
      .reviews td {
         padding: 1.5rem;
         text-align: left;
         border-bottom: var(--border);
      }

      .reviews th {
         background: var(--light-bg);
         font-weight: 600;
         color: var(--black);
         text-transform: uppercase;
         font-size: 1.4rem;
      }

      .reviews tr:hover {
         background: var(--light-bg);
      }

      .stars {
         color: var(--orange);
         font-size: 1.4rem;
         margin-bottom: .5rem;
      }

      .review-text {
         font-size: 1.4rem;
         color: var(--light-color);
         line-height: 1.6;
      }

      .action-btns {
         display: flex;
         gap: 1rem;
      }

      .btn {
         display: inline-block;
         padding: .5rem 1rem;
         border-radius: .5rem;
         color: var(--white);
         cursor: pointer;
         font-size: 1.4rem;
         text-transform: capitalize;
         transition: .2s linear;
         text-align: center;
      }

      .btn-delete {
         background: var(--red);
      }

      .btn-delete:hover {
         background: #c0392b;
      }

      .empty {
         text-align: center;
         font-size: 1.8rem;
         color: var(--red);
         padding: 2rem;
      }

      /* Pagination Styles */
      .pagination {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-top: 2rem;
         padding: 1.5rem;
         background: var(--white);
         border-radius: .5rem;
         border: var(--border);
         box-shadow: var(--box-shadow);
      }

      .pagination-info {
         font-size: 1.4rem;
         color: var(--light-color);
      }

      .pagination-info span {
         color: var(--black);
         font-weight: bold;
      }

      .pagination-nav {
         display: flex;
         gap: .5rem;
      }

      .page-nav,
      .page-number {
         display: flex;
         align-items: center;
         justify-content: center;
         width: 3.5rem;
         height: 3.5rem;
         border: var(--border);
         border-radius: .5rem;
         font-size: 1.4rem;
         color: var(--black);
         background: var(--white);
         transition: all .2s linear;
      }

      .page-nav:hover,
      .page-number:hover {
         background: var(--light-bg);
      }

      .page-number.active {
         background: var(--main-color);
         color: var(--white);
         border-color: var(--main-color);
      }
   </style>
</head>
<body>
<?php include '../components/admin_header.php'; ?>

<section class="reviews">
   <h1 class="heading">Product Reviews</h1>

   <div class="box-container">
      <table>
         <thead>
            <tr>
               <th>Product</th>
               <th>User</th>
               <th>Rating</th>
               <th>Review</th>
               <th>Date</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $select_reviews = $conn->prepare("SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
               $select_reviews->execute();
               if($select_reviews->rowCount() > 0){
                  while($review = $select_reviews->fetch(PDO::FETCH_ASSOC)){
            ?>
            <tr>
                  <td><?= htmlspecialchars($review['product_name']) ?></td>
                  <td><?= htmlspecialchars($review['user_name']) ?></td>
                  <td>
                     <div class="stars">
                        <?php 
                           $rating = $review['rating'];
                           for($i = 1; $i <= 5; $i++){
                              echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                           }
                        ?>
                     </div>
                  </td>
                  <td>
                     <div class="review-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></div>
                  </td>
                  <td><?= date('M j, Y', strtotime($review['created_at'])) ?></td>
                  <td>
                     <div class="action-btns">
                        <a href="#" onclick="confirmDelete(<?= $review['id'] ?>)" class="btn btn-delete">Delete</a>
                        <?php if($review['approved'] == 0): ?>
                        <a href="#" onclick="approveReview(<?= $review['id'] ?>)" class="btn" style="background: var(--green);">Approve</a>
                        <?php else: ?>
                        <span style="color: var(--green);">
                           <i class="fas fa-check-circle"></i> Approved
                        </span>
                        <?php endif; ?>
                     </div>
                  </td>
               </tr>
            <?php
                  }
               }else{
            ?>
               <tr>
                  <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                     No reviews found.
                  </td>
               </tr>
            <?php
               }
            ?>
            </tbody>
         </table>
      </div>
      
      <!-- Pagination -->
      <div class="pagination">
         <div class="pagination-info">
            <p>Showing <span>1</span> to <span>10</span> of <span>20</span> results</p>
         </div>
         <div class="pagination-nav">
            <a href="#" class="page-nav">
               <i class="fas fa-chevron-left"></i>
            </a>
            <a href="#" class="page-number active">1</a>
            <a href="#" class="page-number">2</a>
            <a href="#" class="page-number">3</a>
            <a href="#" class="page-nav">
               <i class="fas fa-chevron-right"></i>
            </a>
         </div>
         </div>
      </div>
   </div>
</section>

<script src="../js/admin_script.js"></script>
<script>
   function confirmDelete(reviewId) {
      if(confirm('Are you sure you want to delete this review?')) {
         // AJAX call to delete review
         fetch(`delete_review.php?id=${reviewId}`, {
            method: 'DELETE',
         })
         .then(response => response.json())
         .then(data => {
            if(data.success) {
               location.reload();
            } else {
               alert('Error deleting review');
            }
         });
      }
   }
   
   function approveReview(reviewId) {
      // AJAX call to approve review
      fetch(`approve_review.php?id=${reviewId}`, {
         method: 'POST',
      })
      .then(response => response.json())
      .then(data => {
         if(data.success) {
            location.reload();
         } else {
            alert('Error approving review');
         }
      });
   }
</script>
</body>
</html>