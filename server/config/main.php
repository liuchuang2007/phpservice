<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('APP_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'App'.DIRECTORY_SEPARATOR);
//require ROOT.'library/xml/xmlrpc.php';
require ROOT.'library/xml/xmlrpcs.php';

return array(
    'errors'    => require (dirname(__FILE__).DIRECTORY_SEPARATOR.'error_def.php'),//系统错误定义
    'error_log' => ROOT.'error.log',

    //上传文件路径
    'upload_root_path'  => '',
    'user_img_path'     => '/userimg',
    'cert_img_path'     => '/certimg',
    'mission_img_path'  => '/mimg',
    'mission_voice_path' => '/mvoice',
    'chat_voice_path'    => '/chatvoice',
    'chat_img_path'      => '/chatimg',
    'sys_img_path'       => '/sys',

    
    //mongo配置
    'mongo' => array(
        'host'      => '172.168.1.14',
        'port'      => 27017,
        'db'        => 'test',
        'user'      => 'admin',
        'pass'      => 'admin',
        'persist'   => false,
        'persist_key' => 'ci_mongo_persist'
    ),

    //memcache配置
    'memcache' => array(
        'host' => '127.0.0.1',
        'port' => '11211',
        'timeout' => 86400,
        'prefix'  => ''
    ),

    //mysql配置
    'mysql' => array(
        'host'  => '127.0.0.1',
        'port'  => 3306,
        'user'  => 'root',
        'pass'  => '123',
        'db'    => 'tuzi',
        'prefix'  => 'tuzi_',
        'charset' => 'utf8'
    ),


    //openfire
    'openfire' => array(
        'host'  => '127.0.0.1',
        'port'  => 3306,
        'user'  => 'root',
        'pass'  => '123',
        'db'    => 'openfire',
        'prefix'  => '',
        'charset' => 'utf8'
    ),

    // xmpp system notification
    'send' => array(
        'host' => '172.168.1.14',
        'port' => 5222,
        'user' => 'liu',
        'pass' => '123',
        'xm'   => 'xmpp'
    ),
);
?>