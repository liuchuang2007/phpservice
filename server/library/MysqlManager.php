<?php
/**
 *@name: MysqlManager
 *@description: manipulate mysql
 *@author: liuchuang
 *@Date:2013-08-15
 */
class MysqlManager extends Mysqli{
    private $table_prefix = '';

    public function __construct($conf) {
        parent::__construct($conf['host'],$conf['user'],$conf['pass'],$conf['db']);
        $this->table_prefix = $conf['prefix'];
        if ($this->connect_errno) {
            $this->errorMsg(301);
        }

        parent::query("SET NAMES '{$conf['charset']}'");
    }

    public function table($name) {
        return $this->table_prefix.$name;
    }

    public function query($sql) {
        if ($this->connect_errno) {
            return null;
        }

        $Ret = parent::query($sql);
        if ($this->errno){
            $this->errorMsg(303, $sql);
        }

        return $Ret;
    }


    public function escape_string($unescaped_string) {
        return parent::escape_string($unescaped_string);
    }

    public function close() {
        return parent::close();
    }


    public function getOne($sql, $limited = false) {
        if ($limited == true) {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false) {
            $row = $res->fetch_array();

            if ($row !== false) {
                return $row[0];
            }
            else {
                return '';
            }
        }
        else {
            return false;
        }
    }

    public function getAll($sql) {
        $res = $this->query($sql);
        if ($res !== false) {
            $arr = array();
            while ($row = $res->fetch_assoc()) {
                $arr[] = $row;
            }

            return $arr;
        }
        else {
            return false;
        }
    }

    public function getRow($sql, $limited = false) {
        if ($limited == true) {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false) {
            return $res->fetch_assoc();
        }
        else {
            return false;
        }
    }

    public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '') {
        $field_names = $this->getCol('DESC ' . $table);

        $sql = '';
        if ($mode == 'INSERT') {
            $fields = $values = array();
            foreach ($field_names AS $value) {
                if (array_key_exists($value, $field_values) == true) {
                    $fields[] = $value;
                    $values[] = "'" . $field_values[$value] . "'";
                }
            }

            if (!empty($fields)) {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        }
        else {
            $sets = array();
            foreach ($field_names AS $value) {
                if (array_key_exists($value, $field_values) == true) {
                    $sets[] = $value . " = '" . $field_values[$value] . "'";
                }
            }

            if (!empty($sets)) {
                $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
            }
        }

        if ($sql) {
            return $this->query($sql);
        }
        else {
            return false;
        }
    }

    public function getCol($sql) {
        $res = parent::query($sql);
        if ($res !== false) {
            $arr = array();
            while ($row = $res->fetch_array()) {
                $arr[] = $row[0];
            }

            return $arr;
        }
        else {
            return false;
        }
    }

    public function insert_id() {
        return $this->insert_id;
    }

    private function errorMsg($code, $sql='', $stop=false) {
        App::$app->log->add('Mysql Error['.$sql.']', $code);
        if ($stop)exit;
    }
}

?>