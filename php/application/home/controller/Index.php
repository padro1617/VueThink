<?php
namespace app\home\controller;
use app\common\controller\Common;
use think\Db;
use think\Request;
use app\common\model;

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
    /**
     * 登录
     * 短信验证码
     * $date=date('Y-m-d H:i:s',"1285724523");
     */
    public function index($tcode=''){
        if(Request::instance()->isPost()){
            $userModel = model('User');
            $param = $this->param;
            $phone = $param['phone'];
            $code = $param['code'];
            $codetype = $param['codetype'];//'login';
            $time = $this->request->time();
            if($phone==''){
                return resultArray(['error' => '手机号不能为空！']);
            }
            if($code==''){
                return resultArray(['error' => '短信验证码不能为空！']);
            }
            //获取短信验证码内容
            $_code =get_phonecode($phone.'_'.$codetype);
            if (empty($_code)) {
                return resultArray(['error' => '验证码已失效，请刷新页面，重新获取验证码！']);
            }
            if ($_code!=$code) {
                return resultArray(['error' => '验证码错误！']);
            }
            $bankcard='';
            $userinfo=$userModel->where(array('phone'=>$phone))->find();
            if($userinfo==Null){
                //获取推广码
                $tcode = $param['tcode'];
                $tuid=0;//推广员ID  -1代表推广员 0代表来着网络
                if(!empty($tcode) && $tcode!='0'){
                    //查询推广员的ID
                    $t_userinfo=$userModel->where(array('tcode'=>$tcode))->find();
                    if($t_userinfo!=Null) {
                        $tuid=$t_userinfo['id'];
                    }
                }
                //{username: "hjhhadmin", realname: "管理员", structure_id: 1, remark: "", groups: [17]}
                $uparam=array(
                    'groups'=>[15],
                    'username'=>'注册用户',
                    'phone'=>$phone,
                    'tuid'=>$tuid,
                    'tcode'=>'',
                    'status'=>1,
                );
                $resultuid = $userModel->createData($uparam);
                if (!$resultuid) {
                    return resultArray(['error' => $userModel->getError()]);
                }
//                return resultArray(['data' => '账号注册成功']);
            }else{
                $resultuid=$userinfo['id'];
                $bankcard=$userinfo['bankcard'];
                if ($userinfo['status']==0) {
                    return resultArray(['error' => '该手机号已被拉黑，请联系管理员恢复！']);
                }
            }
            //去除短信验证码
            set_phonecode($phone.'_'.$codetype,null);

            $auth = array('aid' => $resultuid,'bankcard'=>$bankcard,'phone'=>$phone, 'last_time' => $time);
            //设置登录session
            session('ke_user_auth', $auth);
            if($bankcard){
                //已绑定过
                return resultArray(['data' => Url('home/Index/platform')]);
            }else{
                return resultArray(['data' => Url('home/Index/limit')]);
            }
            //return array('status' => 1, 'url' => Url('home/Index/limit'));
        }
        $sessionuser=is_login();
        if ($sessionuser) {
            if($sessionuser['bankcard']){
                //已绑定过
                $this->redirect('home/Index/platform');
            }else{ 
                $this->redirect('home/Index/limit');
            }
            // $rurl=get_redirect_url();
            // if ($rurl&&$rurl!='/') {
            //     redirect($rurl);
            // }
            // else {
            //     $this->redirect('home/Index/limit');
            // }
        }
        $this->assign('tcode',$tcode);
        return view();
    }
    /*
        ***聚合数据（JUHE.CN）短信API服务接口PHP请求示例源码
        ***DATE:2015-05-25
    */
    public function smsapi(){
        if(Request::instance()->isPost()) {
            $param = $this->param;
            $phone = $param['phone'];
            $codetype = $param['codetype'];            
            if($phone==''){
                return resultArray(['error' => '手机号不能为空！']);
            }
            if(!empty(get_phonecode($phone.'_'.$codetype))){
                return resultArray(['data' => "30分钟有效,已发送过了哦"]);
            }
            $smstplid='50408';//【小白金服】您的验证码是#code#。请
            if($codetype=='login'){
                $smstplid='49300';//登陆的
            }
            //设置短信验证码
            $timestr=(string)time();
            $phonecode=substr($timestr , -4);
            //测试
//            set_phonecode($phone.'_'.$codetype,$phonecode);
//            return resultArray(['data' => "短信发送成功,短信ID：".$phonecode]);
            $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
            $smsConf = ['key'       => 'fd6c17a2cd2796ef45d05b2b655c14c8', //您申请的APPKEY
                        'mobile'    => $phone, //接受短信的用户手机号码
                        'tpl_id'    => $smstplid, //您申请的短信模板ID，根据实际情况修改
                        'tpl_value' => '#code#='.$phonecode //您设置的模板变量，根据实际情况修改 &#company#=聚合数据
            ];
            $content = juhecurl($sendUrl, $smsConf, 1); //请求发送短信
            if ($content) {
                $result     = json_decode($content, true);
                $error_code = $result['error_code'];
                if ($error_code == 0) {
                    //状态为0，说明短信发送成功
                    //这是短信码session
                    set_phonecode($phone.'_'.$codetype,$phonecode);
//                    return resultArray(['data' => "短信发送成功,短信ID：" . $result['result']['sid']]);
                    return resultArray(['data' => "发送成功"]);
                    //echo "短信发送成功,短信ID：" . $result['result']['sid'];
                } else {
                    //状态非0，说明失败
                    $msg = $result['reason'];
                    return resultArray(['error' => "短信发送失败(" . $error_code . ")：" . $msg]);
                }
            } else {
                //返回内容异常，以下可根据业务逻辑自行修改
                return resultArray(['error' => "请求发送短信失败"]);
            }
        }
        return resultArray(['error' => "请求失败"]);
    }

    /**
     * 额度
     * @return \think\response\View
     */
    public function limit()
    {
        $sessionuser=is_login();
        if (!$sessionuser) {
            $this->redirect('home/Index/index');
        }
        return view('limit');
    }
    //登出
    public  function logout(){
        $sessionuser=is_login();
        if ($sessionuser) {
            //去除短信验证码
            session('ke_user_auth',null);
            set_phonecode($sessionuser['phone'].'_login',null);
            set_phonecode($sessionuser['phone'].'_bind',null);
        }
        $this->redirect('/');
    }

    /**
     * 绑定信息
     * @return \think\response\View
     */
    public function bind()
    {
        $sessionuser=is_login();
        if (!$sessionuser) {
            $this->redirect('home/Index/index');
        }
        $uid=$sessionuser['aid'];
        if(Request::instance()->isPost()) {
            //
            $userModel = model('User');
            $param     = $this->param;
            $bankphone = $param['bankphone'];
            $idcard = $param['idcard'];
            $bankcard = $param['bankcard'];
            $truename = $param['truename'];
            $code      = $param['code'];
            $codetype      =$param['codetype'];//'bind';// $param['codetype'];
            $time      = $this->request->time();
            if ($bankphone == '') {
                return resultArray(['error' => '手机号不能为空！']);
            }
            if ($code == '') {
                return resultArray(['error' => '短信验证码不能为空！']);
            }
            //获取短信验证码内容
            $_code = get_phonecode($bankphone.'_'.$codetype);
            if (empty($_code)) {
                return resultArray(['error' => '验证码已失效，请刷新页面，重新获取验证码！']);
            }
            if ($_code != $code) {
                return resultArray(['error' => '验证码错误！']);
            }
            $userinfo = $userModel->where(['id' => $uid])->find();
            if ($userinfo == null) {
                return resultArray(['error' => '您的账号信息已丢失，请重新登录！']);
            }
            $uparam=array(
                'bankphone'=>$bankphone,
                'idcard'=>$idcard,
                'bankcard'=>$bankcard,
                'realname'=>$truename
            );
            $data = $userModel->updateDataById($uparam,$uid);
            if (!$data) {
                return resultArray(['error' => $userModel->getError()]);
            }
//            return resultArray(['data' => '绑定成功']);
            
            $sessionuser['bankcard']=$bankcard;
            $sessionuser['last_time'] =$time;
            //更新session
            session('ke_user_auth', $sessionuser);
            return resultArray(['data' => Url('home/Index/tips')]);
        }
        return view('bind');
    }

    public function tips()
    {
        $sessionuser=is_login();
        if (!$sessionuser) {
            $this->redirect('home/Index/index');
        }
        return view('tips');
    }
    public function platform()
    {
        $sessionuser=is_login();
        if (!$sessionuser) {
            $this->redirect('home/Index/index');
        }
        $uid=$sessionuser['aid'];
        if(Request::instance()->isPost()) {
            //session 防刷数据
            $param = $this->param;
            $postid      = $param['postid'];
            $skey='platform_click_'.$postid;
            $c=session($skey);
            if($c && $c['last_time']>0 && $c['postid']==$postid){
                //计算超时时间 1509545004  10分钟内有效
                if(time()-$c['last_time']<600){
                    return resultArray(['error' => '点击在10分钟内记录一次']);
                }else{
                    session($skey,null);
                }
            }
            //写入日志数据
            $postlogModel = model('Postlog');
            //写入日志 校验时间
            $plogid=$postlogModel->add($uid,$postid);
            if (!$plogid) {
                return resultArray(['error' => $postlogModel->getError()]);
            }
            session($skey,array('postid'=>$postid, 'last_time' => time()));
            return resultArray(['data' => '日志写入成功']);
        }
        $postModel = model('Post');
//        $keywords = empty($param['keywords'])? $param['keywords']: '';
        $data = $postModel->getDataList(null);
        $this->assign('postlist',$data);
        return view('platform');
    }

}