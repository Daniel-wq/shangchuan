<?php
//设置P函数
function p($var){
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
//判断IS_POST
define('IS_POST',$_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
//设置时区
date_default_timezone_set('PRC');

//上传
//执行动作
//目标目录
function up($path='uploads'){
	//1.重组数组
	$data = resetArr();
	//p($data);
	//2.验证判断
    filter($data);
	//3.执行上传
	return upload($data,$path);
}

//重组数组
function resetArr(){
    //获得键值
	$file = current($_FILES);
	//区别对待单文件还是多文件
	//p($file);
	$data = [];
	//判断是否是个数组
	if(is_array($file['name'])){
		//说明是多文件
		foreach($file['name'] as $k=>$v){
			$data[] = [
				'name'=>$v,
				'type'=>$file['type'][$k],
				'tmp_name'=>$file['tmp_name'][$k],
				'error'=>$file['error'][$k],
				'size'=>$file['size'][$k],
			];
		}
	}else{
		//说明是单文件
		$data[] = $file;
	}
	//p($data);
	return $data;
}

//筛选
function filter($data){
    $resultArr = [];
    foreach($data as $k=>$v)
    {
        //4.3书写switch中判断上传类型时候新增，获取上传文件的类型
        $type = strtolower(ltrim(strrchr($v['name'],'.'),'.'));
        //4.2
        switch(true)
        {
            case !is_uploaded_file($v['tmp_name']):
                echo '不合法上传';
                exit;
                break;
            case $v['error'] == 4:
                echo '没有文件上传';
                exit;
                break;
            case $v['error'] == 3:
                echo '只有部分文件上传';
                exit;
                break;
            case $v['error'] == 2:
                echo '大小超过了指定值';
                exit;
                break;
            case $v['error'] == 1:
                echo '大小超过了限制值';
                exit;
                break;
            case $v['size']>3000000:
                echo '大小超过网站限制值';
                exit;
                break;
            //写到下面这个case，新增4.3（$type）这一步骤
            case !in_array($type,['jpg','jpeg','png','gif','bmp']):
                echo '上传类型不允许';
                exit;
                break;
        }
    }
}

//上传文件函数
function upload($data,$path){
    //接收数组的空数组
    $endPath = [];
    //遍历重组数组
    foreach($data as $k=>$v){
        //判断是否是合法文件
        if(is_uploaded_file($v['tmp_name'])){
            //1.上传文件的接收目录
            $dir = $path.'/' . date('Y-m-d');
            //如果目录不存在，就创建一个目录
            is_dir($dir) || mkdir($dir,0777,true);
            //2.截取$v['name']的后缀名-文件类型
            $type = strrchr($v['name'],'.');
            //创建目标目录里的文件名（就是接收过来的名字是什么）
            $fileName =mt_rand(1,9999) . $type;
            //3.完成路径（最终目标目录里的路径）
            $dest = $dir . '/' . $fileName;
            //p($dest);
            //把路径$dest放到$endPath[]里
            $endPath[] = $dest;
            //移动路径（把原路径移动到新目标中）
            move_uploaded_file($v['tmp_name'],$dest);
        }
    }
    return $endPath;
}

//用来在显示页面调用路径的函数
function call($path){
    //定义一个空数组，用来接收遍历的路径
    static $arr = [];
    //获得上传的路径
    $dir = glob($path . '/*');
    //遍历路径
    foreach ($dir as $k=>$v){
        if (is_dir($v)){
            call($v);
        }else{
            $arr[] = $v;
        }
    }
    return $arr;
}
