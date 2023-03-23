<!DOCTYPE HTML>
<html>
<head>
<title>Material Received Detail</title>
<style type="text/css">
@import "../assets/css/jquery.datepick.css";
</style>
<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
<script src="../assets/js/jquery.js"></script>
<script type="text/javascript" src="../assets/js/jquery.datepick.js"></script>

</head>
<body>

<?php
session_start();
if(isset($_SESSION['usr']))
{
  $myid = $_SESSION["usr"];
}  
else
{
  echo "session time out";
  ?> 
  <script> 
    window.location.href = '../index.php';
  </script>
  <?php  
}
?>

<script>
$(document).ready(function()
{
  $('#idtgl1').datepick();
  $('#idtgl2').datepick();
  $('form[name=jfForm]').submit(function()
  {
	  $('#fdata').html('<p><br /><br />Loading....<img src="../assets/gambar/loading.gif" alt="Loading" height="25" width="25" align="middle" /></p>');
    $.post('jgetmatrec.php',{suppid: $('[name=supp]').val(), tgl1id: $('[name=tgl1]').val(),tgl2id: $('[name=tgl2]').val()},
    function(data)
    {             
      $('#fdata').html(data).show();
    });
    return false;     
  });
});
</script>

<?php
include("koneksimysql.php"); 
$rs = $db->Execute("select * from usersupp where UserId = '" . $myid . "' order by suppname");
include("jmenucss.php");
echo '<br />';
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVCKENWOOD ELECTRONICS INDONESIA ';
echo '<br />';
echo 'MATERIAL DETAIL RECEIVED STATUS REPORT';
echo '<br /><br />';
echo '<form method="post" action="jgetmatrec.php" name="jfForm">';
echo 'Supplier : ';
echo '<select name="supp" id="idsupp">';
while (!$rs->EOF) 
{
  echo '<option value="' . $rs->fields[1] . '">' . $rs->fields[2] . ' - ' . $rs->fields[1] . '</option>';
  $rs->MoveNext();
}
echo '</select>';
echo '<br /><br />';
echo 'DATE BETWEEN &nbsp;&nbsp; <input type="text" id="idtgl1" name="tgl1" />&nbsp;&nbsp;';
echo 'AND &nbsp;&nbsp;<input type="text" id="idtgl2" name="tgl2" />';
echo '&nbsp;&nbsp;<input type=submit value="Display">';
echo '</form>';
$rs->Close();
$db->Close();
?>
<br/><br />
<div id="fdata">
</div>
<div id="sfcl">
</div>
</body>
</html>
