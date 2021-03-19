<!DOCTYPE HTML>
<html>
<head>
<title>Material Receive Detail</title>
</head>
<body>

<?php
/*
	 *	create by Imam Prayudi
	 *	on Apr 2020
	 *	Material Receive Detail
	 */  
session_start();
if(isset($_SESSION['usr'])){
}  
else{
  echo "session time out";
  ?> 
  <script> 
    window.location.href = 'index.php';
  </script>
  <?php  
}

include("koneksimysql.php");
if ($_POST){
  $supp = $_POST['suppid'];
  $tgl1  = $_POST['tgl1id'];
  $tgl2  = $_POST['tgl2id'];
  $tgl1 =  date('Y-m-d' , strtotime($tgl1));
  $tgl2 = date('Y-m-d' , strtotime($tgl2));
} 

$rs = $db->Execute("select partno,tipe,tanggal,qty,po,partname from vi07 where ( suppcode = '" . $supp . "') and
  ( tanggal between '" . $tgl1 ."' and '" . $tgl2 . "') and 
  ( tipe = 'U' or tipe = 'P' ) order by tanggal,partno;");
$ada = $rs->RecordCount();
if ($ada == 0){
  echo '<br />Data Nothing ....';
}
else{
  echo '<table id="tblfcl" border="1">';
  echo '<tr>';
  echo '<th>NO</th>';
  echo '<th>Date</th>';
  echo '<th>Part Number</th>';
  echo '<th>Part Name</th>';
  echo '<th>PO Number</th>';
  echo '<th>Receive QTY</th>';
  echo '<th>Trans Code</th>';
  echo '<th>PM</th>';
	echo '</tr>';
  $nomor = 0;
  while (!$rs->EOF){
    $nomor++; 
	  echo '<tr>';
		echo '<td>' . $nomor . '</td>'; 
		$vdate = substr($rs->fields[2],0,10);
    echo '<td>' . $vdate . '</td>';
		echo '<td>' . $rs->fields[0] . '</td>';
    echo '<td>' . $rs->fields[5] . '</td>';
    echo '<td>' . $rs->fields[4] . '</td>';
		$vqty = number_format($rs->fields[3]);
		echo '<td align="right">' . $vqty . '</td>';
		echo '<td align="center">' . $rs->fields[1] . '</td>';
		if ($rs->fields[3] < 0){
		  $cekpm = '-';
		}
		else{
		  $cekpm = '+';
    }			
		echo '<td align="center">' . $cekpm . '</td>';
	  $rs->MoveNext();
  }
	echo '</table>';
}  
?>
</body>
</html>