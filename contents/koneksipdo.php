<?php
$servername = "10.230.30.125";
$dbname = "ediweb";
$username = "sa";
$password = "JvcSql@123";

try {
  $conn = new PDO("mysql:host=$servername;connname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $conn->query("select * from usertbl");

  // print_r($conn);
//   echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>