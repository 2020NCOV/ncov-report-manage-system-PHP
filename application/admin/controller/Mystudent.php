<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;
class Mystudent extends Base
{
    public function index()
    { 
       if(5 ==Session::get('role')){
           
           
           
           return  $this->fetch();
       }else
       {
           $this->error("无权访问");
       }
    
    }
  


}
