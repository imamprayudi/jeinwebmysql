<?php
	/*
	****	create by Mohamad Yunus
	****	on 25 Agustus 2017
	****	remark: hasil json ini dikirim ke http://112.78.139.51/edi/
	*/
	
	//CORS 
	header('Access-Control-Allow-Origin: *'); 
	header('Access-Control-Allow-Headers: X-Requested-With'); 

	include('../../adodb5/adodb.inc.php');
	include('../../adodb5/adodb-exceptions.inc.php');
	include('../../adodb5/adodb-errorpear.inc.php');
	
	//	JEINID
	$db  =& ADONewConnection('odbc_mssql');
	$dsn = "Driver={SQL Server};Server=136.198.117.5;Database=edi;";
	$db->Connect($dsn,'sa','password');
	
	//	get paramater
	$userid		= trim(@$_REQUEST["valuserid"]);
	$search		= trim(@$_REQUEST["query"]);
	
	//	execute query
    $sql 	= "select suppcode, suppname from usersupp where userid = '{$userid}' and (suppcode like '%{$search}%' or suppname like '%{$search}%')";
    $rs 	= $db->Execute($sql);
	
	//	array data
	$return = array();
	
	for ($i = 0; !$rs->EOF; $i++) {
		$return[$i]['suppcode'] = $rs->fields[0];
		$return[$i]['suppname'] = $rs->fields[1];
		$rs->MoveNext();
	}
	
	$o = array(
		"success"=>true,
		"rows"=>$return);
		
	echo json_encode($o);
	
	//	connection close
	$rs->Close();
	$db->Close();
?>
