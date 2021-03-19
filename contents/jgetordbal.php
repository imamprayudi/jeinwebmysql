<!DOCTYPE HTML>
<html>
<head>
<title>Order Balance</title>
<link href="../assets/css/jein.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
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

include("koneksimysql.php");
echo '<div class="datagrid">';
if ($_POST)
{
  $supp = $_POST['suppid'];
  $urutan  = $_POST['urutanid'];
  if ($urutan == 1)
  {
    $orderby = 'partnumber';
  }
  if ($urutan == 2)
  {
    $orderby = 'reqdate';
  }
	if ($urutan == 3)
  {
    $orderby = 'ponumber';
  }
	if ($urutan == 4)
  {
    $orderby = 'model';
  }
	if ($urutan == 5)
  {
    $orderby = 'issuedate';
  }
	
}
 
$rs = $db->Execute("select * from ordbal where suppcode = '" . $supp . "' order by " . $orderby);
$ada = $rs->RecordCount();
if ($ada == 0)
{
  echo '<br />Data Nothing ....';
}
else
{	
  $supp = $supp * 14102703 ;
  echo '&nbsp;&nbsp;&nbsp;';
  echo '<a target="_blank" href="jordbaldl.php?sid=' . $supp . '">DOWNLOAD DATA TO CSV FORMAT</a>';
  echo '<br /><br />';
  echo '<table id="tblfcl">';
  echo '<tr>';
  echo '<th>NO</th>';
  echo '<th>PART NUMBER</th>';
  echo '<th>PART NAME</th>';
  echo '<th>ORDER <br />QTY</th>';
  echo '<th>REQUIRED <br />DATE</th>';
  echo '<th>PO <br />NUMBER</th>';
  echo '<th>SQ</th>';
  echo '<th>ORDER <br />BALANCE</th>';
  echo '<th>SUPP <br />REST</th>';
  echo '<th>MODEL</th>';
  echo '<th>ISSUE <br />DATE</th>';
  echo '<th>PO <br />TYPE</th>';
	echo '</tr>';
  $nomor = 0;
  while (!$rs->EOF)
  {
    $nomor++; 
	  echo '<tr>';
		echo '<td>' . $nomor . '</td>'; 
    echo '<td>' . $rs->fields[2] . '</td>';
		echo '<td>' . $rs->fields[3] . '</td>';
		echo '<td align="right">' . $rs->fields[4] . '</td>';
		$rdate = substr($rs->fields[5],0,10);
		echo '<td>' . $rdate . '</td>';
		echo '<td>' . $rs->fields[6] . '</td>';
		echo '<td>' . $rs->fields[7] . '</td>';
		echo '<td align="right">' . $rs->fields[8] . '</td>';
		echo '<td align="right">' . $rs->fields[9] . '</td>';
		echo '<td>' . $rs->fields[10] . '</td>';
		$idate = substr($rs->fields[11],0,10);
		echo '<td>' . $idate . '</td>';
		echo '<td>&nbsp;' . $rs->fields[12] . '&nbsp;</td>';
		echo '</tr>';
	  $rs->MoveNext();
  }
	echo '</table>';
}
?>
</div>
</body>
</html>