<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
return [
    'default_return_type'    => 'html',
    // 模板参数替换
    'view_replace_str'       => array(
        '__STATIC__'=> '/static',
        '__CSS__'    => '/static/admin/css',
        '__JS__'     => '/static/admin/js',
        '__IMG__' => '/static/admin/images',
        '__NEW__'    => '/static/newadmin',
        '__NEWCSS__'    => '/static/newadmin/css',
        '__NEWJS__'     => '/static/newadmin/js',
        '__LAYUI__'=>'/static/admin/js/layui',
        '__HJ__'    => '/statics',
    ),

    // 管理员状态
    'user_status' => [
        '1' => '正常',
        '2' => '禁用'
    ],
    // 角色状态
    'role_status' => [
        '1' => '启用',
        '2' => '禁用'
    ],

    'role_status' => [
        '1' => '启用',
        '2' => '禁用'
    ],

    'banner_item_type' => [
        '1' => '商品',
        '2' => '主题'
    ],

    'theme_type' => [
        ['id'=>1,'name'=>'商品'],
        ['id'=>2,'name'=>'美图']
    ],

    'default_appoint_setting' => [
        'before_time' => 60,
        'limit_time' => 10,
        'notify_type'=> 0,
        'notify_cs_type' => 0,
    ]
];
