<?php
	/*
	****	create by Imam Prayudi
	****	on April 2020
	****	remark: -
	*/  

	// on linux
	include('/var/www/html/ADODB/con_ediweb.php');
	// ---------

	// delete data
	$rs = $db->Execute("delete from forecastntemp;");
	$rs->Close();	
	
	//	read text file
	$fh = fopen('/var/www/html/uploads/pnb01web.txt','r');

	while ($line = fgets($fh)) {
		set_time_limit(0);
		$transdate 		= substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2);
		$rt 			    = substr($line,10,2);
		$suppcode 		= substr($line,12,5);
		$subsuppcode	= substr($line,17,11);
		$subsuppname	= substr($line,28,40);
		$partno       = substr($line,68,15);
		$partname     = substr($line,83,15);
		$leadtime     = substr($line,98,8);
		$dqty1       	= substr($line,106,9);
		$dqty2       	= substr($line,115,9);
		$dqty3       	= substr($line,124,9);
		$dqty4       	= substr($line,133,9);
		$dqty5       	= substr($line,142,9);
		$dqty6       	= substr($line,151,9);
		$dqty7       	= substr($line,160,9);
		$dqty8       	= substr($line,169,9);
		$dqty9       	= substr($line,178,9);
		$dqty10      	= substr($line,187,9);
		$dqty11      	= substr($line,196,9);
		$dqty12      	= substr($line,205,9);
		$dqty13      	= substr($line,214,9);
		$dqty14      	= substr($line,223,9);
		$dqty15      	= substr($line,232,9);
		$dqty16      	= substr($line,241,9);
		$dqty17      	= substr($line,250,9);
		$dqty18      	= substr($line,259,9);
		$dqty19      	= substr($line,268,9);
		$dqty20      	= substr($line,277,9);
		$dqty21      	= substr($line,286,9);
		$dqty22      	= substr($line,295,9);
		$dqty23      	= substr($line,304,9);
		$dqty24      	= substr($line,313,9);
		$dqty25      	= substr($line,322,9);
		$dqty26      	= substr($line,331,9);
		$dqty27      	= substr($line,340,9);
		$dqty28      	= substr($line,349,9);
		$dqty29      	= substr($line,358,9);
		$dqty30      	= substr($line,367,9);
		$dqty31      	= substr($line,376,9);
		$dqty32      	= substr($line,385,9);
		$dqty33      	= substr($line,394,9);
		$dqty34      	= substr($line,403,9);
		$dt2qt1       = substr($line,412,9);
		$dt2qt2      	= substr($line,421,9);
		$dt2qt3       = substr($line,430,9);
		$dt2qt4       = substr($line,439,9);
		$dt2qt5       = substr($line,448,9);
		$dt2qt6       = substr($line,457,9);
		$dt2qt7       = substr($line,466,9);
		$dt2qt8       = substr($line,475,9);
		$dt2qt9       = substr($line,484,9);
		$dt2qt10      = substr($line,493,9);
		$dt2qt11      = substr($line,502,9);
		$dt2qt12      = substr($line,511,9);
		$dt2qt13      = substr($line,520,9);
		$dt2qt14      = substr($line,529,9);
		$dt2qt15      = substr($line,538,9);
		$dt2qt16      = substr($line,547,9);
		$dt2qt17      = substr($line,556,9);
		$dt2qt18      = substr($line,565,9);
		$dt2qt19      = substr($line,574,9);
		$dt2qt20      = substr($line,583,9);
		$dt2qt21      = substr($line,592,9);
		$dt2qt22      = substr($line,601,9);
		$dt2qt23      = substr($line,610,9);
		$dt2qt24      = substr($line,619,9);
		$dt2qt25      = substr($line,628,9);
		$dt2qt26      = substr($line,637,9);
		$dt2qt27      = substr($line,646,9);
		$dt2qt28      = substr($line,655,9);
		$dt2qt29      = substr($line,664,9);
		$dt2qt30      = substr($line,673,9);
		$dt2qt31      = substr($line,682,9);
		$dt2qt32      = substr($line,691,9);
		$dt2qt33      = substr($line,700,9);
		$dt2qt34      = substr($line,709,9);
		$dt3qt1       = substr($line,718,9);
		$dt3qt2       = substr($line,727,9);
		$dt3qt3       = substr($line,736,9);
		$dt3qt4       = substr($line,745,9);
		$dt3qt5       = substr($line,754,9);
		$dt3qt6       = substr($line,763,9);
		$dt3qt7       = substr($line,772,9);
		$dt3qt8       = substr($line,781,9);
		$dt3qt9       = substr($line,790,9);
		$dt3qt10      = substr($line,799,9);
		$dt3qt11      = substr($line,808,9);
		$dt3qt12      = substr($line,817,9);
		$dt3qt13      = substr($line,826,9);
		$dt3qt14      = substr($line,835,9);
		$dt3qt15      = substr($line,844,9);
		$dt3qt16      = substr($line,853,9);
		$dt3qt17     	= substr($line,862,9);
		$dt3qt18      = substr($line,871,9);
		$dt3qt19      = substr($line,880,9);
		$dt3qt20      = substr($line,889,9);
		$dt3qt21      = substr($line,898,9);
		$dt3qt22      = substr($line,907,9);
		$dt3qt23      = substr($line,916,9);
		$dt3qt24      = substr($line,925,9);
		$dt3qt25      = substr($line,934,9);
		$dt3qt26      = substr($line,943,9);
		$dt3qt27      = substr($line,952,9);
		$dt3qt28      = substr($line,961,9);
		$dt3qt29      = substr($line,970,9);
		$dt3qt30      = substr($line,979,9);
		$dt3qt31      = substr($line,988,9);
		$dt3qt32      = substr($line,997,9);
		$dt3qt33      = substr($line,1006,9);
		$dt3qt34      = substr($line,1015,9);
		$dt4qt1       = substr($line,1024,9);
		$dt4qt2       = substr($line,1033,9);
		$dt4qt3       = substr($line,1042,9);
		$dt4qt4       = substr($line,1051,9);
		$dt4qt5       = substr($line,1060,9);
		$dt4qt6       = substr($line,1069,9);
		$dt4qt7       = substr($line,1078,9);
		$dt4qt8       = substr($line,1087,9);
		$dt4qt9       = substr($line,1096,9);
		$dt4qt10      = substr($line,1105,9);
		$dt4qt11      = substr($line,1114,9);
		$dt4qt12      = substr($line,1123,9);
		$dt4qt13      = substr($line,1132,9);
		$dt4qt14      = substr($line,1141,9);
		$dt4qt15      = substr($line,1150,9);
		$dt4qt16      = substr($line,1159,9);
		$dt4qt17      = substr($line,1168,9);
		$dt4qt18      = substr($line,1177,9);
		$dt4qt19      = substr($line,1186,9);
		$dt4qt20      = substr($line,1195,9);
		$dt4qt21      = substr($line,1204,9);
		$dt4qt22      = substr($line,1213,9);
		$dt4qt23     	= substr($line,1222,9);
		$dt4qt24      = substr($line,1231,9);
		$dt4qt25      = substr($line,1240,9);
		$dt4qt26      = substr($line,1249,9);
		$dt4qt27      = substr($line,1258,9);
		$dt4qt28      = substr($line,1267,9);
		$dt4qt29      = substr($line,1276,9);
		$dt4qt30      = substr($line,1285,9);
		$dt4qt31      = substr($line,1294,9);
		$dt4qt32      = substr($line,1303,9);
		$dt4qt33      = substr($line,1312,9);
		$dt4qt34      = substr($line,1321,9);
		$scold        = substr($line,1330,10);
		$scnew       	= substr($line,1340,44);
		
		//	insert to temp tabel
		$rs = $db->Execute("insert into forecastntemp(	transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
														dqty1, dqty2, dqty3, dqty4, dqty5, dqty6, dqty7, dqty8, dqty9, dqty10, dqty11, dqty12,
														dqty13, dqty14, dqty15, dqty16, dqty17, dqty18, dqty19, dqty20, dqty21, dqty22, dqty23, 
														dqty24, dqty25, dqty26, dqty27, dqty28, dqty29, dqty30, dqty31, dqty32, dqty33, dqty34, 
														dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, dt2qt11, dt2qt12, 
														dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, dt2qt21, dt2qt22, dt2qt23, 
														dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, dt2qt31, dt2qt32, dt2qt33, dt2qt34, 
														dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, dt3qt11, dt3qt12, 
														dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, dt3qt21, dt3qt22, dt3qt23, 
														dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, dt3qt31, dt3qt32, dt3qt33, dt3qt34,
														dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, dt4qt11, dt4qt12, 
														dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, dt4qt21, dt4qt22, dt4qt23, 
														dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, dt4qt31, dt4qt32, dt4qt33, dt4qt34, 
														scold, scnew)
							select	'{$transdate}', '{$rt}', '{$suppcode}', '{$subsuppcode}', '{$subsuppname}', '{$partno}', '{$partname}', '{$leadtime}', 
									'{$dqty1}', '{$dqty2}', '{$dqty3}', '{$dqty4}', '{$dqty5}', '{$dqty6}', '{$dqty7}', '{$dqty8}', '{$dqty9}', '{$dqty10}', '{$dqty11}', '{$dqty12}', 
									'{$dqty13}', '{$dqty14}', '{$dqty15}', '{$dqty16}', '{$dqty17}', '{$dqty18}', '{$dqty19}', '{$dqty20}', '{$dqty21}', '{$dqty22}', '{$dqty23}', 
									'{$dqty24}', '{$dqty25}', '{$dqty26}', '{$dqty27}', '{$dqty28}', '{$dqty29}', '{$dqty30}', '{$dqty31}', '{$dqty32}', '{$dqty33}', '{$dqty34}', 
									'{$dt2qt1}', '{$dt2qt2}', '{$dt2qt3}', '{$dt2qt4}', '{$dt2qt5}', '{$dt2qt6}', '{$dt2qt7}', '{$dt2qt8}', '{$dt2qt9}', '{$dt2qt10}', '{$dt2qt11}', '{$dt2qt12}', 
									'{$dt2qt13}', '{$dt2qt14}', '{$dt2qt15}', '{$dt2qt16}', '{$dt2qt17}', '{$dt2qt18}', '{$dt2qt19}', '{$dt2qt20}', '{$dt2qt21}', '{$dt2qt22}', '{$dt2qt23}', 
									'{$dt2qt24}', '{$dt2qt25}', '{$dt2qt26}', '{$dt2qt27}', '{$dt2qt28}', '{$dt2qt29}', '{$dt2qt30}', '{$dt2qt31}', '{$dt2qt32}', '{$dt2qt33}', '{$dt2qt34}', 
									'{$dt3qt1}', '{$dt3qt2}', '{$dt3qt3}', '{$dt3qt4}', '{$dt3qt5}', '{$dt3qt6}', '{$dt3qt7}', '{$dt3qt8}', '{$dt3qt9}', '{$dt3qt10}', '{$dt3qt11}', '{$dt3qt12}', 
									'{$dt3qt13}', '{$dt3qt14}', '{$dt3qt15}', '{$dt3qt16}', '{$dt3qt17}', '{$dt3qt18}', '{$dt3qt19}', '{$dt3qt20}', '{$dt3qt21}', '{$dt3qt22}', '{$dt3qt23}', 
									'{$dt3qt24}', '{$dt3qt25}', '{$dt3qt26}', '{$dt3qt27}', '{$dt3qt28}', '{$dt3qt29}', '{$dt3qt30}', '{$dt3qt31}', '{$dt3qt32}', '{$dt3qt33}', '{$dt3qt34}', 
									'{$dt4qt1}', '{$dt4qt2}', '{$dt4qt3}', '{$dt4qt4}', '{$dt4qt5}', '{$dt4qt6}', '{$dt4qt7}', '{$dt4qt8}', '{$dt4qt9}', '{$dt4qt10}', '{$dt4qt11}', '{$dt4qt12}', 
									'{$dt4qt13}', '{$dt4qt14}', '{$dt4qt15}', '{$dt4qt16}', '{$dt4qt17}', '{$dt4qt18}', '{$dt4qt19}', '{$dt4qt20}', '{$dt4qt21}', '{$dt4qt22}', '{$dt4qt23}', 
									'{$dt4qt24}', '{$dt4qt25}', '{$dt4qt26}', '{$dt4qt27}', '{$dt4qt28}', '{$dt4qt29}', '{$dt4qt30}', '{$dt4qt31}', '{$dt4qt32}', '{$dt4qt33}', '{$dt4qt34}', 
									'{$scold}', '{$scnew}';");
		$rs->Close();
	}
	fclose($fh);

  // delete non standard date on forecastntemp
	$rsdel = $db->Execute("delete from forecastntemp where transdate like '%EO%';");
	$rsdel->Close();
	echo '<br />delete from Forecastntemp';	

	$rsins = $db->Execute("insert into forecastndate(transdate) select distinct transdate from forecastntemp;");
	$rsins->Close();
	echo '<br />insert into forecastndate';

	$rsdel = $db->Execute("delete from fcl;");
	$rsdel->Close();
	echo '<br />delete from FCL';	

	$rsins = $db->Execute("insert into fcl(	transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
	dqty1, dqty2, dqty3, dqty4, dqty5, dqty6, dqty7, dqty8, dqty9, dqty10, dqty11, dqty12,
	dqty13, dqty14, dqty15, dqty16, dqty17, dqty18, dqty19, dqty20, dqty21, dqty22, dqty23, 
	dqty24, dqty25, dqty26, dqty27, dqty28, dqty29, dqty30, dqty31, dqty32, dqty33, dqty34, 
	dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, dt2qt11, dt2qt12, 
	dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, dt2qt21, dt2qt22, dt2qt23, 
	dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, dt2qt31, dt2qt32, dt2qt33, dt2qt34, 
	dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, dt3qt11, dt3qt12, 
	dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, dt3qt21, dt3qt22, dt3qt23, 
	dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, dt3qt31, dt3qt32, dt3qt33, dt3qt34,
	dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, dt4qt11, dt4qt12, 
	dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, dt4qt21, dt4qt22, dt4qt23, 
	dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, dt4qt31, dt4qt32, dt4qt33, dt4qt34, 
	scold, scnew) select transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
	dqty1, dqty2, dqty3, dqty4, dqty5, dqty6, dqty7, dqty8, dqty9, dqty10, dqty11, dqty12,
	dqty13, dqty14, dqty15, dqty16, dqty17, dqty18, dqty19, dqty20, dqty21, dqty22, dqty23, 
	dqty24, dqty25, dqty26, dqty27, dqty28, dqty29, dqty30, dqty31, dqty32, dqty33, dqty34, 
	dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, dt2qt11, dt2qt12, 
	dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, dt2qt21, dt2qt22, dt2qt23, 
	dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, dt2qt31, dt2qt32, dt2qt33, dt2qt34, 
	dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, dt3qt11, dt3qt12, 
	dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, dt3qt21, dt3qt22, dt3qt23, 
	dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, dt3qt31, dt3qt32, dt3qt33, dt3qt34,
	dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, dt4qt11, dt4qt12, 
	dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, dt4qt21, dt4qt22, dt4qt23, 
	dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, dt4qt31, dt4qt32, dt4qt33, dt4qt34, 
	scold, scnew from forecastntemp;");
	$rsins->Close();
	echo '<br />insert into FCL';
	
	$rsins = $db->Execute("insert into forecastn(	transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
	dqty1, dqty2, dqty3, dqty4, dqty5, dqty6, dqty7, dqty8, dqty9, dqty10, dqty11, dqty12,
	dqty13, dqty14, dqty15, dqty16, dqty17, dqty18, dqty19, dqty20, dqty21, dqty22, dqty23, 
	dqty24, dqty25, dqty26, dqty27, dqty28, dqty29, dqty30, dqty31, dqty32, dqty33, dqty34, 
	dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, dt2qt11, dt2qt12, 
	dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, dt2qt21, dt2qt22, dt2qt23, 
	dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, dt2qt31, dt2qt32, dt2qt33, dt2qt34, 
	dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, dt3qt11, dt3qt12, 
	dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, dt3qt21, dt3qt22, dt3qt23, 
	dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, dt3qt31, dt3qt32, dt3qt33, dt3qt34,
	dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, dt4qt11, dt4qt12, 
	dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, dt4qt21, dt4qt22, dt4qt23, 
	dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, dt4qt31, dt4qt32, dt4qt33, dt4qt34, 
	scold, scnew) select transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
	dqty1, dqty2, dqty3, dqty4, dqty5, dqty6, dqty7, dqty8, dqty9, dqty10, dqty11, dqty12,
	dqty13, dqty14, dqty15, dqty16, dqty17, dqty18, dqty19, dqty20, dqty21, dqty22, dqty23, 
	dqty24, dqty25, dqty26, dqty27, dqty28, dqty29, dqty30, dqty31, dqty32, dqty33, dqty34, 
	dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, dt2qt11, dt2qt12, 
	dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, dt2qt21, dt2qt22, dt2qt23, 
	dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, dt2qt31, dt2qt32, dt2qt33, dt2qt34, 
	dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, dt3qt11, dt3qt12, 
	dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, dt3qt21, dt3qt22, dt3qt23, 
	dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, dt3qt31, dt3qt32, dt3qt33, dt3qt34,
	dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, dt4qt11, dt4qt12, 
	dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, dt4qt21, dt4qt22, dt4qt23, 
	dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, dt4qt31, dt4qt32, dt4qt33, dt4qt34, 
	scold, scnew from forecastntemp;");
	$rsins->Close();
	echo '<br />insert into FORECASTN';
	echo '<br />FINISH ...';

	//	connection close
	$db->Close();
?>
