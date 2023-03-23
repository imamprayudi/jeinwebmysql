
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
	
include('../contents/koneksimysql.php');
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title> JEIN - Print Barcode Label </title>
		<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
        <script src="../assets/js/jquery.js"></script>
		<link rel="shortcut icon" href= "../assets/gambar/receiving.ico"/>
		<link rel="stylesheet" type="text/css" href="../assets/css/style.css">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	</head>
	
	<body>
<?php
// tampilkan data
include("jmenusuppcss.php");
echo '<div id="section">';
echo '<br />';
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVCKENWOOD ELECTRONICS INDONESIA ';
echo '<br />';
echo 'PRINT BARCODE LABEL';
echo '<br /><br />';
echo '<div id="section">';
echo '<form method="post" action="jmnl_barcode_list.php">';
echo '<table border=0 cellpadding=0 cellspacing=0 width=55%>';
echo '<tr>';
echo '<td width="250px" valign="top">Select Supplier</td>';
echo '<td> <select name="suppcode">';
$rs_cb_suppcode = $db->Execute("select * from usersupp where userid = '" .$session_userid. "' order by suppname asc");
while (!$rs_cb_suppcode->EOF)
{
  echo '<option value="' . $rs_cb_suppcode->fields[1] . '">' . $rs_cb_suppcode->fields[1] . ' - ' . $rs_cb_suppcode->fields[2] . '</option>';
	$rs_cb_suppcode->MoveNext();
}
echo '</select></td>';
echo '</tr>';
echo '<tr><td>&nbsp;</td></tr>';
echo '<tr>';
echo '<td colspan=2>';
echo '<input type="submit" value="Get Part List" id="subpart" name="subpart">';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</form>';
$rs_cb_suppcode->Close(); $db->Close();
?>
			</div>
		</div>		
	</body>
</html>