<!DOCTYPE HTML>
<html>
<head>
<title>DELIVERY INSTRUCTIONS</title>
<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
<script src="../assets/js/jquery.js"></script>

</head>
<body>

<?php	

include("jmenucss.php");
/*
session_start();
if(isset($_SESSION['usr']))
{
  $myid = $_SESSION["usr"];
}  
else
{
	echo "session time out";
	?> 
	<script> 
  	window.location.href = '../index.php';
	</script>
  <?php  
}

include("koneksi.php");
$sql 	= "select * from usertbl where UserId = '" . $myid . "'";
$rs 	= $db->execute($sql);
$hitung = $rs->PO_RecordCount($sql);
$usrlevel = $rs->fields[2];
if($hitung==1)
{
	$_SESSION['dinew_smyid'] = $myid;
	$_SESSION['dinew_suserlevel'] = $usrlevel;
	include("jmenucss.php");
	echo '<br /><br />';
	// echo "<br>DELIVERY INSTRUCTION MENU<br>";
	echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
	echo 'PT.JVC ELECTRONICS INDONESIA ';
	echo '<br />';
	echo 'DELIVERY INSTRUCTIONS';
	echo '<br /><br />';
	echo '<br><a href="jdimaketgl.php">Get Delivery Instructions</a>';
	echo '&nbsp;&nbsp;--> gunakan pilihan ini hanya 1 kali per tanggal Delivery untuk mendapatkan Delivery Instruction ( Jika anda memilih lagi menu ini setelah anda mengedit Delivery Instruction seperti edit invoice namun status data belum upload, maka data akan direset ulang)';
	echo '<br><br><a href="jditgl.php">Edit Delivery Instructions</a>';
	echo '&nbsp;&nbsp;--> gunakan pilihan ini untuk melanjutkan pengeditan';
	echo '<br><br><a href="jdiviewtgl.php">View Delivery Instructions</a>'; 
	$sqlob 	= "select top 1 TransDate from ordbal";
	$rsob 	= $db->execute($sqlob);
	while(!$rsob->EOF)
	{
		echo '<br>';
		$rsob->MoveNext();
	}
		$rsob->Close();
}
else
{
	echo "Wrong Username or Password";
}	
	
$rs->Close();
$db->Close();
*/
?>

<div>
<h1>This services temporary not available...</h1>
</div>
</body>
</html>