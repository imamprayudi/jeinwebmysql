<?php
$target_dir = "/var/www/html/uploads/";
// $target_file = $target_dir . basename($_FILE["fileToUpload"]["name"]);
$target_file = $target_dir . "pnb01web.txt";
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
echo "upload : ";
echo $uploadOk;

if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) 
  {
     echo "the file " . basename($_FILE["userfile"]["name"]). " uploaded";
  }
   else
  {
    echo "error...";
    print_r(error_get_last());
  }


?>


