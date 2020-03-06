<?php
namespace app\admin\service;

use think\Request;
use think\Db;
use think\Session;
class Log {

   public function write($type,$content,$usernamme="1")
   {
       $request = Request::instance();
       if (strlen($usernamme) > 1) {
           $data['uid'] = 0;
       } else {
            $data['uid'] = Session::get('userid');
        }
       $data['ope_type']=$type;
       $data['path']=$request->pathinfo();
       $data['content']=$content;
       $data['ip']=$request->ip();
       $data['agent']=$_SERVER['HTTP_USER_AGENT'];
       Db::table('admin_user_log')->insert($data);

        
   }
   
}