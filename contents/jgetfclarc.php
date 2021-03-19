<!DOCTYPE HTML>
<html>
<head>
<title>Forecast</title>
<style>
table th, td
{
  font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
  border:1px solid grey;
  font-size:12px;
  border-collapse: collapse;
}
td
{
  padding:5px;
  border-collapse: collapse;
}
th
{
  background-color:navy;
  color:white;
  border-collapse: collapse;
}
</style>
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
  $tgl  = $_POST['tanggal'];
  $tglyy = substr($tgl,0,4);
  $tglmm = substr($tgl,5,2);
  $tgldd = substr($tgl,8,2);
  $tg = $tglyy . '-' . $tglmm . '-' . $tgldd;
  $tipe = $_POST['tipe'];

  if ($tipe == 1)
  {
    $tipetext = 'WEEKLY';
	  $r1 = 8;
	  $r2 = 35;
  }
  if ($tipe == 2)
  {
    $tipetext = 'MONTHLY';
	  $r1 = 36;
	  $r2 = 41;
  }
}

$rs = $db->Execute("select * from forecastn where rt = 'H' and suppcode = '" . $supp . "' and transdate = '" . $tg . "'");
$ada = $rs->RecordCount();

if ($ada == 0)
{
  echo 'Data Nothing ....';
}
else
{
  echo '<table id="tblfcl">';
  while (!$rs->EOF)
  {
    echo '<br />';
    echo '<caption>FORECAST FOR ' . $rs->fields[4] . ' (' . $rs->fields[2] . ')' . ' - ' . $rs->fields[0] . ' - ' .$tipetext . '</caption>';
    echo '<tr>';
    echo '<th>NO</th>';
    echo '<th>Part Number</th>';
    echo '<th>DD/MM</th>'; 
    for ($i = $r1; $i <= $r2; $i++)
    {
      echo '<th>' . $rs->fields[$i] . '</th>';
    }
      echo '</tr>';
      $rs->MoveNext();
  }
  $nomor = 0;
  $rs = $db->Execute("select * from forecastn where rt = 'D' and transdate = '" . $tg . "' and suppcode = '" . $supp . "' order by partno");
  while (!$rs->EOF)
  {
    $nomor++;
    echo '<tr>';
    echo '<td>' . $nomor . '</td><td>';
    echo $rs->fields[5] . '<br />';
    echo $rs->fields[6] . '<br />';
    echo $rs->fields[7] . '<br />';
    echo '<td>FIRM<br />FOREC<br />PLAN<br />TOTAL<br /></td>';
    for ($y = $r1; $y <= $r2; $y++)
    {
      echo '<td align="right">' . $rs->fields[$y] . '<br />';
      echo $rs->fields[$y+34] . '<br />';
      echo $rs->fields[$y+34+34] . '<br />';
      echo $rs->fields[$y+34+34+34] . '</td>';
    }
      echo '</tr>';
      $rs->MoveNext();
  }
  echo '</table>';
}
$rs->Close();
$db->Close();
?>
</body>
</html>