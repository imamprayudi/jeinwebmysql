<!DOCTYPE HTML>
<html>
<head>
<title>Statement Of Account End Term Payment</title>
<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
<link href="../assets/css/default.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
function ShowBps(supp,tgl)
{
  var xmlhttp;    
  if (supp=="")
  {
    document.getElementById("sbps").innerHTML="";
    return;
  }
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      document.getElementById("sbps").innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","jgetsoaend.php?supp="+supp+"&tgl="+tgl,true);
  xmlhttp.send();

}

function setText()
{
  var e = document.getElementById("idsupp");
  var strsupp = e.options[e.selectedIndex].value;
  var t = document.getElementById("idtgl");
  var strtgl = t.options[t.selectedIndex].value;
  document.getElementById("sbps").innerHTML = '<div><p><br /><br />Loading....<img src="../assets/gambar/loading.gif" alt="Loading" height="25" width="25" align="middle" /></p></div>'; 
  ShowBps(strsupp,strtgl);
}

</script>
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

include("koneksimysql.php"); 
$rs = $db->Execute("select * from usersupp where UserId = '" . $myid . "' order by suppname");
include("jmenucss.php");
echo '<br />';
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVC ELECTRONICS INDONESIA ';
echo '<br />';
echo 'STATEMENT OF ACCOUNT - PAYMENT END OF MONTH';
echo '<br /><br />';
echo '<form action="">';
echo 'Supplier : &nbsp;&nbsp;';
echo '<select name="supp" id="idsupp">';

while (!$rs->EOF) 
{
  echo '<option value="' . $rs->fields[1] . '">' . $rs->fields[2] . ' - ' . $rs->fields[1] . '</option>';
  $rs->MoveNext();
}
echo '</select>&nbsp;&nbsp;';
$rs = $db->Execute("select tanggal from soaenddate order by tanggal desc");
echo '<select name="tgl" id="idtgl">';
while (!$rs->EOF)
{
  echo '<option value="' . substr($rs->fields[0],0,10) . '">' . substr($rs->fields[0],0,10) . '</option>';
  $rs->MoveNext();
}
echo '</select>&nbsp;&nbsp;';
echo '<input type=BUTTON value="Display" name="mybtn" id="btn" onClick="setText()">';
echo '</form>';
$rs->Close();
$db->Close();
?>
<br /><br />
<div id="fdata">
</div><div id="sbps">
</div>

</body>
</html>
