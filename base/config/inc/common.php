<?php
/**
 * define common variable and constant
 * author : Aruze Gaming America, Inc.
 */

require_once 'function.php';

// check test env
$IS_TEST = false;
$SERVER_NAME = filter_input(INPUT_SERVER, "SERVER_NAME");
$HTTPS = filter_input(INPUT_SERVER, "HTTPS");
define('USER_AGENT', filter_input(INPUT_SERVER, "HTTP_USER_AGENT"));

// check device
define('IS_SP', preg_match('/(iPhone|Android.+Mobile)/', USER_AGENT));
define('IS_TABLET', preg_match('/(iPad|Android((?!Mobile).)+$)/', USER_AGENT));
define('IS_PC', (!IS_SP && !IS_TABLET));

if (preg_match('/^(172.16.)+[0-9]{1,3}(.)+[0-9]{1,3}$/', $SERVER_NAME)
        || preg_match('/(localhost)/', $SERVER_NAME)) {
   $IS_TEST = true;
}
else if ('cli' == php_sapi_name()) {
    $HOST_NAME = gethostname();
    if (preg_match('/(localhost)/', $HOST_NAME)) {
        $IS_TEST = true;
    }
}

//directory separator
PHP_OS == "Windows" || PHP_OS == "WINNT" ? define("SEPARATOR", "\\") : define("SEPARATOR", "/");

define('IS_TEST', $IS_TEST);

// allow only SSL in production env
if ('cli' !== php_sapi_name() && !IS_TEST && !$HTTPS) {
    echo 'unsafe to access.';
    exit();
}

$settingFile = 'setting' . (IS_TEST ? '_test' : '') . '.ini';
$ini = parse_ini_file(__DIR__ . '/' . $settingFile, true);
if (!$ini) {
    echo "common file open failed.";
    exit();
}
$tool = gv('tool', $ini);
$db = gv('db', $ini);

/****** setting tools ******/
define('TOOL_NAME', gv('tool_name', $tool));            // tool name
define('AUTO_MAIL_FROM', gv('auto_mail_from', $tool));  // auto mail from address
define('AUTO_MAIL_FROM_PASSWORD', gv('auto_mail_from_password', $tool));  //
// setting URL and directory
define('BASE_DOMAIN', gv('base_domain', $tool));        // setting domain
define('BASE_URL', gv('base_url', $tool));              // base URL
define('DOCUMENT_ROOT', gv('document_root', $tool));    // root directory
define('BASE_PDF_DIR', gv('base_pdf_dir', $tool));      // PDF storage location
define('ZEND2_DIR', gv('zend2_dir', $tool));            // zend2 location
define('ZEND2_RESOURCE', gv('zend2_resources', $tool)); // zend2 resources location
define('APP_DIR', gv('app_dir', $tool));        // application location
define('APP_UPLOAD_DIR', gv('aapp_upload_dir', $tool));
// setting directory for view
define('VIEW_PC', gv('view_pc', $tool));
define('VIEW_SP', gv('view_sp', $tool));
define('VIEW_PAD', gv('view_pad', $tool));

$view_dir = "";
$view_type = filter_input(INPUT_COOKIE, 'view_type');
if ($view_type) {
    setcookie('view_type', $view_type, time() + (60 * 60 * 24 * 30), '/', BASE_DOMAIN);
    switch ($view_type) {
        case 'sp':
            $view_dir = VIEW_SP;
            break;
        case 'pad':
            $view_dir = VIEW_PAD;
            break;
        default :
            $view_dir = VIEW_PC;
            break;
    }
}
else if (IS_SP) {
    $view_dir = VIEW_SP;
}
else if (IS_TABLET) {
    $view_dir = VIEW_PAD;
}
else {
    $view_dir = VIEW_PC;
}
define('VIEW_DIR', ($view_dir ? $view_dir : VIEW_PC));

// setting language
$lang_id = filter_input(INPUT_COOKIE, 'lang_id');
if ($lang_id && !file_exists(__DIR__ . '/../../module/App/language/' . $lang_id . '.mo')) {
    $lang_id = 'en_US';
}
define('LANG_ID', ($lang_id ? $lang_id : 'en_US'));

$resource_id = filter_input(INPUT_COOKIE, 'resource_id');
if ($resource_id && !file_exists(ZEND2_RESOURCE . '/languages/' . $resource_id . '/Zend_Validate.php')) {
    $resource_id = 'en';
}
define('RESOURCE_DIR', ($resource_id ? $resource_id : 'en'));

// setting timezone
$timezone = filter_input(INPUT_COOKIE, 'timezone');
if (!$timezone || !check_timezone($timezone)) {
    $timezone = 'UTC';
}
define('TIME_ZONE', $timezone);

// get time difference
$time_diff = time_diff($timezone);
define('TIME_DIFF', $time_diff);

/****** setting constant about session ******/
define('SESSION_NAME', gv('session_name', $tool));               // session name
define('CACHE_EXPIRE', gv('cache_expire', $tool) / 60);          // cached session time
define('COOKIE_LIFETIME', gv('cookie_lifetime', $tool));         // cookie time
define('COOKIE_DOMAIN', BASE_DOMAIN);                           // setting cookie domain
define('GC_MAXLIFETIME', gv('gc_maxlifetime', $tool));           // garbage collection time
define('GC_DIVISOR', gv('gc_divisor', $tool));                   // garbage collection percentage
define('GC_PROBABILITY', gv('gc_probability', $tool));           // garbage collection probability
define('REMEMBER_ME_SECONDS', gv('remember_me_seconds', $tool)); // seconds to hold session prior to data clear
define('USE_COOKIES', gv('use_cookies', $tool));                 // is use cookie for session
define('SESSION_ID_EXPIRE', gv('session_id_expire', $tool));     // seconds of same session ID

define('COOKIE_SSL_ONLY', (IS_TEST ? ($HTTPS ? true : false) : true));  // is cookie only SSL
ini_set('session.cookie_secure', (COOKIE_SSL_ONLY ? 'ON' : ''));        // setting session.cookie_secure

/* setting constant about login */
define('EXPIRE_PW', gv('expire_pw', $tool));                     // number of days password
define('FORBID_SAME_PW', gv('forbid_same_pw', $tool));           // number of check same password
define('LOGIN_FAILED_COUNT', gv('login_failed_count', $tool));   // number of times login failures in same day
define('RESTRICT_LOGIN', gv('restrict_login', $tool));           // multiple login(0:allow, 1:not allow)
define('PW_MIN_LENGTH', gv('pw_min_length', $tool));             // password min length
define('PW_MAX_LENGTH', gv('pw_max_length', $tool));             // password max length
define('PW_REGEX_PATTERN', gv('pw_regex_pattern', $tool));       // password regular expression

/****** setting constant about log ******/
define('LOG_LEVEL', gv('log_level', $tool));        // output log min level
define('LOG_ACCESS', gv('log_access', $tool));      // is get access log
define('LOG_DIR', gv('log_dir', $tool));            // log storage location
define('LOG_ROTATE', gv('log_rotate', $tool));      // number of days save log

/****** setting constant about DB ******/
define('DB_HOST', gv('host', $db));
define('DB_NAME', gv('name', $db));
define('DB_USER', gv('user', $db));
define('DB_PASS', gv('pass', $db));
define('DB_CHAR', gv('char', $db));
