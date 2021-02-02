<?php

/**
 * 数据库设置
 *  网站部署需要配置
 */
    return array(

        'DB_TYPE'               =>'mysql',
        'DB_HOST'               =>  '127.0.0.1', // 服务器地址
        'DB_NAME'               =>  'sanya_travel',          // 数据库名
        'DB_USER'               =>  'root',      // 用户名
        'DB_PWD'                =>  'root',          // 密码
        'DB_PORT'               =>  '3306',        // 端口
        'DB_PREFIX'             =>  '',    // 数据库表前缀
        'DB_DEBUG'  			=>  true, // 数据库调试模式 开启后可以记录SQL日志
        'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    );
?>