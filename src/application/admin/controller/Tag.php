<?php
namespace app\admin\controller;

use think\Request;
use think\Db;
use think\Session;

class Tag extends Base
{
    public function index()
    {
        if (2 == Session::get('role') ||3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称


            $map_tag['t.org_id'] = $org_id;
            $map_tag['t.dep_id'] = 0;//缺省查看全局的标签
            $map_tag['t.is_del'] = 0;

            if($admin_role == 3){
                $map_tag['t.dep_id'] = $dep_id;
            }


            $res_tag = Db::table('org_tag')
                ->alias("t")//取一个别名
                ->join('org_dep d', 't.dep_id = d.id', 'LEFT')
                //想要的字段
                ->field('t.id,t.name,d.dep_name,d.id as dep_id,t.datetime')
                ->where($map_tag)//必须本机构的部门
                ->order('t.name')
                ->select();


            //var_dump($res_tag);
            $this->assign("msg", "开发中");

            $this->assign("tag_list", $res_tag);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function setadmin()
    {
        if (2 == Session::get('role') ||3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称


            $map_tag['t.org_id'] = $org_id;
            if ($dep_id != 0) {
                $map_tag['t.dep_id'] = $dep_id;
            }
            $map_tag['t.is_del'] = 0;


            $res_tag = Db::table('org_tag')
                ->alias("t")//取一个别名
                ->join('org_dep d', 't.dep_id = d.id', 'LEFT')
                //想要的字段
                ->field('t.name,d.dep_name,t.datetime')
                ->where($map_tag)//必须本机构的部门
                ->order('t.name')
                ->select();


            //var_dump($res_tag);
            $this->assign("msg", "开发中");

            $this->assign("tag_list", $res_tag);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }


    public function add()
    {
        if (2 == Session::get('role') ||3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $admin_name = Session::get('name'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id');

            $add_tag_name = trim(Request::instance()->post('add_tag_name'));
            $msg = "";
            if ($add_tag_name == 1) {
                $tag_name = trim(Request::instance()->post('tag_name'));

                if (count($tag_name) < 2) {
                    $map_tag_where['name'] = $tag_name;
                    $map_tag_where['org_id'] = $org_id;
                    $map_tag_where['dep_id'] = $dep_id;
                    $map_tag_where['is_del'] = 0;
                    $res_tag = Db::table('org_tag')
                        ->where($map_tag_where)
                        ->find();
                    if (count($res_tag) > 0) {
                        $msg = "名称重名，添加失败";
                    } else {
                        $data['name'] = $tag_name;
                        $data['org_id'] = $org_id;
                        $data['dep_id'] = $dep_id;
                        $data['is_del'] = 0;
                        $data['remark'] = $admin_name . "手动添加";
                        $res_tag = Db::table('org_tag')
                            ->insert($data);
                        if ($res_tag !== false) {
                            $msg = "添加成功";
                        } else {
                            $msg = "添加失败";
                        }
                    }
                } else {
                    $msg = "标签名称不能少于2个字符";
                }

            }
            $this->assign("msg", $msg);
            return $this->fetch();
        }else{
            $this->error("无权访问");
        }
    }

    public function edit()
    {
        if (2 == Session::get('role') ||3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $admin_name = Session::get('name'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id');



            $msg = "请输入新的信息";

            $is_edit = Request::instance()->param('edit');
            $edit_tag_id = Request::instance()->param('id');
            $edit_tag_name = trim(Request::instance()->post('tag_name'));

            //检测编辑数据
            if ($is_edit == 1) {
                if (strlen($edit_tag_name) < 2) {
                    $this->error("参数错误");
                }
                //构造查询条件
                $map_edit['id'] = $edit_tag_id;
                $map_edit['org_id'] = $org_id;
                $map_edit['is_del'] = 0;
                if($admin_role  ==3)
                    $map_edit['dep_id'] = $dep_id; //本部门管理员,仅可以修改本部门的数据
                //需要修改的数据
                $data['name'] = $edit_tag_name;


                $edit_res = Db::table('org_tag')
                    ->where($map_edit)
                    ->update($data);
                if ($edit_res !== false) {
                    $msg = "更新成功";
                    $service = new \app\admin\service\Log();
                    $service->write("11", "修改标签失败:" . $edit_tag_name);
                } else {
                    $msg = "更新失败";
                    $service = new \app\admin\service\Log();
                    $service->write("11", "修改标签失败:" . $edit_tag_name);
                }

            }


            $map_edit['id'] = $edit_tag_id;
            $map_edit['org_id'] = $org_id;
            $map_edit['is_del'] = 0;
            if($admin_role  ==3)
                $map_edit['dep_id'] = $dep_id; //本部门管理员,仅可以修改本部门的数据
            $edit_res = Db::table('org_tag')
                ->where($map_edit)
                ->find();
            if (empty($edit_res)) {
                $this->error("参数错误");
            } else {
                $edit_id = $edit_res['id'];
                $edit_name = $edit_res['name'];
            }


            $this->assign("msg", $msg);
            $this->assign("id", $edit_id);
            $this->assign("name", $edit_name);
            return $this->fetch();
        }else{
            $this->error("无权访问");
        }
    }


}
