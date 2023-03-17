<?php
	/*
	****	create by Mohamad Yunus
	****	on 24 Juli 2017
	****	remark: -
	*/  
	include('/var/www/html//ADODB/con_ediweb.php');
        // ini_set('memory_limit','128M');
	
	//	delete data
	$rs = $db->Execute("delete from stdpack");
	$rs->Close();
	
	//	read csv file
	$feed 		= fopen('/var/www/html/uploads/stdpack.csv', 'r');
	$no			= 1;
	
	while ($i = fgetcsv($feed, 10000, ",")) 
	{
                // set_time_limit(0);
		$suppcode 		= trim($i['0']);
		$partnumber 	= trim($i['1']);
		$partname 		= trim($i['2']);
		$stdpack 		= trim($i['3']);
		$kategori 		= trim($i['4']);
		$replikasi 		= trim($i['5']);
		$lokasi 		= trim($i['6']);
		$stdpack_supp 	= trim($i['7']);
		$input_user 	= trim($i['8']);
		$input_date 	= trim($i['9']);
                $imincl         = trim($i['10']);
		
		//	insert data
		$rs = $db->Execute("insert into stdpack
							select 	'{$replikasi}', '{$suppcode}', '{$partnumber}', '{$partname}', '{$stdpack}', '{$kategori}',  
									'{$lokasi}', '{$stdpack_supp}', '{$input_user}', '{$input_date}', '{$imincl}'");
		$rs->Close();
		
		$no++;
	}
	
	//	connection close
	$db->Close();
?>
