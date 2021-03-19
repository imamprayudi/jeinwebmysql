
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

include('koneksimysql.php');
$supp = $_GET['suppid'];
$suppcode = $supp / 14102703 ;
$tgl  = $_GET['tgl'];
$sqlh = "select * from bps where ( hd = 'H' ) and (suppcode = '" . $suppcode . "') and (transdate = '" . $tgl . "') LIMIT 1";
$rsh 		= $db->Execute($sqlh);

while (!$rsh->EOF)
{ 
  $headers = 'PARTNO, PARTNAME, ' . $rsh->fields[6] . ',' . $rsh->fields[7] . ',' . $rsh->fields[8] . ',' . $rsh->fields[9];
  $headers = $headers . ',' . $rsh->fields[10] . ',' . $rsh->fields[11] . ',' . $rsh->fields[12] . ',' . $rsh->fields[13] . ',' . $rsh->fields[14];
  $headers = $headers . ',' . $rsh->fields[15] . ',' . $rsh->fields[16] . ',' . $rsh->fields[17] . ',' . $rsh->fields[18] . ',' . $rsh->fields[19];
  $headers = $headers . ',' . $rsh->fields[20] . ',' . $rsh->fields[21] . ',' . $rsh->fields[22] . ',' . $rsh->fields[23] . ',' . $rsh->fields[24];
  $headers = $headers . ',' . $rsh->fields[25] . ',' . $rsh->fields[26] . ',' ; 
  $headers = $headers . "\n";
  $rsh->MoveNext();
}

$sqld = "select  * from bps where ( hd = 'd' ) and (suppcode = '" . $suppcode . "') and (transdate = '" . $tgl . "') order by partno";
$rsd  = $db->Execute($sqld);
$fname = "bps" . $suppcode . ".csv";
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=$fname");
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen("php://output", "w");
fwrite($fp,$headers);

while(!$rsd->EOF)
{  
  fputcsv($fp, array(	$rsd->fields['4'], $rsd->fields['5'], $rsd->fields['6'], $rsd->fields['7'], $rsd->fields['8'],  
                      $rsd->fields['9'], $rsd->fields['10'], $rsd->fields['11'], $rsd->fields['12'], $rsd->fields['13'],  
                      $rsd->fields['14'], $rsd->fields['15'], $rsd->fields['16'], $rsd->fields['17'], $rsd->fields['18'],
                      $rsd->fields['19'], $rsd->fields['20'], $rsd->fields['21'], $rsd->fields['22'], $rsd->fields['23'],
                      $rsd->fields['24'], $rsd->fields['25'], 
                      $rsd->fields['26']));
  $rsd->MoveNext();
}

fclose($fp);
$rsh->Close();
$rsd->Close();
$db->Close();
?>