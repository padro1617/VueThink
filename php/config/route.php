<?php
//use think\Route;
//
//Route::rule('/','home/index/index','GET|POST'); // 定义GET请求路由规则
//Route::rule('2','home/index/bind','GET|POST'); // 定义GET请求路由规则
//Route::rule('3','home/index/limit','GET|POST'); // 定义GET请求路由规则
//Route::rule('4','home/index/platform','GET|POST'); // 定义GET请求路由规则
//Route::rule('5','home/index/tips','GET|POST'); // 定义GET请求路由规则


return [
// 【基础】登录
    '/' => ['home/index/index', ['method' => 'GET|POST|OPTIONS']],
//推广的链接地址
    't/:tcode' => ['home/index/index', ['method' => 'GET|POST|OPTIONS']],
    'logout' => ['home/index/logout', ['method' => 'GET|OPTIONS']],
    'smsapi' => ['home/index/smsapi', ['method' => 'POST|OPTIONS']],
    'limit' => ['home/index/limit', ['method' => 'GET|POST|OPTIONS']],
    'bind' => ['home/index/bind', ['method' => 'GET|POST|OPTIONS']],
    'tips' => ['home/index/tips', ['method' => 'GET|POST|OPTIONS']],
    'platform' => ['home/index/platform', ['method' => 'GET|POST|OPTIONS']]
];