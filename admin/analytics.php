<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
}

// Fetch analytics data
$total_sales = 0;
$total_orders = 0;
$sales_per_month = array_fill(1, 12, 0);
$orders_per_month = array_fill(1, 12, 0);
$order_status_counts = ['pending' => 0, 'completed' => 0];
$product_sales = [];

$select_sales = $conn->prepare("SELECT total_price, placed_on, total_products, payment_status FROM orders");
$select_sales->execute();

if ($select_sales->rowCount() > 0) {
   while ($row = $select_sales->fetch(PDO::FETCH_ASSOC)) {
      $month = (int) date('n', strtotime($row['placed_on']));
      $orders_per_month[$month]++;
      $total_orders++;

      if ($row['payment_status'] == 'completed') {
         $total_sales += $row['total_price'];
         $sales_per_month[$month] += $row['total_price'];
      }

      if (isset($order_status_counts[$row['payment_status']])) {
         $order_status_counts[$row['payment_status']]++;
      }

      $products = explode(' - ', $row['total_products']);
      foreach ($products as $prod) {
         if (preg_match('/(.+) \((\d+) x (\d+)\)/', $prod, $matches)) {
            $name = trim($matches[1]);
            $qty = (int) $matches[3];
            if (!isset($product_sales[$name]))
               $product_sales[$name] = 0;
            $product_sales[$name] += $qty;
         }
      }
   }
}

arsort($product_sales);
$top_products = array_slice($product_sales, 0, 5, true);
$monthly_sales_json = json_encode(array_values($sales_per_month));
$monthly_orders_json = json_encode(array_values($orders_per_month));
$status_data_json = json_encode(array_values($order_status_counts));
$status_labels_json = json_encode(array_keys($order_status_counts));
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Analytics Dashboard</title>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <script src="https://cdn.tailwindcss.com"></script>
   <style>
      .dashboard {
         padding: 2rem 9%;
      }

      .dashboard .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
         gap: 1.5rem;
         align-items: flex-start;
      }

      .dashboard .box {
         background-color: var(--white);
         border-radius: .5rem;
         padding: 2rem;
         box-shadow: var(--box-shadow);
         border: var(--border);
         text-align: center;
      }

      .dashboard .box h3 {
         font-size: 2rem;
         color: var(--black);
         margin-bottom: 1.5rem;
      }

      .dashboard .box p {
         font-size: 1.8rem;
         color: var(--light-color);
         margin-bottom: 1.5rem;
      }

      .dashboard .box p span {
         color: var(--main-color);
         font-weight: bold;
      }

      .chart-container {
         position: relative;
         height: 30rem;
         margin: 2rem 0;
      }

      .chart-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 2rem;
      }

      .chart-header h2 {
         font-size: 2rem;
         color: var(--black);
         display: flex;
         align-items: center;
         gap: 1rem;
      }

      .chart-header h2 i {
         color: var(--orange);
      }

      .chart-header select {
         padding: .5rem 1rem;
         border: var(--border);
         border-radius: .5rem;
         font-size: 1.4rem;
         color: var(--black);
         background-color: var(--light-bg);
         cursor: pointer;
      }
   </style>
</head>

<body>
   <?php include '../components/admin_header.php'; ?>

   <section class="dashboard">
      <h1 class="heading">Analytics Dashboard</h1>

      <div class="box-container">
         <!-- Sales Chart -->
         <div class="box">
            <div class="chart-header">
               <h2><i class="fas fa-chart-bar"></i> Monthly Sales</h2>
               <select>
                  <option>2024</option>
                  <option selected>2025</option>
               </select>
            </div>
            <div class="chart-container">
               <canvas id="salesChart"></canvas>
            </div>
         </div>

         <!-- Orders Chart -->
         <div class="box">
            <div class="chart-header">
               <h2><i class="fas fa-chart-line"></i> Monthly Orders</h2>
               <select>
                  <option>2024</option>
                  <option selected>2025</option>
               </select>
            </div>
            <div class="chart-container">
               <canvas id="ordersChart"></canvas>
            </div>
         </div>

         <!-- Status Distribution -->
         <div class="box">
            <h2><i class="fas fa-pie-chart"></i> Order Status</h2>
            <div class="chart-container">
               <i class="fas fa-pie-chart text-green-500"></i>
               Order Status
            </h2>
            <div class="h-64">
               <canvas id="statusChart"></canvas>
            </div>
         </div>

         
      </div>
      <!-- Top Products -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
               <i class="fas fa-star text-yellow-500"></i>
               Top Selling Products
            </h2>
            <div class="space-y-4">
               <?php foreach ($top_products as $prod => $qty): ?>
                  <div class="flex items-center justify-between">
                     <div class="flex items-center gap-3">
                        <div class="bg-gray-100 p-2 rounded-lg">
                           <i class="fas fa-box text-gray-500"></i>
                        </div>
                        <span class="font-medium text-gray-700"><?= htmlspecialchars($prod) ?></span>
                     </div>
                     <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?= number_format($qty) ?> sold
                     </span>
                  </div>
               <?php endforeach; ?>
            </div>
         </div>
   </div>

   <script>
      // Sales Chart
      const salesChart = new Chart(
         document.getElementById('salesChart'),
         {
            type: 'bar',
            data: {
               labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
               datasets: [{
                  label: 'Sales (NRS)',
                  data: <?= $monthly_sales_json ?>,
                  backgroundColor: 'rgba(67, 97, 238, 0.2)',
                  borderColor: 'rgba(67, 97, 238, 1)',
                  borderWidth: 1,
                  borderRadius: 4
               }]
            },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: { display: false },
                  tooltip: {
                     callbacks: {
                        label: function (context) {
                           return 'NRS ' + context.raw.toLocaleString() + ' /-';
                        }
                     }
                  }
               },
               scales: {
                  y: {
                     beginAtZero: true,
                     ticks: {
                        callback: function (value) {
                           return 'NRS ' + value.toLocaleString() + ' /-';
                        }
                     }
                  }
               }
            }
         }
      );

      // Orders Chart
      const ordersChart = new Chart(
         document.getElementById('ordersChart'),
         {
            type: 'line',
            data: {
               labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
               datasets: [{
                  label: 'Orders',
                  data: <?= $monthly_orders_json ?>,
                  backgroundColor: 'rgba(124, 58, 237, 0.1)',
                  borderColor: 'rgba(124, 58, 237, 1)',
                  borderWidth: 2,
                  tension: 0.3,
                  fill: true,
                  pointBackgroundColor: 'rgba(124, 58, 237, 1)',
                  pointRadius: 4
               }]
            },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: { display: false }
               },
               scales: {
                  y: {
                     beginAtZero: true,
                     ticks: {
                        precision: 0
                     }
                  }
               }
            }
         }
      );

      // Status Chart
      const statusChart = new Chart(
         document.getElementById('statusChart'),
         {
            type: 'doughnut',
            data: {
               labels: <?= $status_labels_json ?>,
               datasets: [{
                  data: <?= $status_data_json ?>,
                  backgroundColor: [
                     'rgba(234, 179, 8, 0.7)',
                     'rgba(16, 185, 129, 0.7)'
                  ],
                  borderColor: [
                     'rgba(234, 179, 8, 1)',
                     'rgba(16, 185, 129, 1)'
                  ],
                  borderWidth: 1
               }]
            },
            options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: {
                     position: 'bottom',
                     labels: {
                        usePointStyle: true,
                        padding: 20
                     }
                  },
                  tooltip: {
                     callbacks: {
                        label: function (context) {
                           const total = context.dataset.data.reduce((a, b) => a + b, 0);
                           const value = context.raw;
                           const percentage = Math.round((value / total) * 100);
                           return `${context.label}: ${value} (${percentage}%)`;
                        }
                     }
                  }
               },
               cutout: '70%'
            }
         }
      );
   </script>

   <script src="../js/admin_script.js"></script>
</body>

</html>