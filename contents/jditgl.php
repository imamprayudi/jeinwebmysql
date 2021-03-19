<?php
session_start();
$myusername = $_SESSION['dinew_smyid'];
?>
<html><head>
<TITLE>DELIVERY INSTRUCTIONS - DATE SELECTION</TITLE>
<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
            <script src="../assets/js/jquery.js"></script>
</head><body bgcolor="#ffffff">
<p>
<?php include("jmenudicss.php"); ?>
<h3><IMG src="../assets/gambar/jvclogo.png" align="left" style="max-height:50px; padding:2px;"></h3>
PT.JVC ELECTRONICS INDONESIA
<br>
DELIVERY INSTRUCTIONS - DATE SELECTION
<br>
</p>
<br>
<br>
<?php
include 'koneksi.php';

$sql = "SELECT * FROM usersupp WHERE userid='{$myusername}' order by suppname";
$nt = $db->Execute($sql);

?>
<form action="jdiinv.php" method="post" id=frmdi name=frmdi>
Select Supplier : 
<Select name="supp">
<?php
while(!$nt->EOF)
{
  echo '<option value="'.$nt->fields[1].'">'.$nt->fields[2].'-'.$nt->fields[1].'</option>';
  $nt->MoveNext();
}
$nt->Close();

?>
</Select>
<br><br>
SELECT DATE :
<Select name="didate">

<?php
echo "<option value='01' selected>01</option>";
echo "<option value='02'>02</option>";
echo "<option value='03'>03</option>";
echo "<option value='04'>04</option>";
echo "<option value='05'>05</option>";
echo "<option value='06'>06</option>";
echo "<option value='07'>07</option>";
echo "<option value='08'>08</option>";
echo "<option value='09'>09</option>";
echo "<option value='10'>10</option>";
echo "<option value='11'>11</option>";
echo "<option value='12'>12</option>";
echo "<option value='13'>13</option>";
echo "<option value='14'>14</option>";
echo "<option value='15'>15</option>";
echo "<option value='16'>16</option>";
echo "<option value='17'>17</option>";
echo "<option value='18'>18</option>";
echo "<option value='19'>19</option>";
echo "<option value='20'>20</option>";
echo "<option value='21'>21</option>";
echo "<option value='22'>22</option>";
echo "<option value='23'>23</option>";
echo "<option value='24'>24</option>";
echo "<option value='25'>25</option>";
echo "<option value='26'>26</option>";
echo "<option value='27'>27</option>";
echo "<option value='28'>28</option>";
echo "<option value='29'>29</option>";
echo "<option value='30'>30</option>";
echo "<option value='31'>31</option>";
echo '</select>';

echo 'month : ';
echo '<Select name="dimonth">';
echo "<option value ='01' selected>01</option>";
echo "<option value='02'>02</option>";
echo "<option value='03'>03</option>";
echo "<option value='04'>04</option>";
echo "<option value='05'>05</option>";
echo "<option value='06'>06</option>";
echo "<option value='07'>07</option>";
echo "<option value='08'>08</option>";
echo "<option value='09'>09</option>";
echo "<option value='10'>10</option>";
echo "<option value='11'>11</option>";
echo "<option value='12'>12</option>";
echo '</select>';

echo 'Year : ';
echo '<select name="diyear">';
echo "<option value='2018' selected>2018</option>";
echo "<option value='2019'>2019</option>";
echo '</select>';

$db->Close();
?>	
		
<br><br>
<input type="submit" value="edit" id=subedit name=subedit>
</form>

</body></html>
