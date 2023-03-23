<!DOCTYPE HTML>
<html>
<head>
<title>Purchase Order Change Detail</title>
<link href="../assets/css/jein.css" rel="stylesheet" type="text/css" />
<script type = "text/javascript"
	   src = "../assets/js/jquery.js">
	 </script>

<script type="text/javascript" language = "javascript">
  $(document).ready(function()
	{
    $('form[name=jfrm]').submit(function()
    {
      var rbnvalue = $('input[name=rbnconfirm]:checked').val();
      var txtreject = $("#txtreject").val();
      var txtsupp = $("#suppid").val();
      var txttgl  = $("#tglid").val();
      $.post('jgetpocdtlpost.php',
      {
        rbnconfirm:rbnvalue,
        textreject:txtreject,
        suppname:txtsupp,
        tglname:txttgl  
      }, 
      function(data,status)
        {
          $("#hasil").text(data);      
          window.close();
        });
            event.preventDefault();
    });
	});
	 </script>

</head>
<body>

<?php
session_start();
if(isset($_SESSION['usr']))
{
  $myid = $_SESSION["usr"];
  $mysecure = $_SESSION["usrsecure"];
  $mygroup = $_SESSION["usrgroup"];
  $myname = $_SESSION["usrname"];
  $mymail = $_SESSION["usrmail"];
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
echo '<div class="datagrid">';
$supp = $_GET['sid'];
$suppcode = $supp / 14102703 ;
$tgl  = $_GET['tglid'];
$sts = $_GET['sts'];
$conf = $_GET['conf'];
$confdate = $_GET['confdate'];
$qry = "select idno,rdate,rtime,supplier,suppliername,";
$qry = $qry . "actioncode,pono,partno,partname,newqty,"; 
$qry = $qry . "newdate,oldqty,olddate,price,model,potype,altno"; 
$qry = $qry . " from mailpoc where (supplier = '" . $suppcode . "') and (rdate='" . $tgl ."')";
$rs = $db->Execute($qry);
$ada = $rs->RecordCount();
if ($ada == 0)
{
  echo '<br />Data Nothing ....';
}
else
{
	
}
echo '<br />';
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVCKENWOOD ELECTRONICS INDONESIA';
echo '<br />';
echo 'PURCHASE ORDER CHANGE DETAIL';
echo '<hr>';
echo $rs->fields[4] . '&nbsp; - &nbsp;' . $suppcode . '&nbsp;&nbsp;Transmission Date : ' . $tgl . '<br />';
echo 'Status : ' . $sts . ' Confirmation Status : ' . $conf . ' Confirmation Date : '. $confdate;
echo '<br />*** The Purchase Order CHANGE consider accepted if there is no reply within 5 days ***';
echo '<hr><br /><br />';
if ($mysecure == '3')
{
  echo '<form method="POST" action="jgetpocdtlpost.php" id="frmconfirm" name="jfrm">';
  echo '<input type="radio" name="rbnconfirm" value="1" checked="checked" />Confirm<br />' ;
  echo '<input type="radio" name="rbnconfirm" value="2" />Reject<br />' ;
  echo 'Input reject reason : <br />'; 
  echo '<input type="text" id="txtreject" name="textreject">';
  echo '<input type="hidden" id="suppid" name="suppname" value="' . $suppcode . '">';
  echo '<input type="hidden" id="tglid" name="tglname" value="' . $tgl . '">';
  echo '<br /><br />';
  echo '<input type="submit" id="submit" value="submit">';
  echo '</form>';
  $qrysts = "update mailpocst set status = 'read' where (supplier = '" . $suppcode . "') and (transdate='" . $tgl ."')";
  $rsts = $db->Execute($qrysts);
  if($db->affected_rows() > 0) {
    echo '<br />';
  }
  $rsts->Close();
}
else
{
 
}
echo '<div id="hasil"></div>';
echo '<table id="tblpodtl">';
echo '<tr>';
echo '<th>NO.</th>';
echo '<th>TRANSMISSION NO.</th>';
echo '<th>PO STATUS</th>';
echo '<th>PO NUMBER</th>';
echo '<th>PART NUMBER</th>';
echo '<th>PART NAME</th>';
echo '<th>NEW QTY</th>';
echo '<th>NEW DATE</th>';
echo '<th>OLD QTY</th>';
echo '<th>OLD DATE</th>';
echo '<th>PRICE</th>';
echo '<th>MODEL</th>';
echo '<th>PO TYPE</th>';
echo '<th>ALT.NO</th>';
echo '</tr>';
$nomor = 0;
while (!$rs->EOF)
{
  $nomor++; 
	echo '<tr>';
	echo '<td align="right">' . $nomor . '</td>'; 
  echo '<td>' . $rs->fields[0] . '</td>';
  echo '<td>' . $rs->fields[5] . '</td>';
  echo '<td>' . $rs->fields[6] . '</td>';
  echo '<td>' . $rs->fields[7] . '</td>';
  echo '<td>' . $rs->fields[8] . '</td>'; // partname
  echo '<td>' . $rs->fields[9] . '</td>'; // newqty
  $rnewdate = substr($rs->fields[10],0,10);
  echo '<td>' . $rnewdate . '</td>';
  echo '<td>' . $rs->fields[11] . '</td>'; // oldqty
  $rolddate = substr($rs->fields[12],0,10);
  echo '<td>' . $rolddate . '</td>';
  $rprice = number_format($rs->fields[13],4);
  echo '<td align="right">' . $rprice . '</td>';
  echo '<td>' . $rs->fields[14] . '</td>'; // model
  echo '<td>' . $rs->fields[15] . '</td>'; // po type
  echo '<td>' . $rs->fields[16] . '</td>'; // alt.no
	echo '</tr>';
	  $rs->MoveNext();
}	 
echo '</table>';
$rs->Close();
$db->Close();
?>
</div>
</body>
</html>
