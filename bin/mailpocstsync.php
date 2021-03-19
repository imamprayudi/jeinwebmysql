<?php
/*
****	create by Imam Prayudi
****	on June 2020
****	Mailpocst Synchronize
****     
*/  

include('/var/www/html/ADODB/con_ediweb.php');
	
/*	delete data on table mailpostsync     */
$rs = $db->Execute("delete from mailpocstsync;");
$rs->Close();	
	
/*	read text file       */
$fh = fopen('/var/www/html/uploads/mailpocst.txt','r');
echo '<!DOCTYPE html>';
echo '<html>';
echo '<body>';      
while ($line = fgetcsv($fh)) {
  set_time_limit(0);
  $transdate = $line[0];
  echo $transdate;
  echo ',';
  $supplier = $line[1];
  echo $supplier;
  echo ',';
  $status = $line[2];
  echo $status;
  echo ',';
  $confirmation = $line[3];
  echo $confirmation;
  echo ',';
  $confirmdate = $line[4];
  echo $line[4];
  echo ',';
  $rejectreason = $line[5];
  echo $line[5];
  echo ',';
  $updated = $line[6];
  echo $updated;
  echo '<br/>';

  $rs = $db->Execute("insert into mailpocstsync(transdate,supplier, 
    status,confirmation,confirmdate,rejectreason,updated) select
    '{$transdate}','{$supplier}','{$status}','{$confirmation}',
    '{$confirmdate}','{$rejectreason}','{$updated}'");
  $rs->Close();
}

fclose($fh);

$rsupdate = $db->Execute("update mailpocst inner join mailpocstsync on
  (mailpocst.transdate = mailpocstsync.transdate) and
  (mailpocst.supplier = mailpocstsync.supplier)
  set mailpocst.status = mailpocstsync.status,
  mailpocst.confirmation = mailpocstsync.confirmation,
  mailpocst.confirmdate = mailpocstsync.confirmdate,
  mailpocst.rejectreason = mailpocstsync.rejectreason,
  mailpocst.updated = mailpocstsync.updated
  where
  mailpocst.updated < mailpocstsync.updated;");
$rsupdate->Close();

echo 'record has been synchronize...';
echo '</body>';
echo '</html>';
/*  connection close  */
$db->Close();

?>

