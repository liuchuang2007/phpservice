<?php
/**
 *@name: DataManager
 *@description: generate the manager the flag parameter required.
 *@author: liuchuang
 *@Date:2013-08-14
 */
class DataManager {
    private static $mysql;
    private static $mongo;
    private static $memcache;
    public static function getManager($flag) {
        switch($flag) {
        case 'mysql':
            if (!DataManager::$mysql) {
                DataManager::$mysql = new MysqlManager(App::$app->mysql);
            }

            return DataManager::$mysql;
        case 'openfire':

            return new MysqlManager(App::$app->openfire);
        case 'mongo':
            if (!DataManager::$mongo) {
                DataManager::$mongo = new MongoManager(App::$app->mongo);
            }

            return DataManager::$mongo;
        case 'memcache':
            if (!DataManager::$memcache) {
                DataManager::$memcache = new MemcacheManager(App::$app->memcache);
            }

            return DataManager::$memcache;
        }
        return null;
    }
}
