<?php
	include('../../ADODB/adodb5/adodb.inc.php');
	include_once('../../ADODB/adodb5/toexport.inc.php');
	include('../../ADODB/adodb5/adodb-exceptions.inc.php');
	include('../../ADODB/adodb5/adodb-errorpear.inc.php');
	
	
	/* koneksi ke MSSQL 
	$db = ADONewConnection('odbc_mssql');
	$dsn = "Driver={SQL Server};Server=136.198.117.80\jeinsql2017s;Database=edi;";
	$db->Connect($dsn,'sa','password');
	*/
	
	/* koneksi ke 136.198.117.48 
	$db = ADONewConnection('odbc_mssql');
	$dsn = "Driver={SQL Server};Server=136.198.117.48\JEINSQL2012;Database=edi;";
	$db->Connect($dsn,'sa','password');
    */
    /* koneksi ke mysql */
    $db = ADONewConnection("mysql");
	// $db->Connect('localhost', 'root', 'Git@1410', 'ediweb');
	$db->Connect('10.230.30.125', 'sa', 'JvcSql@123', 'ediweb');
    
?>
