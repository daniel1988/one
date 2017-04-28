<?php
defined('APP_PATH') || define('APP_PATH', dirname(__FILE__) . '/');
// 生产环境
defined('PRODUCTION') || define('PRODUCTION', is_file('/etc/php.env.production'));

// 预发环境
defined('STAGING') || define('STAGING', is_file('/etc/php.env.staging'));

// 测试环境
defined('TESTING') || define('TESTING', is_file('/etc/php.env.testing'));

// 开发环境
defined('DEVELOPMENT') || define('DEVELOPMENT', !(PRODUCTION || STAGING || TESTING));

include dirname(__FILE__) . '/../src/OneLib/Config.php' ;

use \OneLib\Config;


var_dump( Config::env_str() );

var_dump( Config::load('redis.redis_1'));

var_dump( Config::load('database.db'));
