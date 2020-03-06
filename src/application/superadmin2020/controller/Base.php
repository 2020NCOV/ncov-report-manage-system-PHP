<?php
namespace app\superadmin2020\controller;

use think\Controller;
use think\Request;
use think\Session;
class Base extends Controller{

   public function _initialize()
   {
         //判断session是否存了userid,若不存在则进入登录页面
		if(null ==Session::get('userid') ){

           $this->redirect(url('superadmin2020/login/index'));
        }else{
            $this->assign("role", Session::get('role'));
            $this->assign("myname", Session::get('name'));
            $this->assign("org_name", Session::get('org_name'));
        }

       //判断session是否存了userid,若不存在则进入登录页面
       if(1 ==Session::get('need')){

           if((Request::instance()->module()=='superadmin2020' && Request::instance()->controller()=='Index'  && Request::instance()->action()=="updatepassword")
            || (Request::instance()->module()=='superadmin2020' && Request::instance()->controller()=='Index'  && Request::instance()->action()=="updatepw"))
           {
               
           }else {
               $this->redirect(url('superadmin2020/index/updatepassword'));
           }

       }

        
   }
   
}