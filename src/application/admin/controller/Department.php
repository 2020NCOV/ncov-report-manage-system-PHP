<?php
namespace app\admin\controller;

use think\Request;
use think\Db;
use think\Session;

class Department extends Base
{
    public function index()
    {
        if (2 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称


            $map_dep['org_id'] = $org_id;
            $map_dep['level'] = 1;
            $map_dep['is_del'] = 0;
            if ($admin_role == 3) //部门职能查询部门
                $map_dep['id'] = $dep_id;
            $res_org = Db::table('org_dep')
                ->where($map_dep)
                ->order('dep_name')
                ->select();

            $this->assign("msg", "开发中");

            $this->assign("dep_list", $res_org);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }


    public function add()
    {
        if (2 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $admin_name = Session::get('name'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $add_dep_name = Request::instance()->post('add_dep_name');
            $msg = "";
            if ($add_dep_name == 1) {
                $dep_name = trim(Request::instance()->post('dep_name'));

                if (count($dep_name) < 2) {
                    $map_dep_where['dep_name'] = $dep_name;
                    $map_dep_where['org_id'] = $org_id;
                    $map_dep_where['level'] = 1;
                    $map_dep_where['is_del'] = 0;
                    $res_dep = Db::table('org_dep')
                        ->where($map_dep_where)
                        ->find();
                    if (count($res_dep) > 0) {
                        $msg = "部门名称重名，添加失败";
                    } else {
                        $data['level'] = 1;
                        $data['dep_name'] = $dep_name;
                        $data['org_id'] = $org_id;
                        $data['is_del'] = 0;
                        $data['remark'] = $admin_name . "手动添加";
                        $res_dep = Db::table('org_dep')
                            ->insert($data);
                        if ($res_dep !== false) {
                            $msg = "添加成功!     可继续添加!";
                            $service = new \app\admin\service\Log();
                            $service->write("7", "添加部门成功:" . $dep_name);
                        } else {
                            $msg = "添加失败";
                            $service = new \app\admin\service\Log();
                            $service->write("7", "添加部门失败:" . $dep_name);
                        }
                    }
                } else {
                    $msg = "部门名称不能少于2个字符";
                }

            }
            $this->assign("msg", $msg);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function edit()
    {
        if (2 == Session::get('role')) {

            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $msg = "请输入新的信息";

            $is_edit = Request::instance()->param('edit');
            $edit_dep_id = Request::instance()->param('id');
            $edit_dep_name = Request::instance()->post('dep_name');

            //检测编辑数据
            if ($is_edit == 1) {
                if (strlen($edit_dep_name) < 2) {
                    $this->error("参数错误");
                }
                $map_edit['id'] = $edit_dep_id;
                $map_edit['org_id'] = $org_id;
                $data['dep_name'] = $edit_dep_name;
                $edit_res = Db::table('org_dep')
                    ->where($map_edit)
                    ->update($data);
                if ($edit_res !== false) {
                    $msg = "更新成功";
                    $service = new \app\admin\service\Log();
                    $service->write("8", "修改部门失败:" . $edit_dep_name);
                } else {
                    $msg = "更新失败";
                    $service = new \app\admin\service\Log();
                    $service->write("8", "修改部门失败:" . $edit_dep_name);
                }

            }


            $map_edit['id'] = $edit_dep_id;
            $map_edit['org_id'] = $org_id;
            $edit_res = Db::table('org_dep')
                ->where($map_edit)
                ->find();
            if (empty($edit_res)) {
                $this->error("参数错误");
            } else {
                $edit_id = $edit_res['id'];
                $edit_name = $edit_res['dep_name'];
            }


            $this->assign("msg", $msg);
            $this->assign("id", $edit_id);
            $this->assign("name", $edit_name);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

}
