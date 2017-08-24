<?php
$file =$_GET['down'];
//var_dump($file);
//echo "<img src='$file'>";
//die();
header("Content-type:application/octet-stream");//二进制文件
$fileName = basename($file);//获取文件名
header("Content-Disposition:attachment;filename={$fileName}");//下载窗口
header("Accept-ranges:bytes");//文件单位
header("Accept-length:".filesize($file));//文件大小
readfile($file);
