<?php
class Upload{
    private $dir;//指定上传目录
    private $allowSize;//允许上传大小
    private $allowType;//允许上传类型
    public function __construct($dir=null,$allowSize=null,$allowType=null)
    {
        $this->dir = is_null($dir) ? 'uploads' : $dir;
        $this->allowSize = is_null($allowSize) ? 3000000 : $allowSize;
        $this->allowType = is_null($allowType) ? ['jpg','jpeg','png','gif','bmp'] : $allowType;
    }

    /**执行移动上传
     * 与外部执行接口任务的方法
     * @return bool
     */
    public function up(){
        //1、重组数组
        $data = $this->resetArr();
        //2、筛选过滤
        foreach ($data as $k=>$v){
            //$res调用筛选函数的结果
            //若成功，$res=null
            //如果不成功，就返回一个数组
            $res = $this->filter($v);
            //若成功，$res=null，此时bool = false（bool(!$res) = true）
            if ($res){
                return false;
            }
        }
        //3、移动上传
        $this->move($data);
        return true;
    }

    /**重组数组
     * @return array
     */
    private function resetArr(){
        //1、获得键值；2、简化数组，将三维数组简化为二维数组。
        $file = current($_FILES);
        //1、区别对待单文件还是多文件；2、单文件可以直接赋值给$data数组，多文件需要遍历。
        //p($file);
        //1、定义一个空数组$data；2、用来接收遍历后的内容，用来组成新的数组。
        $data = [];
        //1、判断是否是个数组；2、如果是个数组，需要遍历该数组。
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
            //1、说明是单文件；2、如果是单文件就可以直接加到$data数组里。
            $data[] = $file;
        }
        //p($data);
        return $data;
    }

    /**筛选过滤
     * @param $data
     */
    private function filter($v){
            //4.3书写switch中判断上传类型时候新增，获取上传文件的类型
            $type = strtolower(ltrim(strrchr($v['name'],'.'),'.'));
            //4.2
            switch(true){
                case !is_uploaded_file($v['tmp_name']):
                    //echo '不合法上传';
                    //exit;
                    return ['valid'=>'false','msg'=>'不合法上传'];
                    break;
                case $v['error'] == 4:
                    //echo '没有文件上传';
                    //exit;
                    return ['valid'=>'false','msg'=>'没有文件上传'];
                  break;
                case $v['error'] == 3:
                    //echo '只有部分文件上传';
                    //exit;
                    return ['valid'=>'false','msg'=>'只有部分文件上传'];
                  break;
                case $v['error'] == 2:
                    //echo '大小超过了指定值';
                    //exit;
                    return ['valid'=>'false','msg'=>'大小超过了指定值'];
                  break;
                case $v['error'] == 1:
                    //echo '大小超过了限制值';
                    //exit;
                    return ['valid'=>'false','msg'=>'大小超过了限制值'];
                  break;
                case $v['size']>$this->allowSize:
                    //echo '大小超过网站限制值';
                    //exit;
                    return ['valid'=>'false','msg'=>'大小超过网站限制值'];
                   break;
                //写到下面这个case，新增4.3（$type）这一步骤
                case !in_array($type,$this->allowType):
                    //echo '上传类型不允许';
                    //exit;
                    return ['valid'=>'false','msg'=>'上传类型不允许'];
                  break;
            }
        }


    /**移动上传
     * Upload constructor.
     * @param $data
     * @param $path
     */
    private function move($data){
        //1、定义一个接收数组的空数组$filePath；2、用来接收上传图片路径
        $filePath = [];
        //遍历重组数组
        foreach($data as $k=>$v){
            //判断是否是合法文件
            if(is_uploaded_file($v['tmp_name'])){
                //1.上传文件的接收目录
                $dir = $this->dir . '/' . date('Y-m-d');
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
                $filePath[] = $dest;
                //移动路径（把原路径移动到新目标中）
                move_uploaded_file($v['tmp_name'],$dest);
            }
        }
        return $filePath;
    }
}
