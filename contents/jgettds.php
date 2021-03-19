<?php
/*
get data Time Delivery Schedule
*/
include("koneksimysql.php");
if (isset($_GET['supp']))
{
  $suppid = $_GET['supp']; 
}

if (isset($_GET['tgl']))
{
  $tgl = $_GET['tgl'];
}

$rs = $db->Execute("select * from tds where (hd = 'H') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "')");
$ada = $rs->RecordCount();
if ($ada == 0)
{
  echo 'Data Nothing ....';
}
else
{
  echo '<table id="tblbps" border="1">';
  while (!$rs->EOF)
  {
    echo '<br />';
    echo '<caption>TIME DELIVERY SCHEDULE FOR ' . $rs->fields[3]  . '</caption>';
    echo '<tr>';
    echo '<th>NO</th>';
    echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PartNumber&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>';
    echo '<th>00/00</th>';
    for ($i = 7; $i <= 38; $i++)
    {
      echo '<th>' . $rs->fields[$i] . '</th>';
    }
      echo '</tr>';
      $rs->MoveNext();
  }
    $nomor = 0;
    $rs = $db->Execute("select * from tds where (hd = 'D') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "') order by partno");
    while (!$rs->EOF)
    {
      $nomor++;
      echo '<tr>';
      echo '<td>' . $nomor . '</td><td>';
      echo $rs->fields[4] . '<br />';
      echo $rs->fields[5] . '<br /></td>';
      for ($y = 6; $y <= 38; $y++)
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