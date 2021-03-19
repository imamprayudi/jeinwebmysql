<?php
/*
  Project    : Jeinweb Backup on Cloud
  Modul      : Reply login
  Programmer : Imam Prayudi
  Update     : 11 Desember 2019

*/

session_start();
include('koneksimysql.php');
if($_POST)
{
  $puserid = $_POST['userid'];
  $ppassword = $_POST['password'];
  $rs = $db->Execute("select * from usertbl where UserId = '" . $puserid . "'" . " and UserPass = '" . $ppassword . "'");
  $sukses = $rs->RecordCount();
  if($sukses > 0)
  {
    $_SESSION['usr'] = $rs->fields[0];
    $_SESSION['usrsecure'] = $rs->fields[2];
    $_SESSION['usrgroup'] = $rs->fields[3];
    $_SESSION['usrname'] = $rs->fields[4];
    $_SESSION['usrmail'] = $rs->fields[5];
    $data['success'] = true;
    $data['message'] = "LOGIN SUCCESSFULL";
    
    /*  input data to log  */
    $insuser = $rs->fields[0];
    $qryins = "insert into log1(userid,waktu) values('" . $insuser . "',NOW())";
    $rsins = $db->Execute($qryins);
    $rsins->Close();  
    /* end of input data to log */
  }
  else
  {
    $data['success'] = false;
    $data['message'] = "ACCESS DENIED";
  }
echo json_encode($data);
$rs->Close();
$db->Close();
}
?>