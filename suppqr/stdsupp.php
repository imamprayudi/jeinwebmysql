<?php
session_start();
if(isset($_SESSION['usr']))
{
  $session_userid = $_SESSION['usr'];
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
		<title>Standard Packing Data Maintenance - Supplier Select</title>
		<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
        <script src="../assets/js/jquery.js"></script>
		<link rel="shortcut icon" href= "../assets/gambar/icons/receiving.ico"/>
		<link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	</head>
<body>

<?php
include("../contents/koneksimysql.php");
include("jmenusuppcss.php");
echo '<br />';
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVCKENWOOD ELECTRONICS INDONESIA ';
echo '<br />';
echo 'STANDARD PACKING MAINTENANCE';
echo '<br /><br />';
echo '<form action="stdpart.php" method="post" id="frmpart" name="frmpart">';
echo 'Select Supplier : ';
echo '<select name="supp">';

$rs = $db->Execute("select * from usersupp where userid = '" .$session_userid. "' order by suppname asc");
while (!$rs->EOF) 
  {
    
    echo '<option value="' . $rs->fields[1] . '">' . $rs->fields[1] . '-' . $rs->fields[2] . '</option>';
    $suppname = $rs->fields[1];
	 $rs->MoveNext();
 
  }
echo '</select>';
echo '<br />';
echo '<input type="hidden" value="' . $suppname . '" id="suppname" name="suppname">';
echo '<input type="submit" value="Get Part List" id="subpart" name="subpart">';

echo '</form>';

$rs->Close();


?>
</body>
</html>

