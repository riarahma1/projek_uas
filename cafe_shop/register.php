<?php

include 'config.php';

if (isset($_POST['submit'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];

    if ($pass != $cpass) {
        $message[] = 'Kata sandi tidak cocok!';
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "SELECT * FROM user_form WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $message[] = 'Pengguna sudah ada!';
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO user_form (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_pass);
            if (mysqli_stmt_execute($stmt)) {
                $message[] = 'Registrasi berhasil!';
                header('location:login.php');
                exit();
            } else {
                $message[] = 'Kesalahan saat mendaftarkan pengguna: ' . mysqli_error($conn);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
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
    <div class="form-container">
        <form action="" method="post">
            <h3>Daftar</h3>
            <input type="text" name="name" required placeholder="Masukkan nama pengguna" class="box">
            <input type="email" name="email" required placeholder="Masukkan email" class="box">
            <input type="password" name="password" required placeholder="Masukkan kata sandi" class="box">
            <input type="password" name="cpassword" required placeholder="Konfirmasi kata sandi" class="box">
            <input type="submit" name="submit" class="btn" value="Daftar Sekarang">
            <p>Sudah memiliki akun? <a href="login.php">Masuk Sekarang</a></p>
        </form>
    </div>
</body>
</html>