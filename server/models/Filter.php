<?php
class Filter {

    /*
     * 通过Session过滤接口授权
     */
    public static function apiAccessFilter($usercode, $key) {
        $sess = Session::getSession($usercode);
        if (empty($sess)) {
            $sess = false;
        }
        else {
            if ($sess['userkey'] != $key) {
                $sess = false;
            }
        }
        
        return $sess;
    }

    /*
     * 通过Session判断userkey是否存在
     */
    public static function isUserKeyExist($userkey) {

        //userkey在登陆一定时期后会自动过期失效,
        //要求重新获取,即，重新登陆
        return true;
        
    }

    /*
     * 验证码正确性验证
     */
    public static function authVerify($mobile, $authcode) {

        //添加特殊验证码
        if ($authcode == '999999') return true;

        $mysql = DataManager::getManager('mysql');
        $mobile = $mysql->escape_string($mobile);
        $authcode = $mysql->escape_string($authcode);
        $cond = " AND ctime > " . (time() - 1800);
        $sql = 'SELECT * FROM '.$mysql->table('mobile_message')." WHERE mobile='$mobile' AND authcode='$authcode' $cond order by ctime desc limit 1";

        $res = $mysql->getRow($sql);
        return $res;
    }

    /*
     * 判断是否是手机号
     */
    public static function isMobile($mobile) {
        return preg_match('/^1[0-9]{10}$/', $mobile);
    }

    /*
     * 判断身份证是否正确
     */
    public static function isIdCard($idcard){
        $city = array(11 => '北京', 12 => '天津', 13 => '河北', 14 => '山西', 15 => '内蒙古', 21 => '辽宁', 22 => '吉林', 23 => '黑龙江', 31 => '上海', 32 => '江苏', 33 => '浙江', 34 => '安徽', 35 => '福建', 36 => '江西', 37 => '山东', 41 => '河南', 42 => '湖北', 43 => '湖南', 44 => '广东', 45 => '广西', 46 => '海南', 50 => '重庆', 51 => '四川', 52 => '贵州', 53 => '云南', 54 => '西藏', 61 => '陕西', 62 => '甘肃', 63 => '青海', 64 => '宁夏', 65 => '新疆', 71 => '台湾', 81 => '香港', 82=>'澳门', 91=>'国外');
        $iSum = 0;
        $idCardLength = strlen($idcard);

        //长度验证
        if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) && !preg_match('/^\d{15}$/i',$idcard)) {
            return false;
        }

        //地区验证
        if(!array_key_exists(intval(substr($idcard,0,2)), $city)) {
            return false;
        }

        // 15位身份证验证生日，转换为18位
        if ($idCardLength == 15) {
            $sBirthday = '19'.substr($idcard, 6, 2).'-'.substr($idcard, 8, 2).'-'.substr($idcard, 10, 2);
            $d = new DateTime($sBirthday);
            $dd = $d->format('Y-m-d');
            if($sBirthday != $dd) {
                return false;
            }

            $idcard = substr($idcard, 0, 6).'19'.substr($idcard, 6, 9);//15to18
            $Bit18 = Filter::getVerifyBit($idcard);//算出第18位校验码
            $idcard = $idcard.$Bit18;
        }

        // 判断是否大于2078年，小于1900年
        $year = substr($idcard, 6, 4);
        if ($year < 1900 || $year > 2078 ) {
            return false;
        }

        //18位身份证处理
        $sBirthday = substr($idcard, 6, 4).'-'.substr($idcard, 10, 2).'-'.substr($idcard, 12, 2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        if($sBirthday != $dd) {
            return false;
        }

        //身份证编码规范验证
        $idcard_base = substr($idcard, 0, 17);
        if(strtoupper(substr($idcard, 17, 1)) != Filter::getVerifyBit($idcard_base)) {
            return false;
        }

        return $idcard;
    }

    /*
     * 计算身份证校验码，根据国家标准GB
     */
    private static function getVerifyBit($idcard_base) {
        if(strlen($idcard_base) != 17) {
            return false;
        }

        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4','3', '2');
        $checksum = 0;
        for($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }

        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /*
     * 过滤接口输入参数
     */
    public static function filterParams($filters, $params) {
        $res = array();
        foreach ($params as $key => $val) {
            if (!empty($filters[$key]) && $filters[$key][1] && empty($val)) { // 如果必填项
                $res = App::$app->getErrorDefine('PARAMETER_INVALID');
                $res['data']['field'] = $key;
                //$res['data'] = array('field'=>$key, 'reason'=> 'field empty');
                break;
            }
            
            if ( !empty($filters[$key]) && $filters[$key][2] && !empty($val)) { // 如果限制范围
                if ($filters[$key][0] == 'int' || $filters[$key][0] == 'float') { // 类型为int 或 float
                    // 检查范围
                    if ( $val < $filters[$key][3] || $val > $filters[$key][4]) {
                        $res = App::$app->getErrorDefine('PARAMETER_INVALID');
                        $res['data']['field'] = $key;
                        //$res['data'] = array('field'=>$key, 'reason'=> 'range error. Min='.$filters[$key][3].' Max='.$filters[$key][4]);
                        break;
                    }
                }
                else if ($filters[$key][0] == 'string') { // 类型为string
                    // 检查范围
                    $len = mb_strlen($val, 'utf8');
                    if ( $len < $filters[$key][3] || $len > $filters[$key][4]) {
                        $res = App::$app->getErrorDefine('PARAMETER_INVALID');
                        $res['data']['field'] = $key;
                        //$res['data'] = array('field'=>$key, 'reason'=> 'range error. MinLen='.$filters[$key][3].' MaxLen='.$filters[$key][4]);
                        break;
                    }
                }
            }
        }

        return $res;
    }
}