<?php
$host = "127.0.0.1";
$dbname = "ediweb";
$dbuser = "root";
$dbpass = "";
$opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$dsn = "mysql:host=$host;dbname=$dbname;";
$pdo = null;
// $db = new PDO($dsn, $dbuser, $dbpass, $opts);
try {
    // buat koneksi dengan database

    $pdo = new PDO($dsn, $dbuser, $dbpass);

    // set error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // tampilkan pesan kesalahan jika koneksi gagal
    print "Koneksi atau query bermasalah: " . $e->getMessage() . "<br/>";
    die();
}

// print_r($pdo);

// $data = $pdo->query('select * from usertbl')->fetchAll();
// echo json_encode($data);


?>