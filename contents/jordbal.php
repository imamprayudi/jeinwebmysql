<!DOCTYPE HTML>
<html>
<head>
<title>Order Balance</title>
<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
<script src="../assets/js/jquery.js"></script>
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
   $('form[name=jfForm]').submit(function()
   {
	   $('#fdata').html('<p><br /><br />Loading....<img src="../assets/gambar/loading.gif" alt="Loading" height="25" width="25" align="middle" /></p>');
     $.post('jgetordbal.php',{suppid: $('[name=supp]').val(), urutanid: $('[name=urutan]').val()},
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
echo 'ORDER BALANCE';
echo '<br /><br />';
echo '<form method="post" action="jgetordbal.php" name="jfForm">';
echo 'Supplier : ';
echo '&nbsp;&nbsp;';
echo '<select name="supp" id="idsupp">';

while (!$rs->EOF) 
{
  echo '<option value="' . $rs->fields[1] . '">' . $rs->fields[2] . ' - ' . $rs->fields[1] . '</option>';
  $rs->MoveNext();
}
echo '</select>';
echo '&nbsp;&nbsp;';
echo ' Order By : ';
echo '&nbsp;&nbsp;';
echo '<select name="urutan" id="idurutan">';
echo '<option value="1">Part Number</option>';
echo '<option value="2">Req Date</option>';
echo '<option value="3">PO Number</option>';
echo '<option value="4">Model</option>';
echo '<option value="5">Issue Date</option>';
echo '</select>';
echo '&nbsp;&nbsp;';
echo '<input type=submit value="Display">';
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
