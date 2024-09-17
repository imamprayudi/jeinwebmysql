<!DOCTYPE HTML>
<html>
<head>
<title>Get Forecast</title>
<!-- <link href="/css/jein.css" rel="stylesheet" type="text/css>-->
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
<div class='datagrid'>
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
if (isset($_GET['supp']))
 {
   $suppid = $_GET['supp'];
   $tipe = $_GET['tipe'];
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
	  $r2 = 60;
    }
 }

$rs = $db->Execute("select * from fc2y where rt = 'H' and suppcode = '" . $suppid . "'");
$ada = $rs->RecordCount();

if ($ada == 0)
  {
    echo 'Data Nothing ....';
  }
else
  {
    $supp = $suppid * 14102703 ;
    echo '<a target="_blank" href="jgetfc2ydl.php?sid=' . $supp . '">DOWNLOAD DATA TO CSV FORMAT</a>';
    echo '<br /><br />';
    echo '<table id="tblfcl">';
    
    while (!$rs->EOF)
     {
       echo '<caption>FORECAST FOR ' . $rs->fields[4] . ' (' . $rs->fields[2] . ')' . ' - ' . $rs->fields[0] . ' - ' .$tipetext . '</caption>';
       echo '<tr>';
       echo '<th>NO</th>';
       echo '<th style="width:100%">Part Number</th>';
       echo '<th>DD/MM</th>';
       for ($i = $r1; $i <= $r2; $i++)
        {
          echo '<th>' . $rs->fields[$i] . '</th>';
        }
       echo '</tr>';
       $rs->MoveNext();
     }
    $nomor = 0;
    $rs = $db->Execute("select * from fc2y where rt = 'D' and suppcode = '" . $suppid . "' order by partno");
    while (!$rs->EOF)
     {
       $nomor++;
       echo '<tr>';
       echo '<td>' . $nomor . '</td><td style="width:100%">';
       echo $rs->fields[5]  . '<br />';
       echo $rs->fields[6] . '<br />';
       echo $rs->fields[7] . '<br />';
       echo '</td>';
       echo '<td>FIRM<br />FOREC<br />PLAN<br />TOTAL<br /></td>';
       for ($y = $r1; $y <= $r2; $y++)
        {
         echo '<td align="right">' . $rs->fields[$y] . '<br />';
         echo $rs->fields[$y+53] . '<br />';
         echo $rs->fields[$y+53+53] . '<br />';
         echo $rs->fields[$y+53+53+53] . '</td>';
        }
       echo '</tr>';
       $rs->MoveNext();
     }

    echo '</table>';
  }
$rs->Close();
$db->Close();
?>
</div>
</body>
</html>
