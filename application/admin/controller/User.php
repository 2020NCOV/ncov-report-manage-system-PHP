<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;

class User extends Base
{
    public function unbind()
    {
      
       $admin_uid = Session::get('userid');  //用户id
       $admin_name = Session::get('name');  //用户id
       $admin_role = Session::get('role'); //  管理员角色
       $org_id = Session::get('org_id'); //   机构id
       $org_name = Session::get('org_name'); // 机构名称
       $dep_id = Session::get('dep_id'); // 机构名称
       
       $user_list = '';
      
       $msg = "";
      
       $unbind_username = Request::instance()->param('unbind_username');
       //echo  'del='.$del;
       if(strlen($unbind_username) >0){
           $map_unbind['username'] = $unbind_username;
           $map_unbind['dep_id'] = $org_id;  //必须本机构的部门 
           $map_unbind['isbind'] = 1;
         
           $map_data['isbind'] = 0;
           $map_data['remark'] = $admin_name."解绑";
           $map_data['unbind_date'] = date('Y-m-d H:i:s');
           Db::table('er_wx_department_bind_user')
            ->where($map_unbind)  
            ->update($map_data);
           
           $msg = $unbind_username."解绑成功！";
         
           
       }
      
       
       $userid = Request::instance()->post('userid');
       $unbind = Request::instance()->post('unbind');
       $map_unbind['username'] = ['like','%'.$userid.'%'];
       $map_unbind['dep_id'] = $org_id;
       $map_unbind['isbind'] = 1;
       $res_org_unbind = Db::table('er_wx_department_bind_user')
            ->alias("u") //取一个别名
            ->join('er_user_student_weixin w', 'w.wid = u.wx_uid','LEFT')
      		//想要的字段
      		->field('u.username,u.bind_date,w.name,w.userID,w.phone_num')
            ->where($map_unbind)
            ->limit(100)
            ->select();
      
       if(count($res_org_unbind)>0){
           $user_list = $res_org_unbind;
          
       }
      
       $this->assign("user_list", $res_org_unbind);
       $this->assign("msg", $msg);
      
       return  $this->fetch();
    }
  
  
  
    
}
