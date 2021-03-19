<?php
/*
  Project    : Jeinweb Backup on Cloud
  Modul      : Reply login
  Programmer : Imam Prayudi
  Update     : 11 Desember 2019

*/

// session_start();
include('koneksimysql.php');

  $puserid = 'jein';
  $ppassword = 'J31n&777';
  $rs = $db->Execute("select * from usertbl where UserId = '" . $puserid . "'" . " and UserPass = '" . $ppassword . "'");
  $sukses = $rs->RecordCount();
  if($sukses > 0)
  {
    echo 'data ada';
    /*  input data to log  */
    $insuser = $puserid;
    $qryins = "insert into log1(userid,waktu) values('" . $insuser . "',NOW())";
    $rsins = $db->Execute($qryins);
    $rsins->Close();  
    /* end of input data to log */
  }
  else
  {
    echo 'data tidak ada';
  }

$rs->Close();
$db->Close();

?>