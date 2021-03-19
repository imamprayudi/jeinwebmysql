
<?php
session_start();
if(isset($_SESSION['usr']))
{
 
}  
else
{
  echo "session time out";
}

$supp = $_GET['sid'];
$suppcode = $supp / 14102703 ;
$tgl  = $_GET['tglid'];

include('koneksimysql.php');

// ----- remarks for mssql -----
// $sql = "select idno,convert(varchar(10),rdate,23) as rdate,pono,partno,partname,newqty,convert(varchar(10),newdate,23) as newdate, price, model, potype from mailpo where (supplier = '" . $suppcode . "') and (rdate='" . $tgl ."')";
// -----------------------------
$sql = "select idno,rdate,pono,partno,partname,newqty,newdate, price, model, potype from mailpo where (supplier = '" . $suppcode . "') and (rdate='" . $tgl ."')";
$rs 		= $db->Execute($sql);
$fname = "po" . $suppcode . ".csv";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=$fname");
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen("php://output", "w");
$headers = 'TRANSNO, TRANSDATE,PO, PARTNO, PARTNAME, QTY, PODATE, PRICE, MODEL, TYPE' . "\n";
fwrite($fp,$headers);
while(!$rs->EOF)
{
  fputcsv($fp, array(	$rs->fields['0'], $rs->fields['1'], $rs->fields['2'], 
                      $rs->fields['3'], $rs->fields['4'], $rs->fields['5'], 
                      $rs->fields['6'], $rs->fields['7'], $rs->fields['8'], $rs->fields['9']));
  $rs->MoveNext();
}

fclose($fp);
$rs->Close();
$db->Close();
?>