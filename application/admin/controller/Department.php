<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;

class Department extends Base
{
    public function index()
    {
      
        $admin_uid = Session::get('userid');  //用户id
        $admin_role = Session::get('role'); //  管理员角色
        $org_id = Session::get('org_id'); //   机构id
        $org_name = Session::get('org_name'); // 机构名称
        $dep_id = Session::get('dep_id'); // 机构名称
        
      
        $map_dep['org_id'] = $org_id;
        $map_dep['level'] = 1;
        $map_dep['is_del'] = 0;
        if($admin_role == 3) //部门职能查询部门
            $map_dep['id'] = $dep_id;
        $res_org = Db::table('org_dep')
            ->where($map_dep)
            ->order('dep_name')
            ->select();

        $this->assign("msg", "开发中");
      
        $this->assign("dep_list", $res_org);
        return  $this->fetch();
    }
  
  
    public function add()
    {   
        $admin_uid = Session::get('userid');  //用户id
        $admin_role = Session::get('role'); //  管理员角色
        $admin_name = Session::get('name'); //  管理员角色
        $org_id = Session::get('org_id'); //   机构id
        $org_name = Session::get('org_name'); // 机构名称
        $dep_id = Session::get('dep_id'); // 机构名称
      
        $add_dep_name = Request::instance()->post('add_dep_name');
        $msg="";
        if($add_dep_name ==1){
            $dep_name = trim(Request::instance()->post('dep_name'));
             
            if(count($dep_name)<2){
              $map_dep_where['dep_name'] = $dep_name;
              $map_dep_where['org_id'] = $org_id;
              $map_dep_where['level'] = 1;
              $map_dep_where['is_del'] = 0;
              $res_dep = Db::table('org_dep')
                          ->where($map_dep_where)
                          ->find();
              if(count($res_dep)>0)
              {
                  $msg="部门名称重名，添加失败";
              }else{
                 $data['level']=1;
                 $data['dep_name']=$dep_name;
                 $data['org_id']=$org_id;
                 $data['is_del']=0;
                 $data['remark']=$admin_name."手动添加";
                 $res_dep = Db::table('org_dep')
                          ->insert($data);
                 if($res_dep !==false){
                 	$msg="添加成功";
                 }else{
                 	$msg="添加失败";
                 }   
              }
            }else{
               $msg="部门名称不能少于2个字符";
            }
             
        }
        $this->assign("msg", $msg);
        return  $this->fetch();
    }

  
    
}
