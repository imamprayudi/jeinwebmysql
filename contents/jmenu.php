<!DOCTYPE html>
<html>
<head>
<title>menu</title>
<link href="../assets/css/jein.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
// ------------------------------------------------
// check session
session_start();
if(isset($_SESSION['usr']))
  {
    // echo "session ok";
  }  
  else
  {
    echo "session time out";
?> 
<script> 
   window.location.href = 'index.php';
</script>
   <?php  
 
  }
 // end of check session
 // ---------------------------------------------------
?>
<span class="preload1"></span>
<span class="preload2"></span>
<?php include("jmenucss.php"); ?>
<h1 align="center">PT.JVCKENWOOD ELECTRONICS INDONESIA</h1>
<img border="0" src="../gambar/jvclogo.png" alt="JVCKENWOOD" width="419" height="120" />
</body>
</html>
