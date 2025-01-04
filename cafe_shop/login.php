<?php
include 'config.php';
session_start();
if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM user_form WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: index.php');
            exit();
        } else {
            $message[] = 'Kata sandi salah!';
        }
    } else {
        $message[] = 'Email atau kata sandi salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
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
            <h3>Masuk</h3>
            <input type="email" name="email" required placeholder="Masukkan email" class="box">
            <input type="password" name="password" required placeholder="Masukkan kata sandi" class="box">
            <input type="submit" name="submit" class="btn" value="Masuk Sekarang">
            <p>Belum memiliki akun? <a href="register.php">Daftar Sekarang</a></p>
        </form>
    </div>
</body>
</html>