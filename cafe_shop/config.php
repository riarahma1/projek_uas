<?php
$conn = mysqli_connect('localhost', 'root', '', 'web_store') or die('connection failed');
if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}