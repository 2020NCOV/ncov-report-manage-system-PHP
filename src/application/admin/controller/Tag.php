<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;

class Tag extends Base
{
    public function index()
    {
      
        $admin_uid = Session::get('userid');  //用户id
        $admin_role = Session::get('role'); //  管理员角色
        $org_id = Session::get('org_id'); //   机构id
        $org_name = Session::get('org_name'); // 机构名称
        $dep_id = Session::get('dep_id'); // 机构名称
        
      
        $map_org['org_id'] = $org_id;
        if($dep_id != 0 )
        {
          $map_org['dep_id'] = $dep_id;
        }
        $map_org['is_del'] = 0;
        if($admin_role == 3) //部门只能查询本部门
            $map_org['id'] = $dep_id;
        $res_tag = Db::table('org_tag')
            ->where($map_org)
            ->order('name')
            ->select();

        $this->assign("msg", "开发中");
      
        $this->assign("tag_list", $res_tag);
        return  $this->fetch();
    }
  
  
    public function add()
    {   
        $admin_uid = Session::get('userid');  //用户id
        $admin_role = Session::get('role'); //  管理员角色
        $admin_name = Session::get('name'); //  管理员角色
        $org_id = Session::get('org_id'); //   机构id
        $org_name = Session::get('org_name'); // 机构名称
        $dep_id = Session::get('dep_id'); 
      
        $add_tag_name = Request::instance()->post('add_tag_name');
        $msg="";
        if($add_tag_name ==1){
            $tag_name = trim(Request::instance()->post('tag_name'));
             
            if(count($tag_name)<2){
              $map_tag_where['name'] = $tag_name;
              $map_tag_where['org_id'] = $org_id;
              $map_tag_where['dep_id'] = $dep_id;
              $map_tag_where['is_del'] = 0;
              $res_tag = Db::table('org_tag')
                          ->where($map_tag_where)
                          ->find();
              if(count($res_tag)>0)
              {
                  $msg="名称重名，添加失败";
              }else{
                 $data['name']=$tag_name;
                 $data['org_id']=$org_id;
                 $data['dep_id']=$dep_id;
                 $data['is_del']=0;
                 $data['remark']=$admin_name."手动添加";
                 $res_tag = Db::table('org_tag')
                          ->insert($data);
                 if($res_tag !==false){
                 	$msg="添加成功";
                 }else{
                 	$msg="添加失败";
                 }   
              }
            }else{
               $msg="标签名称不能少于2个字符";
            }
             
        }
        $this->assign("msg", $msg);
        return  $this->fetch();
    }

  
    
}
