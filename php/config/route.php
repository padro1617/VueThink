<?php
use think\Route;

Route::get('/','index'); // 定义GET请求路由规则
Route::get('/2','/home/index/bind'); // 定义GET请求路由规则
Route::get('/3','/home/index/limit'); // 定义GET请求路由规则
Route::get('/4','/home/index/platform'); // 定义GET请求路由规则
Route::get('/5','/home/index/tips'); // 定义GET请求路由规则