<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Controller;

class Login extends Controller{
  
    public function index()
    {
        //重置session
        Session::clear();
        return  $this->fetch();
    
    }
  
   
    public function dologin()
    {
      
        $secure_code="沧海猎人";
        //重置session
        Session::clear();
        
        $username = Request::instance()->post('username');
        $password = Request::instance()->post('password');
        $md5password = md5($secure_code.md5($password));
        $map['username']=$username;
		$map['password']=$md5password;
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
            Session::set('org_id',$data[0]['org_id']);
            Session::set('dep_id',$data[0]['dep_id']);
          
            //Session::set('org_id',$data[0]['org_id']);
          
          
            $map_org['id']=$data[0]['org_id'];
            $map_org['is_del']=0;
            $data_org = Db::table('organization')
              ->where($map_org)
              ->select();
            if(count($data_org)==1)
        	{
                Session::set('org_name',$data_org[0]['corpname']);
              
                $service = new \app\admin\service\Log();
            	$service->write("1","登录成功");
            	$this->redirect(url('admin/index/index'));  	
            }else{
            	$service = new \app\admin\service\Log();
            	$service->write("1","获取机构名称错误".$username,$username);
            	$this->error("获取机构名称错误");
            }   
        }else{
            $service = new \app\admin\service\Log();
            $service->write("1","登录失败".$username,$username);
            $this->error("用户名或密码错误");
        }
    }
}
