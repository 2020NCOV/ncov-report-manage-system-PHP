<?php
namespace app\superadmin2020\controller;
use think\Request;
use think\Db;
use think\Session;
use think\Config;
class Organization extends Base
{
    public function index()
    {
        if (1 == Session::get('role') ) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_name = Session::get('name');  //用户id
            $admin_role = Session::get('role'); //  管理员角色

            $map['access_type'] = 'mp' ;

            $res_org_list = Db::table('organization')
                ->where($map)
                ->select();

            $org_count = count($res_org_list);


            $this->assign("org_count", $org_count);
            $this->assign("org_list", $res_org_list);

            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }
    public function edit()
    {
        if (1 == Session::get('role')) {

            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 机构名称

            $msg = "请输入新的信息";

            $is_edit = Request::instance()->param('edit');
            $edit_org_id = Request::instance()->param('id');

            //检测编辑数据
            if ($is_edit == 1) {

                $corpname = trim(Request::instance()->post('corpname'));
                $corpname_full = trim(Request::instance()->post('corpname_full'));
                $type_corpname = trim(Request::instance()->post('type_corpname'));
                $type_username = trim(Request::instance()->post('type_username'));
                $remark = trim(Request::instance()->post('remark'));

                if (strlen($corpname) < 2) {
                    $this->error("参数错误corpname");
                }
                if (strlen($corpname_full) < 2) {
                    $this->error("参数错误corpname_full");
                }

                if (strlen($type_corpname) < 2) {
                    $this->error("参数错误type_corpname");
                }
                if (strlen($type_username) < 2) {
                    $this->error("参数错误type_username");
                }



                $data['corpname'] = $corpname;
                $data['corpname_full'] = $corpname_full;
                $data['type_corpname'] = $type_corpname;
                $data['type_username'] = $type_username;
                $data['remark'] = $remark;


                $map_edit['id'] = $edit_org_id;
                $edit_res = Db::table('organization')
                    ->where($map_edit)
                    ->update($data);
                if ($edit_res !== false) {
                    $msg = "更新成功";
                } else {
                    $msg = "更新失败";
                }

            }


            $edit_id = 0;
            $edit_name = '';
            $edit_corpname = '';
            $edit_corpname_full = '';
            $edit_type_corpname = '';
            $edit_type_username = '';
            $edit_remark = '';

            $map_edit['id'] = $edit_org_id;
            $edit_res = Db::table('organization')
                ->where($map_edit)
                ->find();
            if (empty($edit_res)) {
                $this->error("参数错误");
            } else {
                $edit_id = $edit_res['id'];
                $edit_name = $edit_res['corpname'];
                $edit_corpname = $edit_res['corpname'];
                $edit_corpname_full = $edit_res['corpname_full'];
                $edit_type_corpname = $edit_res['type_corpname'];
                $edit_type_username = $edit_res['type_username'];
                $edit_remark = $edit_res['remark'];
            }


            $this->assign("msg", $msg);
            $this->assign("id", $edit_id);
            $this->assign("name", $edit_name);
            $this->assign("corpname", $edit_corpname);
            $this->assign("corpname_full", $edit_corpname_full);
            $this->assign("type_corpname", $edit_type_corpname);
            $this->assign("type_username", $edit_type_username);
            $this->assign("remark", $edit_remark);

            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function addorg()
    {
        if (1 == Session::get('role')) {

            $admin_uid = Session::get('userid');  //用户id
            $admin_name = Session::get('name');  //用户id
            $admin_role = Session::get('role'); //  管理员角色

            $add = Request::instance()->post('add');

            if ($add == 1) {
                $corpname = trim(Request::instance()->post('corpname'));
                $corpname_full = trim(Request::instance()->post('corpname_full'));
                $corp_code = trim(Request::instance()->post('corp_code'));
                $type_corpname = trim(Request::instance()->post('type_corpname'));
                $type_username = trim(Request::instance()->post('type_username'));
                $username = trim(Request::instance()->post('username'));
                $orgadminname = trim(Request::instance()->post('adminname'));
                $template_code = trim(Request::instance()->post('template_code'));

                if (strlen($corpname) < 2) {
                    $this->error("参数错误corpname");
                }
                if (strlen($corpname_full) < 2) {
                    $this->error("参数错误corpname_full");
                }
                if (strlen($corp_code) < 8) {
                    $this->error("参数错误corp_code");
                }
                if (strlen($type_corpname) < 2) {
                    $this->error("参数错误type_corpname");
                }
                if (strlen($type_username) < 2) {
                    $this->error("参数错误type_username");
                }
                if (strlen($username) < 2) {
                    $this->error("参数错误username");
                }
                if (strlen($orgadminname) < 2) {
                    $this->error("参数错误adminname");
                }

                if (strlen($template_code) < 2) {
                    $this->error("参数错误$template_code");
                }


                $data['corpname'] = $corpname;
                $data['corpname_full'] = $corpname_full;
                $data['corp_code'] = $corp_code;
                $data['type_corpname'] = $type_corpname;
                $data['type_username'] = $type_username;
                $data['tel'] = $username;
                $data['admin_name'] = $orgadminname;
                $data['template_code'] = $template_code;
                $data['access_type'] = 'mp';


                //检测机构是否已开通
                $map_org_code['corp_code'] = $corp_code;
                $res_org_list = Db::table('organization')
                    ->where($map_org_code)
                    ->find();
                if (count($res_org_list) > 0) {
                    $this->error("该编号已存在");
                }
                //检测机构是否已开通
                $map_org_name['corpname'] = $corpname;
                $res_org_list = Db::table('organization')
                    ->where($map_org_name)
                    ->find();
                if (count($res_org_list) > 0) {
                    $this->error("名称已存在");
                }
                //检测机构是否已开通
                $map_org_full['corpname_full'] = $corpname_full;
                $res_org_list = Db::table('organization')
                    ->where($map_org_full)
                    ->find();
                if (count($res_org_list) > 0) {
                    $this->error("该机构名称已存在");
                }

                //检测手机号是否已经被占用
                $map_admin['username'] = $username;
                $res_admin_list = Db::table('admin_user')
                    ->where($map_admin)
                    ->find();
                if (count($res_admin_list) > 0) {
                    $this->error("该手机号已存在");
                }

                //添加数据
                Db::table('organization')
                    ->insert($data);

                $org_id = Db::table('organization')
                    ->where('corp_code','=',$corp_code)
                    ->find();
                if(count($org_id) == 0){
                    $this->error("添加失败");
                }

                $secure_code="沧海猎人";
                $md5password = md5($secure_code.md5($username));

                //初始化管理员账号
                $admin_data['org_id']=$org_id['id'];
                $admin_data['dep_id']=0;
                $admin_data['username']=$username;
                $admin_data['name']=$orgadminname;
                $admin_data['password']=$md5password;
                $admin_data['role']=2;
                $admin_data['is_del']=0;
                $admin_data['need_m_pass']=1;
                $admin_data['is_admin']=1;
                $admin_data['remarks']="机构内置账号";

                Db::table('admin_user')
                    ->insert($admin_data);

                $this->assign("msg", "开通成功");

            } else {
                $this->assign("msg", "");
            }

            $map_tem['is_del'] = 0;
            $map_tem['is_visable'] = 1;
            $tem_org = Db::table('report_template')
                ->where($map_tem)
                ->select();
            $this->assign("tem_list", $tem_org);


            return $this->fetch();
        } else {
            $this->error("无权访问");
        }
    }

    public function getAccessToken(){
        $appid  = Config::get('wechat_appid');
        $secret = Config::get('wechat_secret');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $HttpService = new \app\index\service\Http();
        $res = json_decode($HttpService->get_request($url));
        $access_token = @$res->access_token;
        return $access_token;
    }


    public function get_image()
    {
        $id = trim(Request::instance()->param('code'));
        $map['access_type'] = 'mp' ;
        $map['corp_code'] = $id ;

        $res_org_list = Db::table('organization')
            ->where($map)
            ->find();

        if(count($res_org_list)==0){
            $this->error("code值错误");
        }

        if(strlen($id)<1)
        {
            $this->error("code值不能为空");
        }
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
        $data['scene'] = $id;
        //小程序路径
        $data['path'] = 'pages/info/info';
        //二维码大小
        $data['width'] = '430';
        $res = $this->postUrl($url, json_encode($data));
        $path = $id . '.jpg';
        file_put_contents($path, $res);
        $return['status_code'] = 2000;
        $return['msg'] = 'ok';
        $return['img'] =  "../../../../../".$path;
        //$return['img'] =  "/".$path;
        echo "<img src='".$return['img']."' />";
        echo "<br><br>云上报<br>";
        echo $res_org_list['corpname'];
        echo "<br>绑定及重新绑定均需要扫码完成绑定";
        exit;
    }

    // 实现Post请求
    public function postUrl($url,$data){
        $curl=curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    
}
