<?php

include("koneksimysql.php");
if (isset($_GET['supp']))
{
	$suppid = $_GET['supp']; 
}

if (isset($_GET['tgl']))
{
  $tgl = $_GET['tgl'];
}

$rs = $db->Execute("select * from soamid where (hd = 'H') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "')");
$ada = $rs->RecordCount();
if ($ada == 0)
{
  echo 'Data Nothing ....';
}
else
{
	echo '<table id="tblsum">';
	echo '<tr>';
	echo '<th>LAST PAYMENT</th>';
	echo '<th>PURCHASE</th>';
	echo '<th>OUR DN CN</th>';
	echo '<th>NET PURCHASE</th>';
	echo '<th>VAT</th>';
	echo '<th>DN CN (PUR)</th>';
	echo '<th>PAYMENT</th>';
	echo '<th>THIS BALANCE</th>';
	echo '</tr>';
	while (!$rs->EOF)
  {
    echo '<tr>';
	  $lastpay = number_format($rs->fields[16],2,'.','');
		echo '<td align="right">' . $lastpay . '</td>';
		$purchase = number_format($rs->fields[17],2,'.','');
		echo '<td align="right">' . $purchase . '</td>';
		$dncn = number_format($rs->fields[18],2,'.','');
		echo '<td align="right">' . $dncn . '</td>';
		$netpur = number_format($rs->fields[19],2,'.','');
		echo '<td align="right">' . $netpur . '</td>';
		$vat = number_format($rs->fields[20],2,'.','');
		echo '<td align="right">' . $vat . '</td>';
		$dncnpur = number_format($rs->fields[21],2,'.','');
		echo '<td align="right">' . $dncnpur . '</td>';
		$payment = number_format($rs->fields[22],2,'.','');
		echo '<td align="right">' . $payment . '</td>';
		$balance = number_format($rs->fields[23],2,'.','');
		echo '<td align="right">' . $balance . '</td>';
    echo '</tr>';
		echo '</table>';
		echo '<br>Payment Term<br>';
		echo '<table id="tblpay">';
		echo '<tr>';
		echo '<th>15 Days</th>';
		echo '<th>30 Days</th>';
		echo '<th>45 Days</th>';
		echo '<th>60 Days</th>';
		echo '<th>75 Days</th>';
		echo '<th>90 Days</th>';
		echo '<th>TOTAL</th>';
		echo '</tr>';
		echo '<tr>';
		$days15 = number_format($rs->fields[27],2,'.','');
		echo '<td align="right">' . $days15 . '</td>';
		$days30 = number_format($rs->fields[28],2,'.','');
		echo '<td align="right">' . $days30 . '</td>';
		$days45 = number_format($rs->fields[29],2,'.','');
		echo '<td align="right">' . $days45 . '</td>';
		$days60 = number_format($rs->fields[30],2,'.','');
		echo '<td align="right">' . $days60 . '</td>';
		$days75 = number_format($rs->fields[31],2,'.','');
		echo '<td align="right">' . $days75 . '</td>';
		$days90 = number_format($rs->fields[32],2,'.','');
		echo '<td align="right">' . $days90 . '</td>';
		$daystotal = number_format($rs->fields[33],2,'.','');
		echo '<td align="right">' . $daystotal . '</td>';
	  $rs->MoveNext();
  }
	echo '</table>';
	echo '<br />';
	$supp = $suppid * 14102703 ;
	echo '<a target="_blank" href="jsoamiddl.php?sid=' . $supp . '&tglid=' . $tgl . '">DOWNLOAD DATA TO CSV FORMAT</a>';
	echo '<br />';
	echo '<table id="tbldtl">';
	echo '<tr>';
	echo '<th>NO</th>';
	echo '<th>DATE</th>';
	echo '<th>PO NUMBER<br>SO NUMBER</th>';
	echo '<th>SQ</th>';
	echo '<th>INVOICE NUMBER<br>ROG SLIP NO.</th>';
	echo '<th>PARTS NUMBER</th>';
	echo '<th>DESCRIPTION</th>';
	echo '<th>QTY</th>';
	echo '<th>UNIT PRICE</th>';
	echo '<th>AMOUNT</th>';
	echo '<th>OUR DN CN</th>';
	echo '</tr>';
	$nomor = 0;
	$rs = $db->Execute("select * from soamid where (hd = 'D') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "') order by invoice, ok");
	while (!$rs->EOF)
  {
		$nomor++;
		echo '<tr>';
		echo '<td align="right">' . $nomor . '</td>';
		$tgl = substr($rs->fields[6],0,10);
		echo '<td >' . $tgl . '</td>';
		echo '<td>' . $rs->fields[7] . '</td>';
		echo '<td>' . $rs->fields[8] . '</td>';
		echo '<td>' . $rs->fields[9] . '</td>';
		echo '<td>' . $rs->fields[10] . '</td>';
		echo '<td>' . $rs->fields[11] . '</td>';
		echo '<td align="right">' . $rs->fields[12] . '</td>';
		$price =  number_format($rs->fields[13],4,'.','');
		echo '<td align="right">' . $price . '</td>';
		$amount =  number_format($rs->fields[14],2,'.','');
		echo '<td align="right">' . $amount . '</td>';
		$ourdncn =  number_format($rs->fields[15],2,'.','');
		echo '<td align="right">' . $ourdncn . '</td>';
    echo '</tr>';
    $rs->MoveNext();
  }
	echo '</table>';
	$rs->Close();
}
$db->Close();
?>