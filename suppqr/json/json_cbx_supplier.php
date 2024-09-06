<?php
	include('../../../adodb5/adodb.inc.php');
	include('../../../adodb5/adodb-exceptions.inc.php');
	include('../../../adodb5/adodb-errorpear.inc.php');
	include('../../contents/koneksimysql.php');
	
	
	
	 $supp	= isset($_REQUEST['supp']) ? trim( str_replace("'", "#", $_REQUEST['supp']) ) : "xx";
	$stmt = "select suppcode, suppname from supplier where suppcode = '" . $supp . "' order by suppname";
	$rs 	= $db->Execute($stmt);
  // $rs 	= $db->Execute("select suppcode, suppname from supplier order by suppname");
	$return = array();

	for ($i = 0; !$rs->EOF; $i++) {
		
		$return[$i]['suppcode'] 		= trim($rs->fields['0']);
		$return[$i]['suppname'] 		= trim($rs->fields['1']);
		
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