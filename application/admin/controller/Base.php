<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
class Base extends Controller{

   public function _initialize()
   {
         //判断session是否存了userid,若不存在则进入登录页面
		if(null ==Session::get('userid') || null ==Session::get('org_name') ){
           //权限验证,接受微信公众平台回调的code,若接收不到code则代表该页面不是从微信公众号里打开的
           $this->redirect(url('admin/login/index'));
        }else{
            $this->assign("role", Session::get('role'));
            $this->assign("myname", Session::get('name'));
            $this->assign("org_name", Session::get('org_name'));
        }

       //判断session是否存了userid,若不存在则进入登录页面
       if(1 ==Session::get('need')){
           //权限验证,接受微信公众平台回调的code,若接收不到code则代表该页面不是从微信公众号里打开的
//           echo Request::instance()->module();
//           echo Request::instance()->controller();
//           echo Request::instance()->action();
           if((Request::instance()->module()=='admin' && Request::instance()->controller()=='Index'  && Request::instance()->action()=="updatepassword")
            || (Request::instance()->module()=='admin' && Request::instance()->controller()=='Index'  && Request::instance()->action()=="updatepw"))
           {
               
           }else {
               $this->redirect(url('admin/index/updatepassword'));
           }

       }

        
   }
   
}