<?php
header('Content-Type:text/html; charset=utf-8');
require __DIR__.'/xmlrpc.php';


// 用户登录
$params = array(
    new xmlrpcval(
        array(
            'userkey' => new xmlrpcval('1e4c24f26a53', 'string'),
            'username' => new xmlrpcval('15502112361', 'string'),
            'password' => new xmlrpcval('880212', 'string'),
        ),
        'struct'),
);
$message=new xmlrpcmsg("user.Login", $params);
$client=new xmlrpc_client('/e/user.php', 'api.tojie.com', '80');




$client->setDebug(2);   // 输出调试信息
$res=$client->send($message, 30);
if(!$res->faultCode()){
    $v = $res->value();
    $res = $v->scalarVal();
    var_dump($res['error']->scalarVal());
    var_dump($res['res']->scalarVal());
    var_dump(json_decode($res['res']->scalarVal(), false));
}else{
    echo $res->faultcode().":".$res->faultString()."<br>";
}