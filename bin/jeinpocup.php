<?php
	/*
	****	create by Imam Prayudi
	****	on Mar 2020
	****	remark: 
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
	
	//	delete data
	$rs = $db->Execute("delete from jeinpoctoday;");
	$rs->Close();	
	
	//	read text file
  $fh = fopen('/var/www/html/uploads/jeinpoc.txt','r');
  while ($line = fgets($fh)) {
    set_time_limit(0);
    $pono 		= substr($line,125,7);
    $altno = substr($line,176,8);
    $rs = $db->Execute("insert into jeinpoctoday(pono,altno) select '{$pono}','{$altno}'");
	$rs->Close();
  }
	fclose($fh);
	echo 'Insert jeinpoc from jeinpoc.txt success<br />';
	//	connection close
	$db->Close();
?>
