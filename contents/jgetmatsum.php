<!DOCTYPE HTML>
<html>
<head>
<title>Material Summary</title>
</head>
<body>

<?php
session_start();
if(isset($_SESSION['usr']))
{
  
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
if ($_POST)
{
  $supp = $_POST['suppid'];
  $period  = $_POST['periodid'];
}

$csupp = 'C' . $supp;
$rs = $db->Execute("select partno,partname, prevblncqty, recqty, shipqty, thisblncqty from sc01 where (loccode = '" . $csupp . "') and (period='" . $period ."') order by partno");
$ada = $rs->RecordCount();
if ($ada == 0)
{
  echo '<br />Data Nothing ....';
}
else
{
  echo '<table id="tblfcl" border="1">';
  echo '<tr>';
  echo '<th>NO</th>';
  echo '<th>Part Number</th>';
  echo '<th>Part Name</th>';
  echo '<th>Previous Month Bal QTY</th>';
  echo '<th>Receive QTY</th>';
  echo '<th>ISSUE QTY</th>';
  echo '<th>This Month Bal QTY</th>';
	echo '</tr>';
  $nomor = 0;
  while (!$rs->EOF)
  {
    $nomor++; 
	  echo '<tr>';
		echo '<td>' . $nomor . '</td>'; 
    echo '<td>' . $rs->fields[0] . '</td>';
    echo '<td>' . $rs->fields[1] . '</td>';
    $f2 = (float)$rs->fields[2];
    $f3 = (float)$rs->fields[3];
    $f4 = (float)$rs->fields[4];
    $f5 = (float)$rs->fields[5];
    echo '<td align="right">' . $f2 . '</td>';
    echo '<td align="right">' . $f3 . '</td>';
    echo '<td align="right">' . $f4 . '</td>';
    echo '<td align="right">' . $f5 . '</td>';
    
    $rs->MoveNext();
  }
	echo '</table>';
}
  
?>
</body>
</html>