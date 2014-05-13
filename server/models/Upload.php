<?php
class Upload {
    private $root_dir;
    private $size; 
    private $dir;
    private $path = '/%year%/%month%/%day%';
    private $path_right = 0777;

    /*
     * 判断文件类型是否合法
     */
    public function formatValid($file) {
        if(!is_array($file)){
            return false;
        }
        else if (empty($this->format)) {
            return true; // 如果不设置类型, 则不检查
        }
        else {
            $torrent = explode(".", $file["name"]);
            $ext = end($torrent);
            $ext = strtolower($ext);

            return in_array($ext, $this->format) ? $file : false;
        }
    }

    /*
     * 判断文件大小是否合法
     */
    public function sizeValid($file) {
        if(!is_array($file)){
          return false;
        }
        elseif ($file['size'] <= $this->maxsize &&  $file['size'] >= $this->minsize) {
            return $file;
        }
        else {
            return false;
        }
    }

    /*
     * 判断目录是否存在
     */
    public function isDirExist() {

        return  is_dir($this->path) ? true : false;
    }

    /*
     * 判断文件是否存在
     */
    public function isFileExist($name) {

            return file_exists($name) ? true : false; 
    }

    /*
     * 创建文件夹路径
     */
    public function newDir($dir)
    {
        if(!is_dir($dir))
        {  
            if(!$this->newDir(dirname($dir))){
                return false;
            }  
            if(!mkdir($dir, $this->path_right, true)){
                return false;
            }
        }

        return true;  

    }
            
    /*
     * 生成文件夹路径
     */
    public function path()
    {
        $path_keys = array(
            '%year%',
            '%month%',
            '%day%');
        $replace_keys = array(
            date("Y"),
            date("m"),
            date("d"));
        for ($i = 0; $i <= 2; $i++)
        {
            $this->path = str_replace($path_keys[$i], $replace_keys[$i], $this->path);
        }
    }
            
    /*
     * 重命名文件名
     */
    private function rename($filename)
    {
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $filename = time().Server::generateStr(4);
        
        return md5($filename) . '.' . $ext;
    }

    public function uploadImage($path, $type, $file, $format = array(), $maxsize, $minsize = 0) {
        $this->root_dir = App::$app->upload_root_path;
        $this->path = $this->root_dir.$path . $this->path;
        $this->format = $format;
        $this->maxsize = $maxsize;
        $this->minsize = $minsize;

        return $this->uploadFile($file, $type);
    }

    /*
     * 保存文件
     */
    public function uploadFile($file, $type) {
        $res = array();
        $url = '';
        if (!$this->formatValid($file) ) {
            $res = App::$app->getErrorDefine('FILE_TYPE_ERR');
        }
        else if (!$this->sizeValid($file)){
            $res = App::$app->getErrorDefine('FILE_SIZE_ERR');
        }
        else {

            $this->path();
            // 如果目录不存在，创建目录
            if ($this->isDirExist($this->path) == false) {

                //文件夹不存在, 生成目录
                $umask = umask(0);
                $this->newDir($this->path);
                umask($umask); 
            }

            // 文件如存在，重新生成文件名
            $filename = basename($file['name']);
            for ($i = 0; $i < 3; $i++) {
            
                $filename = $this->rename($filename);
                $fullname = $this->path . '/' . $filename;
                if (!$this->isFileExist($fullname)) {

                    // 文件不存在, 直接移动文件
                    $umask = umask(0);
                    if (!move_uploaded_file($file['tmp_name'], $fullname)) {
                        $res = App::$app->getErrorDefine('FILE_UPLOAD_FAILED');
                    }
                    break;
                    umask($umask);
                }
            }

            if ($i >= 3) {
                $res = App::$app->getErrorDefine('FILE_UPLOAD_FAILED');
            }
            else {
                $url = str_replace($this->root_dir, '', $fullname);
                if ($type == CHAT_VOICE || $type == CHAT_IMG) {
                    $url = App::$app->resource_domain . $url;
                }
            }
            
        }

        if (empty($res) || empty($res['error'])) {
            $res = array('error'=>0, 'data'=>array('url'=>$url));
        }

        return $res;
    }
}
?>