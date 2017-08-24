<?php
//1、载入函数库；2、为了下面调用call函数,获得在显示页面的图片路径
include '../functions.php';
//1、将目标目录作为参数传给call函数；2、为了获得其中的图片路径，为下载使用
$filePath = call('../uploads');
//p($filePath);
//1、载入tpl/show.html模板文件；2、为了显示图片页面
include './tpl/show.html';


