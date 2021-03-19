<html>
<head>
		<title>Standard Packing Maintenance -  Part Select</title>
		<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
        <script src="../assets/js/jquery.js"></script>
		<link rel="shortcut icon" href= "../assets/gambar/icons/receiving.ico"/>
		<link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	</head>
<body>
<?php

include("../contents/koneksimysql.php");
include("jmenusuppcss.php");

if(isset($_GET['supp']))

  {
    $supp = $_GET['supp'];
  }


if ($_SERVER['REQUEST_METHOD'] != 'POST')
  {
    // bukan dari posting
  }
else
  {
    $supp = $_POST['supp'];
  }	   
// nampilin Supplier name : 
$rs = $db->Execute("select suppcode,suppname from supplier where suppcode = '$supp'");
while (!$rs->EOF)
   {
      $suppname = $rs->fields[1];
	  $rs->MoveNext();
   }

echo '<br />';
echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
echo 'PT.JVC ELECTRONICS INDONESIA ';
echo '<br />';
echo 'STANDARD PACKING MAINTENANCE';
echo '<br /><br />';
echo 'Supplier name : ' . $suppname . ' - ' . $supp;
echo '&nbsp;&nbsp;';

$rs = $db->Execute("select partnumber,partname,stdpack_supp from stdpack where suppcode = '$supp'");
// $sql = "select * from stdpack where suppcode = '$supp'";
// $result=mssql_query($sql,$con);

// buat table
echo '<table border="1">';
echo '<th>Part.No</th>';
echo '<th>Part Name</th>';
echo '<th>Std.Pack</th>';
echo '<th>EDIT</th>';

while (!$rs->EOF)
  {
    echo '<tr>';
	echo '<td>' . $rs->fields[0] . '</td><td>' . $rs->fields[1] . '</td><td align="right">' . $rs->fields[2] ;
	echo '</td><td><a href="stdfrm.php?supp=' . trim($supp) . '&partno=' . $rs->fields[0] . '">EDIT</a></td>' ;  
    echo '</tr>';
 //    echo $nt[0] . ' - ' . $nt[1] . '<br />';
    $rs->MoveNext();
  } // end of while
 echo '</table>';


$rs->Close();
?>
</body>
</html>

