<?php

return [
    'db'            => [
        'adapter'  => 'Mysql',
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123456',
        'dbname'   => 'xxxxx',
        // @link http://www.php.net/manual/zh/pdo.setattribute.php
        'options'  => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // 默认以数组方式提取数据
            PDO::ATTR_ORACLE_NULLS       => PDO::NULL_TO_STRING, // 所有 null 转换为 string
        ],
    ]
];