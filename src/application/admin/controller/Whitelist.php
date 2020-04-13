<?php
namespace app\admin\controller;

use think\Request;
use think\Db;
use think\Session;

class Whitelist extends Base
{
    public function index()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称


            $userid = trim(Request::instance()->post('userid'));
            $name = trim(Request::instance()->post('name'));
            $department_id = trim(Request::instance()->post('department'));
            $del = Request::instance()->param('del');
            //echo  'del='.$del;
            if (strlen($del) > 0) {
                $map_del['org_id'] = $org_id;
                $map_del['userID'] = $del;
                Db::table('org_whitelist')
                    ->where($map_del)//必须本机构的部门
                    ->delete();
            }


            $map['o.org_id'] = $org_id;
            if (strlen($userid) > 0)
                $map['o.userID'] = ['like', '%' . $userid . '%'];
            if (strlen($name) > 0)
                $map['o.name'] = ['like', '%' . $name . '%'];
            if ($department_id != 0)//  查询条件
                $map['o.sub1_department_id'] = $department_id;
            if ($admin_role == 3) //部门职能查询部门
                $map['d.id'] = $dep_id;

            $res_white_list = Db::table('org_whitelist')
                ->alias("o")//取一个别名
                ->join('org_dep d', 'o.sub1_department_id = d.id', 'LEFT')
                //想要的字段
                ->field('o.id,o.userID,o.name,o.userID,o.gender,o.sub1_department_id,o.add_datetime,o.last_update_time,o.add_remark,d.dep_name')
                ->where($map)//必须本机构的部门
                ->order('o.sub1_department_id')
                ->select();

            $usr_count = count($res_white_list);

            $res_white_list = Db::table('org_whitelist')
                ->alias("o")//取一个别名
                ->join('org_dep d', 'o.sub1_department_id = d.id', 'LEFT')
                //想要的字段
                ->field('o.userID,o.name,o.userID,o.gender,o.sub1_department_id,o.add_datetime,o.last_update_time,o.add_remark,d.dep_name')
                ->where($map)//必须本机构的部门
                ->order('o.sub1_department_id,o.userID')
                ->limit(500)
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

            $this->assign("white_list", $res_white_list);
            $this->assign("usr_count", $usr_count);

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


            $userid = trim(Request::instance()->post('userid'));
            $name = trim(Request::instance()->post('name'));
            $department_id = trim(Request::instance()->post('department'));
            $useID = Request::instance()->param('useID');
            //echo  'del='.$del;
            if (strlen($useID) > 0) {
                $map_del['org_id'] = $org_id;
                $map_del['userID'] = $useID;

                if ($admin_role == 3) { //部门管理员只能查询部门
                    $map_dep['sub1_department_id'] = $dep_id;
                }

                $del_res = Db::table('org_whitelist')
                    ->where($map_del)//必须本机构的部门
                    ->delete();
                if ($del_res !== false) {
                    $service = new \app\admin\service\Log();
                    $service->write("6", "删除白名单成功:" . $useID);
                } else {
                    $service = new \app\admin\service\Log();
                    $service->write("6", "删除白名单失败:" . $useID);
                }
            }
            $this->redirect(url('admin/whitelist/index'));
        } else {
            $this->error("无权访问");
        }
    }


    public function memberimport()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_name = Session::get('name');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $msg = "1. 请下载模板文件，并将需要导入的白名单数据存入模板文件中；<br>2. 每次数据不超过1万条；<br>3. 点击“选择文件”按钮，选择相应的需要上传的数据文件 <br>4. 点击“导入”按钮<br><br> ";

            $dep_name = "";

            $map_dep['org_id'] = $org_id;
            $map_dep['level'] = 1;
            $map_dep['is_del'] = 0;
            if ($admin_role == 3) { //部门管理员只能查询部门
                $map_dep['id'] = $dep_id;
            }
            $res_org = Db::table('org_dep')
                ->where($map_dep)
                ->select();

            $dep_array = array();
            foreach ($res_org as $list) {
                array_push($dep_array, $list['dep_name']);
            }


            if (count($res_org) > 0 && $admin_role == 3) { //部门管理员只能导入自己部门
                $dep_name = $res_org[0]['dep_name'];
            }


            //return;

            if (!empty($_FILES)) {

                // 获取表单上传文件 例如上传了001.jpg
                $file = request()->file('dao');
                if (!empty($file)) {

                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->validate(['size' => 10240000, 'ext' => 'xls,xlsx'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if ($info) {


                        $exts = $info->getExtension();
                        $filename = '.' . '/' . 'uploads' . '/' . $info->getSaveName();
                        vendor("PHPExcel"); // 导入PHPExcel类库
                        $PHPExcel = new \PHPExcel(); // 创建PHPExcel对象，注意，不能少了\
                        if ($exts == 'xls') { // 如果excel文件后缀名为.xls，导入这个类
                            vendor("PHPExcel.PHPExcel.Reader.Excel5");
                            $PHPReader = new \PHPExcel_Reader_Excel5();
                        } else if ($exts == 'xlsx') {
                            vendor("PHPExcel.PHPExcel.Reader.Excel2007");
                            $PHPReader = new \PHPExcel_Reader_Excel2007();
                        }
                        $PHPExcel = $PHPReader->load($filename);
                        $currentSheet = $PHPExcel->getSheet(0); // 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
                        $allColumn = $currentSheet->getHighestColumn(); // 获取总列数
                        $allRow = $currentSheet->getHighestRow(); // 获取总行数
                        //echo $allRow;
                        $data = array();
                        for ($j = 1; $j <= $allRow; $j++) {
                            //从A列读取数据
                            for ($k = 'A'; $k <= $allColumn; $k++) {
                                // 读取单元格
                                $data[$j][] = $PHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
                            }
                        }


                        $total_count = $allRow - 1;
                        $import_success_count = 0;
                        $import_success_add_count = 0;
                        $import_success_update_count = 0;
                        $import_error_count = 0;
                        $last_num = 0;
                        $last_name = "";

                        for ($i = 2; $i <= $allRow; $i++) {

                            //数据库中的字段名和excel表中的字段
                            $data_p['org_id'] = $org_id;   //机构id
                            $cur_num = $PHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();;
                            $data_p['userID'] = trim($PHPExcel->getActiveSheet()->getCell("B" . $i)->getValue());  //学号
                            $data_p['name'] = trim($PHPExcel->getActiveSheet()->getCell("C" . $i)->getValue());   //姓名
                            $data_p['gender'] = trim($PHPExcel->getActiveSheet()->getCell("D" . $i)->getValue());   //性别
                            $data_p['dep_name'] = trim($PHPExcel->getActiveSheet()->getCell("E" . $i)->getValue());   //性别
                            $data_p['add_remark'] = $admin_name . "批量导入";   //机构id
                            $data_p['last_update_time'] = date("Y-m-d H:m:s");   //最后更新时间

                            //echo "姓名:";
                            //echo $data_p['name'];

                            //需要检查以上数据不能为空
                            if (strlen($data_p['userID']) < 1 || strlen($data_p['name']) < 1 || strlen($data_p['gender']) < 1 || strlen($data_p['dep_name']) < 1) {
                                $msg = $msg . "<br> 失败原因:存在空的字段数据<br><br>";
                                break;
                            }
                            //echo "开始导入";

                            if ($admin_role == 3 && $dep_name != $data_p['dep_name']) { //部门管理员只能查询部门
                                $msg = $msg . "<br> 失败原因:存在非本部门成员数据，请检查<br><br>";
                                break;
                            }


                            //先检查数组部门数组，看看数组是否在里面，如果在，则继续，如果不在则添加一个部门
                            //并将部门添加到数组中
                            $is_in_array = 0;
                            foreach ($dep_array as $value) {
                                if ($data_p['dep_name'] == $value) {
                                    $is_in_array = 1;
                                }
                            }
                            if ($is_in_array == 0) {
                                //添加数据到部门数据库
                                $msg = $msg . "<br> 本次新增部门：" . $data_p['dep_name'];
                                array_push($dep_array, $data_p['dep_name']);


                                $map_dep_where['dep_name'] = $data_p['dep_name'];
                                $map_dep_where['org_id'] = $org_id;
                                $map_dep['level'] = 1;
                                $map_dep_where['is_del'] = 0;
                                $res_dep = Db::table('org_dep')
                                    ->where($map_dep_where)
                                    ->find();


                                if (count($res_dep) == 0) {
                                    $data_new_add_dep['level'] = 1;
                                    $data_new_add_dep['dep_name'] = $data_p['dep_name'];
                                    $data_new_add_dep['org_id'] = $org_id;
                                    $data_new_add_dep['is_del'] = 0;
                                    $data_new_add_dep['remark'] = $admin_name . "系统通过Excel导入白名单时批量添加";
                                    $res_dep = Db::table('org_dep')
                                        ->insert($data_new_add_dep);
                                }

                            }

                            $map['userID'] = $data_p['userID'];
                            $map['org_id'] = $org_id;  //必须本组织的

                            //判断数据是否存在，决定新增或更新
                            $res_stu_p = Db::table('org_whitelist')
                                ->where($map)
                                ->find();
                            //var_dump($res_stu_p);
                            if ($res_stu_p > 0) {
                                $res_stu = Db::table('org_whitelist')
                                    ->where('id', '=', $res_stu_p['id'])
                                    ->update($data_p);
                                //echo $res_stu;
                                if ($res_stu === false) {
                                    $import_error_count = $import_error_count + 1;
                                } else {
                                    $import_success_count = $import_success_count + 1;
                                    $import_success_update_count = $import_success_update_count + 1;
                                    $last_num = $cur_num;
                                    $last_name = $data_p['name'];

                                }
                            } else {
                                $res_stu = Db::table('org_whitelist')
                                    ->insert($data_p);
                                if ($res_stu) {
                                    $import_success_count = $import_success_count + 1;
                                    $import_success_add_count = $import_success_add_count + 1;
                                    $last_num = $cur_num;
                                    $last_name = $data_p['name'];
                                } else {
                                    $import_error_count = $import_error_count + 1;
                                }
                            }

                        }

                        $msg = $msg . "<br><br>本次成功导入数据：" . $import_success_count . "条记录成功。其中(新增：" . $import_success_add_count . "，更新" . $import_success_update_count . ")";
                        if ($import_success_count < $total_count) {
                            $msg = $msg . "<br><br>本次成功导入数据：" . $import_success_count . "条记录成功。其中(新增：" . $import_success_add_count . "，更新" . $import_success_update_count . ")<br> 最后导入成功记录序号为:" . $last_num . "(" . $last_name . ")<br>请检查数据，并修改正确后,重新导入";
                        }
                        $service = new \app\admin\service\Log();
                        $service->write("5", "本次成功导入数据：" . $import_success_count . "条记录成功。");
                    } else {
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }
            }

            //根据Excel表格数据，自动生成部门名称


            //此部分代码，更新部门数据，此部分代码需要优化，可以在添加数据时候，自动填充这个id值
            $map_org_dep_where['org_id'] = $org_id;
            $map_org_dep_where['level'] = 1;
            $map_org_dep_where['is_del'] = 0;
            $res_org_dep_list = Db::table('org_dep')
                ->where($map_org_dep_where)
                ->select();
            if (count($res_org_dep_list) > 0) {
                foreach ($res_org_dep_list as $list) {
                    $map_org_dep_up['org_id'] = $org_id;  //必须本机构的部门
                    $map_org_dep_up['dep_name'] = $list['dep_name'];  //必须本机构的部门
                    $data_dep['sub1_department_id'] = $list['id'];
                    Db::table('org_whitelist')
                        ->where($map_org_dep_up)
                        ->update($data_dep);
                }

            }
            //以上代码会更新部门信息


            $this->assign("msg", $msg);
            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function memberadd()
    {
        if (2 == Session::get('role') || 3 == Session::get('role')) {

            $admin_uid = Session::get('userid');  //用户id
            $admin_name = Session::get('name');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $add = trim(Request::instance()->post('add'));

            if ($add == 1) {
                $userid = trim(Request::instance()->post('userid'));
                $name = trim(Request::instance()->post('username'));
                $gender = trim(Request::instance()->post('gender'));
                $department = trim(Request::instance()->post('department'));

                //echo $name."-";echo $add."-";echo $userid."-"; echo $name."-";echo $gender."-"; echo $department;

                $data['userID'] = $userid;  //学号
                $data['name'] = $name;   //姓名
                $data['gender'] = $gender;   //性别
                $data['org_id'] = $org_id;   //机构id
                $data['add_remark'] = $admin_name . "手动添加";   //机构id
                $data['last_update_time'] = date("Y-m-d H:m:s");   //最后更新时间

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
                        $data['sub1_department_id'] = $department; // 院系

                        $map['userID'] = $userid;
                        $map['org_id'] = $org_id;  //必须本组织的院系
                        //判断数据是否存在，决定新增或更新
                        $res_stu = Db::table('org_whitelist')
                            ->where($map)
                            ->find();
                        if ($res_stu > 0) {
                            Db::table('org_whitelist')
                                ->where('id', '=', $res_stu['id'])
                                ->update($data);
                        } else {
                            Db::table('org_whitelist')
                                ->insert($data);
                        }

                        $this->assign("msg", $userid . "(" . $name . ")添加成功！");
                        $service = new \app\admin\service\Log();
                        $service->write("4", $userid . "(" . $name . ")添加成功！");
                    } else {
                        $this->assign("msg", $userid . "(" . $name . ")添加失败！");
                        $service = new \app\admin\service\Log();
                        $service->write("4", $userid . "(" . $name . ")添加失败！");
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
}
