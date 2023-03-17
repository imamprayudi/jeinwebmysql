<?php
	/*
	****	create by Mohamad Yunus
	****	on 20 April 2018
	****

	remark: -
	*/ 

 
	include('/var/www/html/ADODB/con_ediweb.php');
	
	//	delete data
	$rs = $db->Execute("delete from stdpack");
	$rs->Close();
	
	//	read text file

	$fh = fopen('/var/www/html/uploads/stdpack.txt','r');
	while ($line = fgets($fh)) {
		set_time_limit(0);
		$suppcode 		= trim(substr($line,0,7));
		$partnumber 	= trim(substr($line,8,20));
		$partname 		= trim(substr($line,29,30));
		$stdpack 		= trim(substr($line,60,10));
		$kategori 		= trim(substr($line,71,15));
		$replikasi 		= trim(substr($line,87,14));
		$lokasi 		= trim(substr($line,103,15));
		$stdpack_supp 	= trim(substr($line,119,10));
		$input_user 	= trim(substr($line,130,30));
		$input_date 	= trim(substr($line,161,25));
                $imincl         = trim(substr($line,187,1));
         	$rs = $db->Execute("insert into stdpack
							select 	'{$replikasi}', '{$suppcode}', '{$partnumber}', '{$partname}', '{$stdpack}', '{$kategori}',  
									'{$lokasi}', '{$stdpack_supp}', '{$input_user}', '{$input_date}', '{$imincl}'");
		$rs->Close();

/*
		 echo "select 	'{$replikasi}', '{$suppcode}', '{$partnumber}', '{$partname}', '{$stdpack}', '{$kategori}',  
									 '{$lokasi}', '{$stdpack_supp}', '{$input_user}', '{$input_date}', '{$imincl}' ";
		 echo '<br>';

*/

	}
	fclose($fh);
	
	//	connection close

	$db->Close();

?>
