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

$rs = $db->Execute("select * from soa where (hd = 'H') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "')");
$ada = $rs->RecordCount();
if ($ada == 0)
{
	echo 'Data Nothing ....';
}
else
{
	$supp = $suppid * 14102703 ;
	echo '&nbsp;&nbsp;&nbsp;';
	echo '<a target="_blank" href="jsoadl.php?sid=' . $supp . '&tglid=' . $tgl . '">DOWNLOAD DATA TO CSV FORMAT</a>';
	echo '<br /><br />';
	echo '<table id="tblsum">';
	echo '<tr>';
	echo '<th>LAST PAYMENT</th>';
	echo '<th>PURCHASE</th>';
	echo '<th>ROG-C</th>';
	echo '<th>NET PURCHASE</th>';
	echo '<th>VAT</th>';
	echo '<th>DN CN (PUR)</th>';
	echo '<th>PAYMENT</th>';
	echo '<th>THIS BALANCE</th>';
	echo '</tr>';

  while (!$rs->EOF)
	{          
		echo '<tr>';
		echo '<td align="right">' . $rs->fields[16] . '</td>';
		echo '<td align="right">' . $rs->fields[17] . '</td>';
		echo '<td align="right">' . $rs->fields[18] . '</td>';
		echo '<td align="right">' . $rs->fields[19] . '</td>';
		echo '<td align="right">' . $rs->fields[20] . '</td>';
		echo '<td align="right">' . $rs->fields[21] . '</td>';
		echo '<td align="right">' . $rs->fields[22] . '</td>';
		echo '<td align="right">' . $rs->fields[23] . '</td>';
		echo '</tr>';
		$rs->MoveNext();
  }

	echo '</table>';
	echo '<br>';
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
	$rs = $db->Execute("select * from soa where (hd = 'D') and (suppcode = '" . $suppid . "') and (transdate = '" . $tgl . "') order by invoice, ok");
	while (!$rs->EOF)
  {
		$nomor++;
		echo '<tr>';
		echo '<td align="right">' . $nomor . '</td>';
		echo '<td >' . $rs->fields[6] . '</td>';
		echo '<td>' . $rs->fields[7] . '</td>';
		echo '<td>' . $rs->fields[8] . '</td>';
		echo '<td>' . $rs->fields[9] . '</td>';
		echo '<td>' . $rs->fields[10] . '</td>';
		echo '<td>' . $rs->fields[11] . '</td>';
		echo '<td align="right">' . $rs->fields[12] . '</td>';
		echo '<td align="right">' . $rs->fields[13] . '</td>';
		echo '<td align="right">' . $rs->fields[14] . '</td>';
		echo '<td align="right">' . $rs->fields[15] . '</td>';
    echo '</tr>';
    $rs->MoveNext();
  }
	echo '</table>';
	$rs->Close();
}
$db->Close();
?>