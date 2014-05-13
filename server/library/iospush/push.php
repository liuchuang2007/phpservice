<?php
/*
 * When use this module,please add your own pushCert.pem
 */ 
function push_ios_msg($users, $msg) {
    $body = array('aps' => array('alert' => $msg, 'badge' => 2, 'sound'=>'default'));  //推送方式，包含内容和声音
    $ctx = stream_context_create();
    //如果在Windows的服务器上，寻找pem路径会有问题，路径修改成这样的方法：
    //$pem = dirname(__FILE__) . '/' . 'apns-dev.pem';
    //linux 的服务器直接写pem的路径即可
    $cert_pass = dirname(__FILE__).'/pushCert.pem';
    stream_context_set_option($ctx, "ssl", "local_cert", $cert_pass);
    $pass = "123456";
    stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
    //此处有两个服务器需要选择，如果是开发测试用，选择第二名sandbox的服务器并使用Dev的pem证书，如果是正是发布，使用Product的pem并选用正式的服务器
    //$fp = stream_socket_client("ssl://gateway.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
    $fp = stream_socket_client("ssl://gateway.sandbox.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) {
        //echo "Failed to connect $err $errstrn";
        return false;
    }

    $payload = json_encode($body);
    //echo "sending message :" . $payload ."\n";
    foreach($users as $user) {
        $msg = chr(0) . pack("n",32) . pack("H*", str_replace(' ', '', $user['devicetoken'])) . pack("n",strlen($payload)) . $payload;
        fwrite($fp, $msg);
    }
    fclose($fp);
    return true;
}