<?php
// +----------------------------------------------------------------------
// | Description: 基础框架路由配置文件
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honghaiweb.com>
// +----------------------------------------------------------------------

return [
    // 定义资源路由
    '__rest__'=>[
        'admin/rules'		   =>'admin/rules',
        'admin/groups'		   =>'admin/groups',
        'admin/users'		   =>'admin/users',
        'admin/menus'		   =>'admin/menus',
        'admin/structures'	   =>'admin/structures',
        'admin/posts'          =>'admin/posts',
    ],
	// 统计分页 导出数据
	'admin/users/export' => ['admin/base/export', ['method' => 'GET|POST|OPTIONS']],
	// 统计分页 导出数据
	// 'admin/base/export' => ['admin/base/export', ['method' => 'GET|POST|OPTIONS']],
	// 【基础】登录
	'admin/base/login' => ['admin/base/login', ['method' => 'POST|OPTIONS']],
	// 【基础】记住登录
	'admin/base/relogin'	=> ['admin/base/relogin', ['method' => 'POST|OPTIONS']],
	// 【基础】修改密码
	'admin/base/setInfo' => ['admin/base/setInfo', ['method' => 'POST|OPTIONS']],
	// 【基础】退出登录
	'admin/base/logout' => ['admin/base/logout', ['method' => 'POST|OPTIONS']],
	// 【基础】获取配置
	'admin/base/getConfigs' => ['admin/base/getConfigs', ['method' => 'POST|OPTIONS|GET']],
	// 【基础】获取验证码
	'admin/base/getVerify' => ['admin/base/getVerify', ['method' => 'GET']],
	// 【基础】上传图片
	'admin/upload' => ['admin/upload/index', ['method' => 'POST|OPTIONS']],
	// 保存系统配置
	'admin/systemConfigs' => ['admin/systemConfigs/save', ['method' => 'POST|OPTIONS']],
	// 【规则】批量删除
	'admin/rules/deletes' => ['admin/rules/deletes', ['method' => 'POST|OPTIONS']],
	// 【规则】批量启用/禁用
	'admin/rules/enables' => ['admin/rules/enables', ['method' => 'POST|OPTIONS']],
	// 【用户组】批量删除
	'admin/groups/deletes' => ['admin/groups/deletes', ['method' => 'POST|OPTIONS']],
	// 【用户组】批量启用/禁用
	'admin/groups/enables' => ['admin/groups/enables', ['method' => 'POST|OPTIONS']],
	// 【用户】批量删除
	'admin/users/deletes' => ['admin/users/deletes', ['method' => 'POST|OPTIONS']],
	// 【用户】批量启用/禁用
	'admin/users/enables' => ['admin/users/enables', ['method' => 'POST|OPTIONS']],
	// 【菜单】批量删除
	'admin/menus/deletes' => ['admin/menus/deletes', ['method' => 'POST|OPTIONS']],
	// 【菜单】批量启用/禁用
	'admin/menus/enables' => ['admin/menus/enables', ['method' => 'POST|OPTIONS']],
	// 【组织架构】批量删除
	'admin/structures/deletes' => ['admin/structures/deletes', ['method' => 'POST|OPTIONS']],
	// 【组织架构】批量启用/禁用
	'admin/structures/enables' => ['admin/structures/enables', ['method' => 'POST|OPTIONS']],
	// 【部门】批量删除
	'admin/posts/deletes' => ['admin/posts/deletes', ['method' => 'POST|OPTIONS']],
	// 【部门】批量启用/禁用
	'admin/posts/enables' => ['admin/posts/enables', ['method' => 'POST|OPTIONS']],
	
	// MISS路由
	//'__miss__'  => 'admin/base/miss',
];