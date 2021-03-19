<?php
session_start();
if(isset($_SESSION['dinew_smyid']))
{
  $myusername = $_SESSION["dinew_smyid"];
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

?>
<html>
<head>
<TITLE>DELIVERY INSTRUCTIONS - DATE SELECTION</TITLE>
<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
<script src="../assets/js/jquery.js"></script>
</head>
<body bgcolor="#ffffff">
<p>
<?php
include("jmenucss.php");
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVC ELECTRONICS INDONESIA ';
echo '<br />';
echo 'GET DELIVERY INSTRUCTIONS';
echo '<br /><br />';
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
include 'koneksi.php';
$sql	= "SELECT * FROM usersupp WHERE userid='{$myusername}' order by suppname";
$result	= $db->execute($sql);
// pesan error
if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
  $pesan = $_REQUEST['p'];
}
echo "<br>";
echo $pesan;
echo "<br><br>";
?>

<form action="jdimake.php" method="post" id=frmdi name=frmdi>
  Select Supplier : 
	<Select name="supp">
	<?php
	while(!$result->EOF)
	{
		echo '<option value="'.$result->fields[1].'">'.$result->fields[2].'-'.$result->fields[1].'</option>';
		$result->MoveNext();
	}
	$result->Close();
	?>
	</select>
	<br><br>
	SELECT DATE :
	<?php
	echo '<Select name="didate">';
	for($i=1; $i <= 31; $i++)
	{
		if($i == date("d"))
		{
		if($i<=9)
		{
			echo "<option value=$i selected>0$i</option>";
		}
		else
		{
			echo "<option value=$i selected>$i</option>";
		}
		}
		else
		{
		if($i<=9)
		{
			echo "<option value=$i>0$i</option>";
		}
		else
		{
			echo "<option value=$i>$i</option>";
		}
		}
	}
	echo '</select>';
	echo '&nbsp;&nbsp;month : ';
	echo '<Select name="dimonth">';
	for($i=1; $i <= 12; $i++)
	{
		if($i == date("m"))
		{
		if($i<=9)
		{
			echo "<option value=$i selected>0$i</option>";
		}
		else
		{
			echo "<option value=$i selected>$i</option>";
		}
		}
		else
		{
		if($i<=9)
		{
			echo "<option value=$i>0$i</option>";
		}
		else
		{
			echo "<option value=$i>$i</option>";
		}
		}
	}

	echo '</select>';
	echo '&nbsp;&nbsp;year : ';
	echo '<Select name="diyear">';
	echo "<option value ='2013' selected>2013</option>";
	$current = date("Y");
	$nextcurrent = (date("Y")+2);
	for($i=2013; $i <= $nextcurrent; $i++)
	{
		if($i == $current)
		{
		echo "<option value =$i selected>$i</option>";
		}
		else
		{
		echo "<option value =$i>$i</option>";
		}
	}

	echo '</select>';
	echo '&nbsp;&nbsp;&nbsp;sequence : ';
	echo '&nbsp;&nbsp;';
	echo '<Select name="disq">';
	echo "<option value='1' selected>1</option>";
	echo "<option value='2'>2</option>";
	echo "<option value='3'>3</option>";
	echo "<option value='4'>4</option>";
	echo '</select>';
	?>
	<br><br>
	<input type="submit" value="Get DI" id=submit1 name=submit1 />
</form>
<br>
Petunjuk :<br>
pilih sequence = 1 , untuk mendapatkan Delivery Instruction secara kondisi normal<br><br>

Untuk Supplier dengan 1X delivery per hari follow Time Delivery Schedule, pertama pilih sequence 1 untuk mendapatkan delivery instruction dengan quantity sesuai Time Delivery Schedule<br>
Setelah itu update data dengan input invoice,<br>
Sesuaikan quantity dengan actual delivery <br>
Upload data.....<br><br>

Jika pada sequence 1 ada perubahan quantity , maka balance akan muncul pada Delivery Instruction sequence selanjutnya<br>
maka perlu untuk Get Delivery Instruction sequence selanjutnya....<br><br>
Pastikan data sequence 1 telah terupload semua !!! <br>
kembali ke menu Get Delivery Instruction<br>
pilih tanggal yg sama kemudian pilih sequence 2<br>
maka Delivery Instruction akan muncul dengan balance quantity.<br>
update data dengan input invoice dan upload......
<?php	
$db->Close(); 
?>
</body>
</html>