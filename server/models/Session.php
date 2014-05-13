<?php
class Session {
    
    /*
     * 获取登陆用户的session信息
     */
    public static function  getSession($userkey) {
        $mem = DataManager::getManager('memcache');
        $val = $mem->get($userkey);
        
        return empty($val) ? '' : unserialize($val);
    }

    /*
     * 设置用户的session信息
     */
    public static function  setSession($userkey, $arr) {
        $mem = DataManager::getManager('memcache');
        $str = serialize($arr);
        
        if ($mem->get($userkey)) {
            return $mem->update($userkey, $str);
        }
        else {
            return $mem->set($userkey, $str);
        }
    }

    /*
     * 更新用户的session信息
     */
    public static function  updateSession($userkey, $arr) {
        $mem = DataManager::getManager('memcache');
        $str = serialize($arr);
        return $mem->update($userkey, $str);
    }

    /*
     * 删除用户的session信息
     */
    public static function  delSession($userkey) {
        $mem = DataManager::getManager('memcache');
        return $mem->delete($userkey);
    }
}