<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
   header('location:login.php');
};
if (isset($_GET['logout'])) {
   unset($user_id);
   session_destroy();
   header('location:login.php');
};
if (isset($_POST['add_to_cart'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];
   $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
   if (mysqli_num_rows($select_cart) > 0) {
      $message[] = 'Produk sudah ada di keranjang!';
   } else {
      mysqli_query($conn, "INSERT INTO cart(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
      $message[] = 'Produk berhasil ditambahkan ke keranjang!';
   }
};
if (isset($_POST['update_cart'])) {
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($conn, "UPDATE cart SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'Jumlah produk di keranjang berhasil diperbarui!';
}
if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM cart WHERE id = '$remove_id'") or die('query failed');
   header('location:index.php');
}
if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'") or die('query failed');
   header('location:index.php');
}
if (isset($_POST['purchase'])) {
    $user_id = $_SESSION['user_id']; $total_price = $_POST['total_price']; 
    $payment_method = $_POST['payment_method']; $address = $_POST['address'];
    $phone = $_POST['phone']; 
    mysqli_query($conn, "INSERT INTO orders(user_id, total_price, payment_method, address, phone) VALUES('$user_id', '$total_price', '$payment_method', '$address', '$phone')") or die('query failed'); $message[] = 'Pembelian berhasil dilakukan!'; 
}
?>

<!DOCTYPE html>
<html lang="id">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Keranjang Belanja</title>
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
         <div class="user-profile">
            <?php
            $select_user = mysqli_query($conn, "SELECT * FROM user_form WHERE id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($select_user) > 0) {
               $fetch_user = mysqli_fetch_assoc($select_user);
            };
            ?>
            <p> Nama pengguna : <span><?php echo $fetch_user['name']; ?></span> </p>
            <p> Email : <span><?php echo $fetch_user['email']; ?></span> </p>
            <div class="flex">
               <a href="login.php" class="btn">Masuk</a>
               <a href="register.php" class="option-btn">Daftar</a>
               <a href="index.php?logout=<?php echo $user_id; ?>"
               onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="delete-btn">Keluar</a>
            </div>
        </div>
        <div class="products">
            <h1 class="heading">PRODUK KAMI</h1>
            <div class="box-container">
                <?php
            $select_product = mysqli_query($conn, "SELECT * FROM products") or die('query failed');
            if (mysqli_num_rows($select_product) > 0) {
               while ($fetch_product = mysqli_fetch_assoc($select_product)) {
            ?>
                <form method="post" class="box" action="">
                    <img src="images/<?php echo $fetch_product['image']; ?>" alt="">
                    <div class="name"><?php echo $fetch_product['name']; ?></div>
                    <div class="price">Rp<?php echo $fetch_product['price']; ?></div>
                    <input type="number" min="1" name="product_quantity" value="1">
                    <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                    <input type="submit" value="Tambahkan ke Keranjang" name="add_to_cart" class="btn">
                </form>
                <?php
               };
            };
            ?>
            </div>
         </div>
         <div class="shopping-cart">
            <h class="heading">Keranjang Belanja</h>
            <table>
            <thead>
               <th>Gambar</th>
               <th>Nama</th>
               <th>Harga</th>
               <th>Kuantitas</th>
               <th>Total</th>
               <th>Aksi</th>
            </thead>
            <tbody>
               <?php
                  $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'") or die('query failed');
                  $grand_total = 0;
                  if (mysqli_num_rows($cart_query) > 0) {
                     while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
                        ?>
                        <tr>
                           <td><img src="images<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                           <td><?php echo $fetch_cart['name']; ?></td>
                           <td>Rp<?php echo $fetch_cart['price']; ?></td>
                           <td>
                              <form action="" method="post">
                                 <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                                 <input type="number" min="1" name="cart_quantity"
                                 value="<?php echo $fetch_cart['quantity']; ?>">
                                 <input type="submit" name="update_cart" value="Perbarui" class="option-btn">
                              </form>
                           </td>
                           <td>Rp<?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?></td>
                           <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn"
                           onclick="return confirm('Hapus item dari keranjang?');">Hapus</a></td>
                        </tr>
                        <?php
                        $grand_total += $sub_total;
                     }
                  } else {
                     echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">Tidak ada item yang ditambahkan</td></tr>';
                  }
                  ?>
                  <tr class="table-bottom">
                     <td colspan="4">Total keseluruhan :</td>
                     <td>Rp<?php echo $grand_total; ?></td>
                     <td><a href="index.php?delete_all" onclick="return confirm('Hapus semua dari keranjang?');"
                     class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Hapus Semua</a>
                  </td>
               </tr>
            </tbody>
         </table>
         <form action="" method="post"> 
            <input type="hidden" name="total_price" value="<?php echo $grand_total; ?>"> 
            <label for="payment_method">Metode Pembayaran:</label> 
            <select name="payment_method" id="payment_method" required> 
            <option value="credit_card">Kartu Kredit</option> 
            <option value="bank_transfer">Transfer Bank</option> 
            <option value="cash_on_delivery">Bayar di Tempat</option> </select> 
            <label for="address">Alamat Pengiriman:</label> 
            <input type="text" name="address" id="address" required> 
            <label for="phone">Nomor Telepon:</label> 
            <input type="text" name="phone" id="phone" required> 
            <input type="submit" name="purchase" value="Beli" class="btn"> 
         </form>
      </div>
   </div>
</body>
</html>