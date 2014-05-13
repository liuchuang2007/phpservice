<?php
/**
 *@name: MemcacheManager
 *@description: manipulate memcache
 *@author: liuchuang
 *@Date:2013-08-14
 */
Class MemcacheManager
{
    private $prefix;
    private $cur_key;
    private $cur_cache;
    private $timeout;
    public function __construct($conf) {
        if(!function_exists('memcache_connect')) {
            $this->errorMsg(101);//Memcache is not currently installed...
        }
        else {
        
            $this->memcache = New Memcache;
            if(!$this->memcache->connect($conf['host'], $conf['port'])) {
                $this->errorMsg(103);//Could not connect to the Memcache host
            }
            $this->prefix = $conf['prefix'];
            $this->timeout = $conf['timeout'];
        }
    }
    
    public function exists($key) {
        if($this->memcache->get($this->prefix . $key)) {
            $this->cur_cache = $this->memcache->get($this->prefix . $key);
            $this->cur_key = $this->prefix . $key;
            return true;
       }
       else {    
            return false; 
       }
       
    }
    
    public function delete($key) {
        if($this->memcache->get($this->prefix . $key)) {
            return $this->memcache->delete($this->prefix . $key);
            
        }
        else {
            return false;
        }
    }

    public function flush() {
        $this->memcache->flush();
    }
    
    public function update($key, $data, $interval=0) {
        $interval = empty($interval) ? $this->timeout : $interval;
        
        if($this->prefix . $this->cur_key) {
            if(!empty($this->cur_cache)) {
                return  $this->memcache->replace($this->cur_key, $data, MEMCACHE_COMPRESSED, $interval);//
            }
        }
        elseif ($this->memcache->get($this->prefix . $key)) {
            return $this->memcache->replace($this->prefix . $key, $data, MEMCACHE_COMPRESSED, $interval);//
        }
        else {
            return false;
        }
    }
    
    public function get($key) {
        if(($this->prefix . $key) == $this->cur_key) {
            return $this->cur_cache;
        }
        else {
            return $this->memcache->get($this->prefix . $key);
        }
    }
    
    public function set($key, $data, $interval=0) {   
        $interval = empty($interval) ? $this->timeout : $interval;
        //echo $data.'---';die;
        return $this->memcache->set($this->prefix . $key, $data, MEMCACHE_COMPRESSED, $interval);//
    }

    private function errorMsg($code) {
        App::$app->log->add('Memcache Error', $code);
        exit;
    }

    public function close() {
        $this->memcache->close();
    }
}
    
?>