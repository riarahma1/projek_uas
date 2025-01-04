<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
   header('location:login.php');
};
if (isset($_POST['generate_receipt'])) {
   $order_id = $_POST['order_id'];
   $order_date = date('Y-m-d H:i:s');
   $total_price = $_POST['total_price'];

   mysqli_query($conn, "INSERT INTO receipts(user_id, order_id, order_date, total_price) VALUES('$user_id', '$order_id', '$order_date', '$total_price')") or die('query failed');
   $message[] = 'Nota berhasil dibuat!';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Buat Nota</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
      }
   }
   ?>
   <div class="container">
      <div class="receipt-form">
         <h1 class="heading">Buat Nota</h1>
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="total_price" value="<?php echo $grand_total; ?>">
            <label for="order_id">ID Pesanan:</label>
            <input type="text" name="order_id" id="order_id" required>
            <label for="total_price">Total Harga:</label>
            <input type="text" name="total_price" id="total_price" required>
            <input type="submit" name="generate_receipt" value="Buat Nota" class="btn">
         </form>
      </div>
   </div>
</body>
</html>
