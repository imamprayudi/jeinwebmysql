<?php
	/*
	****	create by Imam Prayudi
	****	on JULI 2020
	****    Regularly delete archive Forecast 
        ****    
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
	
	//	delete data
        date_default_timezone_set("Asia/Bangkok");
        $tanggal = date("Y-m-d");
        // print 'tanggal sekarang : ' . $tanggal . '<br />';
        $tgl = date_create($tanggal);
        date_add($tgl,date_interval_create_from_date_string("-270 days"));
        // echo '<br />';
        $tgldulu = date_format($tgl,"Y-m");
        $tgldulu = $tgldulu . '%';
        // print 'tanggal dulu : ' . $tgldulu;
        $sqldel = "delete from forecastndate where transdate like '{$tgldulu}';";
        // echo '<br />';
        // print $sqldel;
        // echo '<br />';
	$rs = $db->Execute($sqldel);
	$rs->Close();	
	$sqldel = "delete from forecastn where transdate like '{$tgldulu}';";
        // print $sqldel;
        $rs = $db->Execute($sqldel);
        $rs->Close();
	//	connection close
	$db->Close();

?>
