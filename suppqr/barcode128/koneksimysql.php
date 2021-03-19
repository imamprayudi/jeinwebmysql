<?php
	include('../../../ADODB/adodb5/adodb.inc.php');
    /* koneksi ke mysql */
    $db = ADONewConnection("mysql");
	$db->Connect('localhost', 'root', 'JvcSql@123', 'ediweb');
    
?>

