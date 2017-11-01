<?php
namespace app\home\controller;
use app\common\controller\Common;
use think\Db;
use think\Request;
use app\common\model\User;

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
    public function index(){
        if(Request::instance()->isPost()){
            $userModel = model('User');
            $param = $this->param;
            $phone = $param['phone'];
            $code = $param['code'];
            $time = $this->request->time();
            if($phone==''){
                return resultArray(['error' => '手机号不能为空！']);
            }
            if($code==''){
                return resultArray(['error' => '短信验证码不能为空！']);
            }
            //获取短信验证码内容
            $_code =get_phonecode($phone);
            if (empty($_code)) {
                return resultArray(['error' => '验证码已失效，请刷新页面，重新获取验证码！']);
            }
            if ($_code!=$code) {
                return resultArray(['error' => '验证码错误！']);
            }
            $userinfo=$userModel->where(array('phone'=>$phone))->find();
            if($userinfo==Null){
                //获取推广码
                $tcode = $param['tcode'];
                $tuid=0;//推广员ID  -1代表推广员 0代表来着网络
                if(!empty($tcode)){
                    //查询推广员的ID
                    $t_userinfo=$userModel->where(array('tcode'=>$tcode))->find();
                    if($userinfo!=Null) {
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
                if ($userinfo['status']==0) {
                    return resultArray(['error' => '该手机号已被拉黑，请联系管理员恢复！']);
                }
            }
            //去除短信验证码
            set_phonecode(null,null);

            $auth = array('aid' => $resultuid, 'last_time' => $time);
            //设置登录session
            session('ke_user_auth', $auth);
            return resultArray(['data' => Url('home/Index/limit')]);
            //return array('status' => 1, 'url' => Url('home/Index/limit'));
        }
        if (is_login()) {
            $rurl=get_redirect_url();
            if ($rurl&&$rurl!='/') {
                redirect($rurl);
            }
            else {
                $this->redirect('home/Index/limit');
            }
        }
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
            if($phone==''){
                return resultArray(['error' => '手机号不能为空！']);
            }
            if(!empty(get_phonecode($phone))){
                return resultArray(['data' => "30分钟有效"]);
            }
            //设置短信验证码
            $timestr=(string)time();
            $phonecode=substr($timestr , -4);
            //测试
//            set_phonecode($phone,$phonecode);
//            return resultArray(['data' => "短信发送成功,短信ID：".$phonecode]);
            $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
            $smsConf = ['key'       => 'fd6c17a2cd2796ef45d05b2b655c14c8', //您申请的APPKEY
                        'mobile'    => $phone, //接受短信的用户手机号码
                        'tpl_id'    => '49300', //您申请的短信模板ID，根据实际情况修改
                        'tpl_value' => '#code#='.$phonecode //您设置的模板变量，根据实际情况修改 &#company#=聚合数据
            ];
            $content = juhecurl($sendUrl, $smsConf, 1); //请求发送短信
            if ($content) {
                $result     = json_decode($content, true);
                $error_code = $result['error_code'];
                if ($error_code == 0) {
                    //状态为0，说明短信发送成功
                    //这是短信码session
                    set_phonecode($phone,$phonecode);
//                    return resultArray(['data' => "短信发送成功,短信ID：" . $result['result']['sid']]);
                    return resultArray(['error' => "发送成功"]);
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
        $uid=is_login();
        if (!$uid) {
            $this->redirect('home/Index/index');
        }
        return view('limit');
    }
    //登出
    public  function logout(){
        set_phonecode(null,null);
        if(is_login()){
            //去除短信验证码
            session('ke_user_auth',null);
        }
        $this->redirect('/');
    }

    /**
     * 绑定信息
     * @return \think\response\View
     */
    public function bind()
    {
        $uid=is_login();
        if (!$uid) {
            $this->redirect('home/Index/index');
        }
        if(Request::instance()->isPost()) {
            //
            $userModel = model('User');
            $param     = $this->param;
            $bankphone = $param['bankphone'];
            $idcard = $param['idcard'];
            $bankcard = $param['bankcard'];
            $truename = $param['truename'];
            $code      = $param['code'];
            $time      = $this->request->time();
            if ($bankphone == '') {
                return resultArray(['error' => '手机号不能为空！']);
            }
            if ($code == '') {
                return resultArray(['error' => '短信验证码不能为空！']);
            }
            //获取短信验证码内容
            $_code = get_phonecode($bankphone);
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
            return resultArray(['data' => Url('home/Index/tips')]);
        }
        return view('bind');
    }

    public function tips()
    {
        $uid=is_login();
        if (!$uid) {
            $this->redirect('home/Index/index');
        }
        return view('tips');
    }
    public function platform()
    {
        $uid=is_login();
        if (!$uid) {
            $this->redirect('home/Index/index');
        }
        return view('platform');
    }

}