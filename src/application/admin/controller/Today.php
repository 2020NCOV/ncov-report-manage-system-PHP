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


        $userid = trim(Request::instance()->post('userid'));
        $name = trim(Request::instance()->post('name'));
        $department_id = trim(Request::instance()->post('department'));


       if( 2 ==Session::get('role') || 3 ==Session::get('role')){

           
           $msg = "今日未提交";
           $today = date('Y-m-d');
           $today1 = date('Y-m-d',strtotime('+1 day'));

           $map['o.org_id'] = $org_id;
           if (strlen($userid) > 0)
               $map['o.userID'] = ['like', '%' . $userid . '%'];
           if (strlen($name) > 0)
               $map['o.name'] = ['like', '%' . $name . '%'];
           if ($department_id != 0)//  查询条件
               $map['o.sub1_department_id'] = $department_id;
           if ($admin_role == 3) //部门职能查询部门
               $map['d.id'] = $dep_id;
           
           $where_str = " o.report_date NOT BETWEEN '".$today."' AND '".$today1."'" ;
         

           //echo $where_str;
          
         
          // $UserData = Db::table('org_whitelist')
          //     ->where($where_str)
          //     ->order('userID')
           //    ->select();
          // echo  DB::getLastSQL();
           $UserData = Db::table('org_whitelist')
                ->alias("o")//取一个别名
                ->join('org_dep d', 'o.sub1_department_id = d.id', 'LEFT')
                //想要的字段
                ->field('o.id,o.userID,o.name,o.userID,o.gender,o.sub1_department_id,o.report_date,o.add_datetime,o.last_update_time,o.add_remark,d.dep_name')
                ->where($where_str)
                ->where($map)//必须本机构的部门
                ->order('o.sub1_department_id')
                ->limit(500)
                ->select();

           $UserData_count = Db::table('org_whitelist')
               ->alias("o")//取一个别名
               ->join('org_dep d', 'o.sub1_department_id = d.id', 'LEFT')
               //想要的字段
               ->field('o.id,o.userID,o.name,o.userID,o.gender,o.sub1_department_id,o.report_date,o.add_datetime,o.last_update_time,o.add_remark,d.dep_name')
               ->where($where_str)
               ->where($map)//必须本机构的部门
               ->order('o.sub1_department_id')
               ->select();


           $map_dep['org_id'] = $org_id;
           $map_dep['level'] = 1;
           $map_dep['is_del'] = 0;
           if ($admin_role == 3) //部门职能查询部门
               $map_dep['id'] = $dep_id;
           $res_org = Db::table('org_dep')
               ->where($map_dep)
               ->select();
           //var_dump($res_org);
           $this->assign("dep_list", $res_org);


           $count= count($UserData_count);
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
