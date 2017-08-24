<?php
//1、载入函数库；2、为了下面调用上传函数，以及对IS_POST的判断
include './functions.php';
//1、载入上传类；2、为了下面调用上传方法，完成上传操作。
include './Upload.class.php';
if(IS_POST)
{
    //1、实例化上传类；2、为了调用上传方法。
    $obj = new Upload();
    //1、调用up()方法；2、为了获得上传路径，完成上传操作。
    $obj->up();
}
//1、载入tpl/index.html模板文件；2、为了显示上传页面。
include './up/tpl/index.html';
?>

