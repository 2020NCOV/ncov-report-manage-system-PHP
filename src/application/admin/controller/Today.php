<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;
class Today extends Base
{
    public function index()
    { 
      
        $admin_uid = Session::get('userid');  //用户id
       $admin_role = Session::get('role'); //  管理员角色
       $org_id = Session::get('org_id'); //   机构id
       $org_name = Session::get('org_name'); // 机构名称
       $dep_id = Session::get('dep_id'); // 机构名称
      
      
       $org_id = Session::get('org_id'); //   机构id
       if(1 ==Session::get('role') || 2 ==Session::get('role') || 3 ==Session::get('role')|| 4 ==Session::get('role')){

           
           $msg = "今日未提交";
           $today = date('Y-m-d');
           $today1 = date('Y-m-d',strtotime('+1 day'));
           
           $where_str = " org_id =".$org_id." And report_date NOT BETWEEN '".$today."' AND '".$today1."'" ;
         
           if($admin_role ==3)
           {
              $where_str = "sub1_department_id=".$dep_id ."  and ".$where_str;
           }
           //echo $where_str;
          
         
           $UserData = Db::table('org_whitelist')
               ->where($where_str)
               ->order('userID')
               ->select();
    
           $count= count($UserData);
           $this->assign("count", $count);
           $this->assign("msg", $msg);
           $this->assign("userlist", $UserData);
           //var_dump($StuData);
           
           return  $this->fetch();
       }else
       {
           $this->error("无权访问");
       }
    
    }
  
  


}
