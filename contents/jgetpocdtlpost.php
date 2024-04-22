

<?php
// include("koneksi.php");
require_once("../connection.php");  
$conf = $_POST['rbnconfirm'];
$reject = $_POST['textreject'];
$supp = $_POST['suppname']; 
$tgl  = $_POST['tglname'];
if($conf=='1')
{
  $confsts = 'confirm';
}
else
{
  $confsts = 'reject';
}

$qry = "update mailpocst set confirmation = '";
$qry = $qry . $confsts;
$qry = $qry . "', confirmdate = GETDATE()";
$qry = $qry . ", rejectreason = '";
$qry = $qry . $reject;
$qry = $qry . "' where (supplier = '";
$qry = $qry . $supp;
$qry = $qry . "') and (transdate='";
$qry = $qry . $tgl;
$qry = $qry . "')";
$rs = $pdo->query($qry)->fetchAll();
if(count($rs) > 0) 
{
  echo "<script>window.close();</script>";
}
// $rs->Close();
?>