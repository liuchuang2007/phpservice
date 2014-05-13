<?php
/**
 *@description: log 
 *@author: liuchuang
 *@Date:2013-08-16
 */
class CatLog {
    private static $file;
    private function getLogFile() {
        if (!CatLog::$file) {
            CatLog::$file = fopen(App::$app->error_log, 'a+');
        }
    }

    public function add($type, $code) {

        $keys = array_keys(App::$app->errors);
        if (in_array($code, $keys)) {
            $msg = App::$app->errors[$code];
            
            $this->getLogFile();
            $str = date('Y-m-d H:i:s').'['.$type.'] '.$msg."\n";
            fputs(CatLog::$file, $str);

        
            //write log into mysql
            /*if (App::$app->log_db['is_open']) {
                $mysql = new MysqlManager(App::$app->log_db);
                $row = array('error_info'=>$type, 'reason'=>$msg,'ctime'=>date('Y-m-d H:i:s'));
                $mysql->autoExecute('error_log',$row, 'INSERT');
            }*/
        }
    }

    public function __destruct() {
        if (CatLog::$file) {
            fclose(CatLog::$file);
        }
    }
}
