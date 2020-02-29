<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;

class Administrator extends Base
{
    public function index()
    {
      
       $admin_uid = Session::get('userid');  //用户id
       $admin_role = Session::get('role'); //  管理员角色
       $org_id = Session::get('org_id'); //   机构id
       $org_name = Session::get('org_name'); // 机构名称
       $dep_id = Session::get('dep_id'); // 机构名称
      

       $map_admin['a.org_id'] = $org_id;
  	   if($admin_role == 3) //部门只能查询本部门
            $map_admin['a.dep_id'] = $dep_id;
       $map_admin['a.is_del'] = 0;
      
       $res_admin_list = Db::table('admin_user')
         	->alias("a") //取一个别名
      		->join('org_dep d', 'a.dep_id = d.id','LEFT')
            ->join('admin_role r', 'a.role = r.id','LEFT')
      		//想要的字段
      		->field('a.username,a.name,a.role,d.dep_name,r.name as role_name')
            ->where($map_admin)  //必须本机构的部门 
            ->order('a.dep_id')
            ->select();
      
       $admin_count = count($res_admin_list);
      
      
      
      
      //列出所有部门
      	$map_dep['org_id'] = $org_id;
        $map_dep['level'] = 1;
        $map_dep['is_del'] = 0;
        if($admin_role == 3) //部门职能查询部门
            $map_dep['id'] = $dep_id;
        $res_org = Db::table('org_dep')
            ->where($map_dep)
            ->select();
        //var_dump($res_org);
        $this->assign("dep_list", $res_org);
      
       $this->assign("admin_list", $res_admin_list);
       $this->assign("usr_count", $admin_count);
      
       return  $this->fetch();
    }
  
  	
  
    
}
