<?php
require  '../../App.php';
class UserController {
    public static function login($xmlrpcval){
    
        // Get client request params
        $params = array();
        $req = $xmlrpcval->getParam(0);
        $arr = $req->scalarval();
        $params['username'] = empty($arr['username']) ? '' : $arr['username']->scalarval();
        $params['password'] = empty($arr['password']) ? '' : $arr['password']->scalarval();
        $params['regkey'] = empty($arr['userkey']) ? '' : $arr['userkey']->scalarval();
        $params['devicetoken'] = empty($arr['devicetoken']) ? '' : $arr['devicetoken']->scalarval();
        

        // return test
        $res = array('error'=>0, 'data'=>array('result'=>'success'));
        return renderMsg($res['error'], $res['data']);  
    }

}

new xmlrpc_server( 
  array('user.Login'=>array('function'=>'UserController::login'),
));