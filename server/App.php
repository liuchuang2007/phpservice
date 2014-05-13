<?php
/**
 *@description: application entrance,add autoload
 *@author: liuchuang
 *@Date:2013-08-14
 */

$config = require dirname(__FILE__) . '/config/main.php';
class App {
    public static $app;
    public function __construct($config) {
        App::$app = $this;
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
        
        //init CatLog
        //$this->log = new CatLog();

        //init error info
        //$this->pubErr = $this->getPubErrors();
    }

    public static function autoload($class) {

        //include library file
        $cfile = ROOT.'models/'.$class.'.php';
        if (file_exists($cfile)) {
            require_once $cfile;
            return;
        }

        //include controller file
        $cfile = ROOT.'library/'.$class.'.php';
        if (file_exists($cfile)) {
            require_once $cfile;
            return;
        }

        //include xmpplib
        $cfile = ROOT.'library/xmpp/'.$class.'.php';
        if (file_exists($cfile)) {
            require_once $cfile;
            return;
        }
    }

    public static function getErrorDefine($error_name, $extra=array()) {
        $res = array('error'=>App::$app->pubErr[$error_name]['id'], 'data'=>array('reason'=>App::$app->pubErr[$error_name]['desc']));
        if (is_array($extra) && !empty($extra)) {
            $res['data'] = array_merge($res['data'], $extra);
        }
        
        return $res;
    }

    /*
     * Get all error defination
     */
    /*private function getPubErrors() {

        $mysql = DataManager::getManager('mysql');
        $sql = 'SELECT * FROM '.$mysql->table('api_errors');
        $res = $mysql->getAll($sql);
        $result = array();
        foreach($res as $row) {
            $result[$row['error_name']]['id'] = intval($row['error_id']);
            $result[$row['error_name']]['desc'] = $row['error_desc'];
        }

        return $result;
    }*/
}
spl_autoload_register(array('App','autoload'));

new App($config);