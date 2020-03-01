<?php
namespace app\admin\controller;

use think\Request;
use think\Db;
use think\Session;

class Administrator extends Base
{
    public function index()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $userid = Request::instance()->post('username');
            $name = Request::instance()->post('name');
            $department_id = Request::instance()->post('department');


            $map_admin['a.org_id'] = $org_id;

            $map_admin['a.is_del'] = 0;
            $map_admin['a.role'] = 3;//只列出一级机构

            if (strlen($userid) > 0)
                $map_admin['a.username'] = ['like', '%' . $userid . '%'];
            if (strlen($name) > 0)
                $map_admin['a.name'] = ['like', '%' . $name . '%'];
            if ($department_id != 0)//  查询条件
                $map_admin['a.dep_id'] = $department_id;
            if ($admin_role == 3) //部门只能查询本部门
                $map_admin['a.dep_id'] = $dep_id;

            $res_admin_list = Db::table('admin_user')
                ->alias("a")//取一个别名
                ->join('org_dep d', 'a.dep_id = d.id', 'LEFT')
                ->join('admin_role r', 'a.role = r.id', 'LEFT')
                //想要的字段
                ->field('a.id,a.username,a.name,a.role,d.dep_name,r.name as role_name')
                ->where($map_admin)//必须本机构的部门
                ->order('a.dep_id')
                ->select();

            $admin_count = count($res_admin_list);


            //列出所有部门
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

            $this->assign("admin_list", $res_admin_list);
            $this->assign("usr_count", $admin_count);

            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function add()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {

            $secure_code = "沧海猎人";
            $admin_uid = Session::get('userid');  //用户id
            $admin_name = Session::get('name');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 部门名称

            $add = Request::instance()->post('add');

            if ($add == 1) {
                $username = Request::instance()->post('user_name');
                $name = Request::instance()->post('name');
                $passwd = Request::instance()->post('passwd');
                $department = Request::instance()->post('department');

                //echo $name."-";echo $add."-";echo $username."-"; echo $name."-";echo $passwd."-"; echo $department;

                if (preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $passwd) == 0) {
                    $this->error("密码必须为字母和数字组合,长度不能小于8位");
                }
                if (strlen($passwd) < 8) {
                    $this->error("密码必须为字母和数字组合,长度不能小于8位");
                }

                if (strlen($username) < 10) {
                    $this->error("用户名数据格式不正确");
                }
                if (strlen($name) < 1) {
                    $this->error("姓名数据格式不正确");
                }

                $md5password = md5($secure_code . md5($passwd));

                if (3 == Session::get('role')) {//本部门只能添加本部门的数据
                    $department = $dep_id;   //机构id
                }


                $data['username'] = $username;  //学号
                $data['name'] = $name;   //姓名
                $data['password'] = $md5password;   //性别
                $data['org_id'] = $org_id;   //机构id
                $data['role'] = 3;   //一级部门管理员
                $data['dep_id'] = $department;   //机构id
                $data['remarks'] = $admin_name . "手动添加";   //机构id
                $data['is_del'] = 0;   //机构id



                //权限检查开始
                if ($department == '0') {
                    $this->assign("msg", "请选择院系！");
                } else {
                    $map_dep_where['org_id'] = $org_id;
                    $map_dep_where['level'] = 1;
                    $map_dep_where['is_del'] = 0;
                    $map_dep_where['id'] = $department;
                    $res_org_list = Db::table('org_dep')
                        ->field('id', 'dep_name')
                        ->where($map_dep_where)
                        ->find();
                    if (count($res_org_list) > 0) {
                        //权限检查结束
                        $map['username'] = $username;
                        //判断数据是否存在，决定新增或更新
                        $res_admin = Db::table('admin_user')
                            ->where($map)
                            ->find();
                        if ($res_admin > 0) {
                            if($res_admin['is_del'] == 0){
                                $this->assign("msg", '用户名不可用,' . $username . "(" . $name . ")添加失败！");
                            }else{
                            Db::table('admin_user')
                                ->where('username',"=",$username)
                                ->update($data);
                            $this->assign("msg", "用户名:" . $username . " 姓名:" . $name . ";密码:" . $passwd . ";部门名称:" . $res_org_list['dep_name'] . ";<br>上面成功信息,便于您复制后发给相关人员<br>添加成功,可继续添加！");
                            $service = new \app\admin\service\Log();
                            $service->write("9", $username . "(" . $name . ")更新成功1！");
                            }
                        } else {
                            Db::table('admin_user')
                                ->insert($data);
                            $this->assign("msg", "用户名:" . $username . " 姓名:" . $name . ";密码:" . $passwd . ";部门名称:" . $res_org_list['dep_name'] . ";<br>上面成功信息,便于您复制后发给相关人员<br>添加成功,可继续添加！");
                            $service = new \app\admin\service\Log();
                            $service->write("9", $username . "(" . $name . ")添加成功！");
                        }
                    } else {
                        $this->assign("msg", '部门选择有误,' . $username . "(" . $name . ")添加失败！");
                        $service = new \app\admin\service\Log();
                        $service->write("9", '部门选择有误,' . $username . "(" . $name . ")添加失败！");
                    }
                }
            } else {
                $this->assign("msg", "");
            }

            if ($admin_role == 3) //部门职能查询部门
                $map_dep['id'] = $dep_id;

            $map_dep['org_id'] = $org_id;
            $map_dep['level'] = 1;
            $map_dep['is_del'] = 0;
            $res_org = Db::table('org_dep')
                ->where($map_dep)
                ->select();
            //var_dump($res_org);
            $this->assign("dep_list", $res_org);

            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function del()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称


            $userid = Request::instance()->post('userid');
            $name = Request::instance()->post('name');
            $department_id = Request::instance()->post('department');
            $del_id = Request::instance()->param('id');

            $admin_res = Db::table('admin_user')
                ->where('id', '=', $del_id)//必须本机构的部门
                ->find();
            //echo  'del='.$del;
            if (strlen($del_id) > 0) {
                $map_del['org_id'] = $org_id;
                $map_del['id'] = $del_id;

                if ($admin_role == 3) { //部门管理员只能查询部门
                    $map_dep['dep_id'] = $dep_id;
                }
                $data['is_del'] = 1;
                $del_res = Db::table('admin_user')
                    ->where($map_del)//必须本机构的部门
                    ->update($data);
                if ($del_res !== false) {
                    $service = new \app\admin\service\Log();
                    $service->write("6", "删除管理员账号成功:" . $admin_res['username']);
                } else {
                    $service = new \app\admin\service\Log();
                    $service->write("6", "删除管理员失败:" . $admin_res['username']);
                }
            }
            $this->redirect(url('admin/administrator/index'));
        } else {
            $this->error("无权访问");
        }
    }


    public function edit()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {
            $secure_code = "沧海猎人";
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $msg = "请输入新的信息!";

            $is_edit = Request::instance()->param('edit');
            $edit_admin_id = Request::instance()->param('id');

            $name = trim(Request::instance()->post('name'));
            $passwd = trim(Request::instance()->post('passwd'));
            $department = Request::instance()->post('department');


            if (3 == Session::get('role')) {//本部门只能添加本部门的数据
                $department = $dep_id;   //机构id
            }

            //检测编辑数据
            if ($is_edit == 1) {
                if (strlen($name) < 1) {
                    $this->error("姓名数据格式不正确");
                }
                $data['name'] = $name;   //姓名

                if (strlen($passwd) > 0) {
                    if (preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $passwd) == 0) {
                        $this->error("密码必须为字母和数字组合,长度不能小于8位");
                    }
                    if (strlen($passwd) < 0) {
                        $this->error("密码必须为字母和数字组合,长度不能小于8位");
                    }

                    $md5password = md5($secure_code . md5($passwd));
                    $data['password'] = $md5password;   //性别
                }
                $data['dep_id'] = $department;   //机构id

                $data_where['id'] = $edit_admin_id;   //id
                $data_where['org_id'] = $org_id;   //id
                $data_where['role'] = 3;   //一级部门管理员

                //权限检查开始
                if ($department == '0') {
                    $this->assign("msg", "请选择院系！");
                } else {
                    $map_dep_where['org_id'] = $org_id;
                    $map_dep_where['level'] = 1;
                    $map_dep_where['is_del'] = 0;
                    $map_dep_where['id'] = $department;
                    $res_org_list = Db::table('org_dep')
                        ->field('id', 'dep_name')
                        ->where($map_dep_where)
                        ->find();
                    if (count($res_org_list) > 0) {
                        //权限检查结束

                        Db::table('admin_user')
                            ->where($data_where)
                            ->update($data);
                        //echo DB::getLastSQL();
                        $msg = " 姓名:" . $name . ";部门名称:" . $res_org_list['dep_name'] . ";<br>更新成功！";
                        $service = new \app\admin\service\Log();
                        $service->write("10", "(" . $name . ")更新成功！");
                    }else {
                        $msg = '部门选择有误,' . "(" . $name . ")更新失败！";
                        $service = new \app\admin\service\Log();
                        $service->write("10", '部门选择有误,' . "(" . $name . ")更新失败！");
                    }
                }
            }


            $map_edit['id'] = $edit_admin_id;
            $map_edit['org_id'] = $org_id;
            $map_edit['is_del'] = 0;
            $edit_res = Db::table('admin_user')
                ->where($map_edit)
                ->find();
            if (empty($edit_res)) {
                $this->error("参数错误");
            } else {
                $edit_id = $edit_res['id'];
                $edit_username = $edit_res['username'];
                $edit_name = $edit_res['name'];
                $edit_dep_id = $edit_res['dep_id'];
            }


            if ($admin_role == 3) //部门职能查询部门
                $map_dep['id'] = $dep_id;

            $map_dep['org_id'] = $org_id;
            $map_dep['level'] = 1;
            $map_dep['is_del'] = 0;
            $res_org = Db::table('org_dep')
                ->where($map_dep)
                ->select();
            //var_dump($res_org);
            $this->assign("dep_list", $res_org);

            $this->assign("msg", $msg);
            $this->assign("id", $edit_id);
            $this->assign("name", $edit_name);
            $this->assign("username", $edit_username);
            $this->assign("dep_id", $edit_dep_id);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }


}
