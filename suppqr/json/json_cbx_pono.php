<?php
include('../../../ADODB/adodb5/adodb.inc.php');
	include_once('../../../ADODB/adodb5/toexport.inc.php');
	include('../../../ADODB/adodb5/adodb-exceptions.inc.php');
	include('../../../ADODB/adodb5/adodb-errorpear.inc.php');
	include('../../contents/koneksimysql.php');
	
	
	
	$suppcode	= isset($_REQUEST['suppcode']) ? trim( str_replace("'", "#", $_REQUEST['suppcode']) ) : "";
	$partnumber	= isset($_REQUEST['partno']) ? trim( str_replace("'", "#", $_REQUEST['partno']) ) : "";
	$ponumber	= isset($_REQUEST['pono']) ? trim( str_replace("'", "#", $_REQUEST['pono']) ) : "";
	
	$rs = $db->Execute("select ponumber from ordbal 
						where 	suppcode 	= '".$suppcode."' and 
								partnumber 	= '".$partnumber."' and 
								ponumber 	like '%".$ponumber."%'
						order by ponumber asc");
	$return = array();

	for ($i = 0; !$rs->EOF; $i++) {
		
		$return[$i]['pono'] 		= trim($rs->fields['0']);
		
		$rs->MoveNext();
	}
	
	$o = array(
		"success"=>true,
		"rows"=>$return);
	
	echo json_encode($o);
		
	
	// Closing Database Connection
	$rs->Close();
	$db->Close();
?>