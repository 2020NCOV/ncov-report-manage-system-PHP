<?php
namespace app\superadmin2020\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Controller;

class Login extends Controller{
  
    public function index()
    {
        //重置session
        Session::clear();
        if(isset($_COOKIE['login_count'])){
            $count=$_COOKIE['login_count'];
        }
        else{
            $count=0;
            cookie('login_count',0);
        }
        if($count>=3){
            $this->assign('verify',true);
        }
        else {
            $this->assign('verify',false);
        }
        $this->assign('count',$count);
        return  $this->fetch();
    }
  
   
    public function dologin()
    {
        $secure_code="沧海猎人";
        //重置session
        $code = Request::instance()->post('code');
        if($code){
            if(!captcha_check($code)){
                $this->error("验证码错误！",'login/index');
            }
        }
        Session::clear();
        $username = Request::instance()->post('username');
        $password = Request::instance()->post('password');
        $md5password = md5($secure_code.md5($password));
        $map['username']=$username;
		$map['password']=$md5password;
        $map['is_del']=0;
        $map['org_id']=0;
        $map['dep_id']=0;
        $map['role']=1;
        //使用数组方式，防止SQL注入漏洞
        $data = Db::table('admin_user')
          ->where($map)
          ->select();
        //echo Db::getLastSql();
        if(count($data)>0)
        {
            Session::set('userid',$data[0]['id']);
            Session::set('role',$data[0]['role']);
          	Session::set('name',$data[0]['name']);
            Session::set('username',$data[0]['username']);
            Session::set('need',$data[0]['need_m_pass']);

            $service = new \app\admin\service\Log();
            $service->write("1","登录成功");
            $this->redirect(url('superadmin2020/index/index'));

        }else{
            if(!isset($_COOKIE['login_count'])){
                cookie('login_count',0,1800);
            }
            $count=$_COOKIE['login_count'];
            cookie('login_count',++$count,1800);
            if($count>=3){
                $this->assign('verify',true);
            }
            $service = new \app\admin\service\Log();
            $service->write("1","登录失败".$username,$username);
            $this->error("用户名或密码错误",'login/index');
            
        }
    }
}
