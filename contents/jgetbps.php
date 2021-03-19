
<?php
/*
get data Big Parts Schedule
*/
include("koneksimysql.php");
session_start();
if(!isset($_SESSION['usr']))
{
  echo "session time out";
  ?> 
  <script> 
    window.location.href = '../index.php';
  </script>
  <?php  
}  

if (isset($_GET['supp']))
{
  $suppid = $_GET['supp']; 
}

if (isset($_GET['tgl']))
{
  $tgl = $_GET['tgl'];
}

$rs = $db->Execute("select * from bps where (hd = 'H') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "')");
$ada = $rs->RecordCount();
if ($ada == 0)
{
  echo 'Data Nothing ....';
}
else
{
  $supp = $suppid * 14102703 ;
  echo '&nbsp;&nbsp;&nbsp;';
  echo '<a target="_blank" href="jbpsdl.php?suppid=' . $supp . "&tgl=" . $tgl . '">DOWNLOAD DATA TO CSV FORMAT</a>';
  echo '<br />';
  echo '<table id="tblbps" border="1">';
    
  while (!$rs->EOF)
  {
    echo '<br />';
    echo '<caption>BIG PART SCHEDULE FOR ' . $rs->fields[3] . '</caption>';
    echo '<tr>';
    echo '<th>NO</th>';
    echo '<th>Part Number</th>';
    echo '<th>00/00</th>';
    for ($i = 7; $i <= 26; $i++)
    {
      echo '<th>' . $rs->fields[$i] . '</th>';
    }
    echo '</tr>';
    $rs->MoveNext();
  }
  $nomor = 0;
  $rs = $db->Execute("select * from bps where (hd = 'D') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "') order by partno");
  while (!$rs->EOF)
  {
    $nomor++;
    echo '<tr>';
    echo '<td>' . $nomor . '</td><td>';
    echo $rs->fields[4] . '<br />';
    echo $rs->fields[5] . '<br /></td>';
    for ($y = 6; $y <= 26; $y++)
    {
      echo '<td align="right">' . $rs->fields[$y] . '</td>';
    }
    echo '</tr>';
    $rs->MoveNext();
  }
    echo '</table>';
}
$rs->Close();
$db->Close();
?>