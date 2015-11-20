<?php
/**
 * function
 * author : Aruze Gaming America, Inc.
 */

/**
 * extract controller and action name from REQUEST_URI
 * @param string &$_controller : controller name
 * @param string &$_action : action name
 */
function get_action(&$_controller, &$_action)
{
    $host = filter_input(INPUT_SERVER, "HTTP_HOST");
    $uri = filter_input(INPUT_SERVER, "REQUEST_URI");
    $request = 'http'
            . (IS_TEST
                ? (filter_input(INPUT_SERVER, "HTTPS") ? 's' : '')
                : 's') . '://' . $host . $uri;

    $tmp = str_replace(BASE_URL, '', $request);
    $tmp = explode('/', $tmp);

    $str1 = gv(1, $tmp);
    $_controller = $str1 ? strtolower($str1) : 'index';
    $_action = strtolower(gv(2, $tmp));
}

/**
 * target word and delimiter change to upper camel style
 * @param string $_str : target word
 * @param string $_delimiter : target delimiter
 * @return string eg) conv_nameId('abc_def', '_'); 'AbcDef'
 */
function conv_nameId($_str, $_delimiter = '_')
{
    $tmp = explode($_delimiter, $_str);
    $str = implode(' ', $tmp);
    $str = ucwords($str);
    return str_replace(' ', '', $str);
}

/**
 * target word and delimiter change to lower case and '-'
 * @param string $_str : target word
 * @param string $_delimiter : target delimiter
 * @return string eg) conv_folderId('abc_def', '_'); 'abc-def'
 */
function conv_folderId($_str, $_delimiter = '_')
{
    $tmp = explode($_delimiter, $_str);
    $str = implode('-', $tmp);
    return strtolower($str);
}

/**
 * change array to array object
 * @param array $_array : target array
 * @return boolean|ArrayObject
 */
function get_array_object($_array)
{
    if (!is_array($_array)) {
        return false;
    }
    $obj = new ArrayObject($_array);
    $obj->setFlags(ArrayObject::ARRAY_AS_PROPS);
    return $obj;
}

/**
 * return value of target key from array
 * if not exist return default
 * @param string $_key : key
 * @param array $_ary : array
 * @param string $_default : default value
 * @return string
 */
function gv($_key, $_ary, $_default = null)
{
    if (!is_array($_ary) || $_key === "" || $_key === null) {
        return $_default;
    }
    $val = (isset($_ary[$_key]) && '' !== $_ary[$_key]) ? $_ary[$_key] : $_default;
    return $val;
}

/**
 * change target int to byte size
 * @param int|string $_size : target int
 * @return int
 */
function get_real_size($_size)
{
    if (!$_size) {
        return 0;
    }

    $scan = array();
    $scan['gb'] = 1073741824;   // 1024 * 1024 * 1024
    $scan['g']  = 1073741824;   // 1024 * 1024 * 1024
    $scan['mb'] = 1048576;      // 1024 * 1024
    $scan['m']  = 1048576;      // 1024 * 1024
    $scan['kb'] =    1024;
    $scan['k']  =    1024;
    $scan['b']  =       1;

    foreach ($scan as $unit => $factor) {
        if (strlen($_size) > strlen($unit)
            && strtolower(substr($_size, strlen($_size) - strlen($unit))) == $unit
        ) {
            return substr($_size, 0, strlen($_size) - strlen($unit)) * $factor;
        }
    }
    return $_size;
}

/**
 * change target int to specified unit
 * @param string $_size : eg) 1GB, 2G, 1024KB ....
 * @param string $_unit : eg) gb, g, mb, m, kb, k, b
 * @param boolean $_num : true = value + *iB  /  false = only value
 * @param boolean $_int : true = round  /  false = not round
 * @param boolean $_comma : true = comma separated value  /  false = only value
 * @return int|float|string
 */
function format_size($_size, $_unit = "", $_num = false, $_int = false, $_comma = false)
{
    if (!$_size) {
        return 0;
    }

    $str = array();
    $str['gb'] = 'GiB';
    $str['g']  = 'GiB';
    $str['mb'] = 'MiB';
    $str['m']  = 'MiB';
    $str['kb'] = 'KiB';
    $str['k']  = 'KiB';
    $str['b']  = 'Byte';

    $scan = array();
    $scan['gb'] = 1073741824;   // 1024 * 1024 * 1024
    $scan['g']  = 1073741824;   // 1024 * 1024 * 1024
    $scan['mb'] = 1048576;      // 1024 * 1024
    $scan['m']  = 1048576;      // 1024 * 1024
    $scan['kb'] =    1024;
    $scan['k']  =    1024;
    $scan['b']  =       1;

    // if conversion not supported, return original
    $unit = strtolower($_unit);
    if (!gv($unit, $scan)) {
        return $_size;
    }

    $b_size = get_real_size($_size);    // change to byte unit
    $size = $b_size / gv($unit, $scan); // calculate unit

    // round
    if ($_int) {
        $size = round($size);
    }
    // separate by comma
    if ($_comma) {
        $size = number_format($size);
    }

    return $size . ($_num ? ' ' . gv($unit, $str) : '');
}

/**
 * create random string
 * @param int $_len : number of char
 * @param int $_type : (1=small, 2=capital+small, 3=alphanumeric, 4=alphanumeric+sign)
 * @return boolean|string
 */
function make_rand_str($_len, $_type = 4)
{
    if (!$_len || !$_type || !is_int($_len)
           || !is_int($_type) || 4 < $_type) {
        return false;
    }

    $_tbl[1] = 'abcdefghijklmnopqrstuvwxyz';
    $_tbl[2] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $_tbl[3] = '0123456789';
    $sign = '!#$%&@_+-*/?';

    $i = 0;
    $tmp = "";
    $cnt = 4 == $_type ? 3 : $_type;
    while ($i < $cnt) {
       ++ $i;
       $tmp .= $_tbl[$i];
    }

    $i = 0;
    $str = "";
    $max = strlen($tmp);
    while ($i < $_len) {
        ++ $i;
        $pos = rand(0, $max);
        $str .= substr($tmp, $pos, 1);
    }

    // include sign
    if (4 == $_type) {
        $pos = rand(0, strlen($sign));
        $s = substr($sign, $pos, 1);

        $pos = rand(1, $_len);
        $str = substr($str, 0, $pos) . $s . substr($str, $pos, $_len - $pos);
        $str = substr($str, 0, $_len);
    }
    return $str;
}

/**
 * get string for token
 * @return string
 */
function make_token_id()
{
    return md5(microtime() . rand(0, 1000));
}

/**
 * add "before_" to key except the key of (deleted, create_, update_)
 * @param array $_data : array
 * @param string $_primary : exception key
 * @return ArrayObject|boolean
 */
function make_before_data($_data, $_primary)
{
    if (!$_data) {
        return false;
    }

    $ret = array();
    foreach ($_data as $key => $val) {
        if ($key == $_primary || $key == 'deleted'
                || $key == 'create_user' || $key == 'create_time'
                || $key == 'update_user' || $key == 'update_time') {
            continue;
        }
        $ret['before_' . $key] = $val;
    }

    return get_array_object($ret);
}

/**
 * check changing
 * @param array $_data : array
 * @return boolean ex) $_data[before_abc] : != $_data[abc]:true
 */
function check_change_data($_data)
{
    $chk = false;
    foreach ($_data as $key => $val) {
        if (preg_match('/^(before_)/', $key)) {
            $key = preg_replace('/^(before_)/', '', $key);
            $chk = ($val == $_data->$key) ? false : true;
        }

        if ($chk) {
            break;
        }
    }
    return $chk;
}

/**
 * get timezone list
 * @staticvar array $regions
 * @param string $_sort : sort 'zone':timezoneをキーに昇順とする
 * @return array : type of array:'{timezone}' => '(UTC {sign}H:i) {timezone}',
 */
function make_time_list($_sort = 'offset')
{
    static $regions = array(
        DateTimeZone::AFRICA,
        DateTimeZone::AMERICA,
        DateTimeZone::ANTARCTICA,
        DateTimeZone::ASIA,
        DateTimeZone::ATLANTIC,
        DateTimeZone::AUSTRALIA,
        DateTimeZone::EUROPE,
        DateTimeZone::INDIAN,
        DateTimeZone::PACIFIC,
    );

    $zones = array();
    foreach ($regions as $region) {
        $zones = array_merge($zones, DateTimeZone::listIdentifiers($region));
    }

    $offsets = array();
    foreach ($zones as $zone) {
        $tz = new DateTimeZone($zone);
        $offsets[$zone] = $tz->getOffset(new DateTime);
    }

    // sort offset
    asort($offsets);

    $list = array();
    foreach ($offsets as $zone => $offset) {
        $prefix = $offset < 0 ? '-' : '+';
        $format = gmdate('H:i', abs($offset));
        $prety = "UTC ${prefix}${format}";
        $list[$zone] = "$zone (${prety})";
    }
    if ('zone' == $_sort) {
        ksort($list);
    }
    return $list;
}

/**
 * get timezone of constant
 * @return string {timezone} (UTC {sign} H:i)
 */
function get_timezone()
{
    $tz = new DateTimeZone(TIME_ZONE);
    $offset = $tz->getOffset(new DateTime);
    $prefix = $offset < 0 ? '-' : '+';
    $format = gmdate('H:i', abs($offset));
    $prety = "UTC ${prefix}${format}";
    return TIME_ZONE . " (${prety})";
}

/**
 * check existence of timezone
 * @param string $_zone time zone
 * @return boolean
 */
function check_timezone($_zone)
{
    if (!$_zone) {
        return false;
    }
    $tz = DateTimeZone::listIdentifiers();
    return in_array($_zone, $tz);
}

/**
 * get diffrence timezone and UTC (seconds)
 * @param string $_zone time zone
 * @return int
 */
function time_diff($_zone)
{
    $tz = new DateTimeZone($_zone);
    return $tz->getOffset(new DateTime);
}

/**
 * is iPad
 * @return boolean
 */
function is_iPad()
{
    return preg_match('/iPad/', USER_AGENT);
}

/**
 * is iPhone
 * @return boolean
 */
function is_iPhone()
{
    return preg_match('/iPhone/', USER_AGENT);
}

/**
 * is Android Tablet
 * @return boolean
 */
function is_androidPad()
{
    return preg_match('/Android((?!Mobile).)+$/', USER_AGENT);
}

/**
 * is Android
 * @return boolean
 */
function is_android()
{
    return preg_match('/Android.+Mobile/', USER_AGENT);
}
