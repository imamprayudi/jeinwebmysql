<?php
include("../contents/koneksimysql.php");

if ($_SERVER['REQUEST_METHOD'] != 'POST')   
  {    
    // echo 'PROSES BUKAN DARI POSTING';   
  } 
else   
  {     
    $vsupp = $_POST['hdnsupp'];     
    $vpartno = $_POST['partno'];     
    $vqty = $_POST['qty'];    
    // update data     
    $sql = "update stdpack set stdpack_supp = " . $vqty . " where suppcode = '" . $vsupp . "' and partnumber = '" . $vpartno . "'";     
    $rsupd= $db->Execute($sql);
	//echo $sql;     
    if(!$rsupd)       
      {         
        echo "sql error : ";       
      }   
  } //end of else request method post...
// page redirect
//ob_start();
echo '<script language="javascript">location.href="stdpart.php?supp=' . $vsupp . '";</script>'; 
//header("Location:stdlocalpart.php");
//ob_flush();


$rsupd->Close();

?>