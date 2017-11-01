<?php
namespace app\home\controller;
use think\Request;
use app\common\controller\Common;

class Index extends Common
{
    // 这是THinkphp下的自动运行的方法，所有继承这个控制器的子类
    // 首先运行此方法
    // public function _initialize()
    // {
    //     define('SID', is_login());
    //     if (!SID) {
    //         set_redirect_url($_SERVER['REQUEST_URI']);
    //         $this->redirect('Login/login');
    //     }
    //     //权限认证
    //     $auth = new \Auth\Auth();
    //     $request = Request::instance();
    //     if (!$auth->check($request->module() . '-' . $request->controller() . '-' . $request->action(), SID)) {// 第一个参数是规则名称,第二个参数是用户UID
    //         /* return array('status'=>'error','msg'=>'有权限！');*/
    //         $this->error('你没有权限');
    //     }
    // }
    public function index()
    {        
        // $this->assign('course_list', $course_list);
        return view('index');
        //return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }

    
    public function bind()
    {    
        return view('bind');
    }
    
    public function limit()
    {    
        return view('limit');
    }
    
    public function platform()
    {    
        return view('platform');
    }
    
    public function tips()
    {    
        return view('tips');
    }
}