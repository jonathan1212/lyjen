<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST,
        'username' => 'root',
        'password' => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHAR,
            PDO::MYSQL_ATTR_INIT_COMMAND => " SET SESSION time_zone = '+0:00'"
        ),

    ),
	 'service_manager' => array(
                'factories' => array(
                        'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'
                )
        ),
    'session_config' => array(
        'options' => array(
            'name' => SESSION_NAME,
            'cache_expire' => CACHE_EXPIRE,
            'cookie_lifetime' => COOKIE_LIFETIME,
            'cookie_domain' => COOKIE_DOMAIN,
            'cookie_secure' => COOKIE_SSL_ONLY,
            'cookie_path' => '/',
            'remember_me_seconds' => REMEMBER_ME_SECONDS,
            'use_cookies' => USE_COOKIES,
            'gc_maxlifetime' => GC_MAXLIFETIME,
            'gc_divisor' => GC_DIVISOR,
            'gc_probability' => GC_PROBABILITY,
        ),
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent',
        ),
    ),
);
