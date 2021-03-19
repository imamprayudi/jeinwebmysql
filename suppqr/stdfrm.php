<html>
<head>
		<title>Standard Packing Data Maintenance - Supplier Select</title>
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
    $vsupp = $_GET['supp'];    
    $vpartno = $_GET['partno'];         
    // tampilkan data lama....    
	$rs = $db->Execute("select partname,stdpack_supp from stdpack where suppcode ='$vsupp' and partnumber = '$vpartno'");
  
while (!$rs->EOF)	
      {      
        $partname = $rs->fields[0];      
        $qty = $rs->fields[1];    
		    // $lokasi = $rs->fields[2];
		    $rs->MoveNext();
      }                
        echo '<br />';
        echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
        echo 'PT.JVC ELECTRONICS INDONESIA ';
        echo '<br />';
        echo 'STANDARD PACKING MAINTENANCE';
        echo '<br /><br />';    
    echo '<form method="post" action="stdedit.php">';    
    echo 'PART NUMBER : ';    
    echo '<input type="text" name="partno" id="partno" value="' . $vpartno . '" readonly="readonly"/>';    
    echo '<br />PART NAME    : ';    
    echo '<input type="text" name="partname" id="partname" value="' . $partname . '" readonly="readonly" /><br />';        
    echo 'QTY : ';    echo '<input type="text" name="qty" id="qty" value="' . $qty . '" />';    
    echo '<br />';    
	echo '<input type="hidden" name="hdnsupp" id="hdnsupp" value="' . $vsupp . '" />';    
    echo '<input type="submit" name="submit" value="UPDATE" />';    
    echo '</form>';  
  }

$rs->Close();

?>
</body>
</html>