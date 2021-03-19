<?php
$uploads_dir= '\uploadstest';
if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
 // echo  "File ".  $_FILES['userfile']['name']  ." uploaded successfully to";
//$uploads_dir/$dest.\n";
//$dest =  $_FILES['userfile'] ['name'];
	$dest = '/var/www/html/uploads/mail.poc.txt';
if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $dest)) {
       // echo "The file ". basename( $_FILES["userfile"]["name"]). " has been uploaded.";
	   echo "success";
    } else {
        echo "failed";
    }

}
?>


