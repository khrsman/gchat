<?php
    $filename =  $_GET['filename'];
	$uploadpath = 'image/'.$filename;
	move_uploaded_file($_FILES['myimage']['tmp_name'],$uploadpath);
//echo $filename;
?>
