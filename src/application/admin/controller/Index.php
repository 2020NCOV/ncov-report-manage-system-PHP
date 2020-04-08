<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;

class Index extends Base
{
    public function index()
    {

        $today = date("Y-m-d");

        $total_count =0;

        if (2 == Session::get('role') || 3 == Session::get('role')) {
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
            $dep_id = Session::get('dep_id'); // 部门ID

            //获取总人数
            $map_total_count['org_id']= $org_id;
            if($admin_role ==3)
                $map_total_count['sub1_department_id']= $dep_id;
            $total_count = Db::table('org_whitelist')
                ->where($map_total_count)
                ->count();
            $total_unreport_count=0;
            $total_report_count=0;
            $total_report_ratio = 0;

            //统计各部门上报率
            $map_dep_count['org_id']= $org_id;
            $map_dep_count['is_del']= 0;
            $map_dep_count['level']= 1;
            if($admin_role ==3)
                $map_dep_count['id']= $dep_id;
            $dep_res = Db::table('org_dep')
                ->where($map_dep_count)
                ->select();

            if(count($dep_res)>0){
                foreach ($dep_res as $key => $list) {
                    //统计各部门总人数
                    $count = Db::table('org_whitelist')
                        ->where('sub1_department_id' ,'=',$list['id'])
                        ->count();
                    $dep_res[$key]['total']=$count;

                    //防止除数为0
                    $count1 =$count;
                    if($count1 == 0)
                        $count1 = 0.00000001;
                    $dep_res[$key]['total_sum']=$count1;

                    //统计今日上报人数
                    $count = Db::table('org_whitelist')
                        ->where('sub1_department_id' ,'=',$list['id'])
                        ->where('report_date' ,'like','%'.$today.'%')
                        ->count();
                    $dep_res[$key]['today']=$count;

                    //防止除数为0
                    $count1 =$count;
                    if($count == 0)
                        $count1 = 0.00000001;
                    $dep_res[$key]['today_sum']=$count1;

                    //统计上报总人数和未上报人数
                    $total_report_count = $total_report_count +$dep_res[$key]['today'];


                    //统计上报率
                    $dep_res[$key]['unreport']=$dep_res[$key]['total']-$dep_res[$key]['today'];
                    $total_unreport_count = $total_unreport_count+$dep_res[$key]['unreport'];

                    $total_count1 = $total_count;
                    if($total_count ==0)
                        $total_count1=0.0001;
                    $total_report_ratio = sprintf("%.0f",sprintf("%f", $total_report_count/$total_count1)*100)."%";

                    $dep_res[$key]['ratio']=sprintf("%.0f",sprintf("%f", $dep_res[$key]['today_sum']/$dep_res[$key]['total_sum'])*100)."%";

                }
            }

            //var_dump($dep_res);


        }


        $this->assign("total_count", $total_count);//总人数
        $this->assign("total_unreport_count", $total_unreport_count);//总人数
        $this->assign("total_report_count", $total_report_count);//总人数
        $this->assign("total_report_ratio", $total_report_ratio);//总人数
        $this->assign("dep_list", $dep_res);//总人数

        return  $this->fetch();
    }
  
  
    public function index2()
    {

        //***总人数



        //var_dump($type_count_num1[0]['num']);

        //***当前在北京市总数
        $bj_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%北京%' and `study_type` = '全日制'";
        $bj_count_num1 = Db::query($bj_count_num_str1);
        //var_dump($bj_count_num1[0]['num']);

        //***当前在外省市总数
        $unbj_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` not like '%北京%' and `cur_place`  not like '%大陆%' and `study_type` = '全日制'";
        $unbj_count_num1 = Db::query($unbj_count_num_str1);
        //var_dump($unbj_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and `study_type` = '全日制'";
        $out_sea_count_num1 = Db::query($out_sea_count_num_str1);
        //var_dump($out_sea_count_num1[0]['num']);



        //***在京,北京居住地,大陆生,总数
        $bj_home_type1_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` = '全日制'";
        $bj_home_type1_count_num1 = Db::query($bj_home_type1_count_num_str1);
        //var_dump($bj_home_type1_count_num1[0]['num']);


        //***在京,北京户籍,大陆生,总数
        $bj_huji_type1_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `jiguansheng` like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` = '全日制'";
        $bj_huji_type1_count_num1 = Db::query($bj_huji_type1_count_num_str1);
        //var_dump($bj_home_type1_count_num1[0]['num']);

        //***在京,非北京户籍,大陆生,总数
        $unbj_huji_type1_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `jiguansheng` not like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` = '全日制'";
        $unbj_huji_type1_count_num1 = Db::query($unbj_huji_type1_count_num_str1);
        //var_dump($bj_home_type1_count_num1[0]['num']);


        //***在京,北京居住地,港澳台,总数
        $bj_home_type2_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` like '%北京%'  and`cur_place` like '%北京%' and gat!='大陆' and `study_type` = '全日制'";
        $bj_home_type2_count_num1 = Db::query($bj_home_type2_count_num_str1);
        //var_dump($bj_home_type2_count_num1[0]['num']);

        //***在京,非北京居住地,大陆生,总数
        $unbj_home_type1_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` not like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` = '全日制'";
        $unbj_home_type1_count_num1 = Db::query($unbj_home_type1_count_num_str1);
        //var_dump($unbj_home_type1_count_num1[0]['num']);

        //***在京,非北京居住地,港澳台,总数
        $unbj_home_type2_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` not like '%北京%'  and`cur_place` like '%北京%' and gat!='大陆' and `study_type` = '全日制'";
        $unbj_home_type2_count_num1 = Db::query($unbj_home_type2_count_num_str1);
        //var_dump($unbj_home_type2_count_num1[0]['num']);

        //***在京,港澳台,总数
        $bj_gat_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%北京%' and gat!='大陆' and `study_type` = '全日制'";
        $bj_gat_count_num1 = Db::query($bj_gat_count_num_str1);
        //var_dump($bj_gat_count_num1[0]['num']);


        //***当前在外省市,湖北地区 总数
        $hubei_cur_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%湖北%' and gat='大陆'   and `study_type` = '全日制'";
        $hubei_cur_count_num1 = Db::query($hubei_cur_count_num_str1);
        //var_dump($hubei_cur_count_num1[0]['num']);

        //***当前在外省市,湖北地区 总数
        $un_hubei_cur_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` NOT like '%北京%' and `cur_place` NOT like '%湖北%' and `cur_place` NOT like '%大陆%' and gat='大陆'  and `study_type` = '全日制'";
        $un_hubei_cur_count_num1 = Db::query($un_hubei_cur_count_num_str1);
        //var_dump($un_hubei_cur_count_num[0]['num']);

        //***当前在外省市,湖北地区,港澳台等 总数
        $un_hubei_outsea_cur_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` NOT like '%北京%' and `cur_place` NOT like '%湖北%' and `cur_place` NOT like '%大陆%' and gat!='大陆'  and `study_type` = '全日制'";
        $un_hubei_outsea_cur_count_num1 = Db::query($un_hubei_outsea_cur_count_num_str1);
        //var_dump($un_hubei_outsea_cur_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_dalu_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and gat='大陆'  and `study_type` = '全日制'";
        $out_sea_dalu_count_num1 = Db::query($out_sea_dalu_count_num_str1);
        //var_dump($out_sea_dalu_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_gat_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and gat!='大陆'  and `study_type` = '全日制'";
        $out_sea_gat_count_num1 = Db::query($out_sea_gat_count_num_str1);
        //var_dump($out_sea_gat_count_num1[0]['num']);

        $this->assign("type_count_num1", $type_count_num1[0]['num']);
        $this->assign("bj_count_num1", $bj_count_num1[0]['num']);
        $this->assign("unbj_count_num1", $unbj_count_num1[0]['num']);
        $this->assign("out_sea_count_num1", $out_sea_count_num1[0]['num']);
        $this->assign("bj_home_type1_count_num1", $bj_home_type1_count_num1[0]['num']);
        $this->assign("bj_home_type2_count_num1", $bj_home_type2_count_num1[0]['num']);
        $this->assign("unbj_home_type1_count_num1", $unbj_home_type1_count_num1[0]['num']);
        $this->assign("unbj_home_type2_count_num1", $unbj_home_type2_count_num1[0]['num']);
        $this->assign("hubei_cur_count_num1", $hubei_cur_count_num1[0]['num']);
        $this->assign("un_hubei_cur_count_num1", $un_hubei_cur_count_num1[0]['num']);
        $this->assign("un_hubei_outsea_cur_count_num1", $un_hubei_outsea_cur_count_num1[0]['num']);
        $this->assign("out_sea_dalu_count_num1", $out_sea_dalu_count_num1[0]['num']);
        $this->assign("out_sea_gat_count_num1", $out_sea_gat_count_num1[0]['num']);
        $this->assign("bj_gat_count_num1", $bj_gat_count_num1[0]['num']);

        $this->assign("bj_huji_type1_count_num1", $bj_huji_type1_count_num1[0]['num']);
        $this->assign("unbj_huji_type1_count_num1", $unbj_huji_type1_count_num1[0]['num']);



        //以下为非全日制
        $type_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE  `study_type` != '全日制'";
        $type_count_num2 = Db::query($type_count_num_str2);
        //var_dump($type_count_num2[0]['num']);

        $bj_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%北京%' and `study_type` != '全日制'";
        $bj_count_num2 = Db::query($bj_count_num_str2);
        //var_dump($bj_count_num2[0]['num']);

        $unbj_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` not like '%北京%' and `cur_place`  not like '%大陆%' and `study_type` != '全日制'";
        $unbj_count_num2 = Db::query($unbj_count_num_str2);
        //var_dump($unbj_count_num2[0]['num']);

        $out_sea_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and `study_type` != '全日制'";
        $out_sea_count_num2 = Db::query($out_sea_count_num_str2);
        //var_dump($out_sea_count_num2[0]['num']);



        //***在京,北京居住地,大陆生,总数
        $bj_home_type1_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` != '全日制'";
        $bj_home_type1_count_num2 = Db::query($bj_home_type1_count_num_str2);
        //var_dump($bj_home_type1_count_num1[0]['num']);


        //***在京,北京户籍,大陆生,总数
        $bj_huji_type1_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `jiguansheng` like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` != '全日制'";
        $bj_huji_type1_count_num2 = Db::query($bj_huji_type1_count_num_str2);
        //var_dump($bj_home_type1_count_num1[0]['num']);

        //***在京,非北京户籍,大陆生,总数
        $unbj_huji_type1_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `jiguansheng` not like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` != '全日制'";
        $unbj_huji_type1_count_num2 = Db::query($unbj_huji_type1_count_num_str2);
        //var_dump($bj_home_type1_count_num1[0]['num']);


        //***在京,北京居住地,港澳台,总数
        $bj_home_type2_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` like '%北京%'  and`cur_place` like '%北京%' and gat!='大陆' and `study_type` != '全日制'";
        $bj_home_type2_count_num2 = Db::query($bj_home_type2_count_num_str2);
        //var_dump($bj_home_type2_count_num1[0]['num']);

        //***在京,非北京居住地,大陆生,总数
        $unbj_home_type1_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` not like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' and `study_type` != '全日制'";
        $unbj_home_type1_count_num2 = Db::query($unbj_home_type1_count_num_str2);
        //var_dump($unbj_home_type1_count_num1[0]['num']);

        //***在京,非北京居住地,港澳台,总数
        $unbj_home_type2_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` not like '%北京%'  and`cur_place` like '%北京%' and gat!='大陆' and `study_type` != '全日制'";
        $unbj_home_type2_count_num2 = Db::query($unbj_home_type2_count_num_str2);
        //var_dump($unbj_home_type2_count_num1[0]['num']);

        //***在京,港澳台,总数
        $bj_gat_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%北京%' and gat!='大陆' and `study_type` != '全日制'";
        $bj_gat_count_num2 = Db::query($bj_gat_count_num_str2);
        //var_dump($bj_gat_count_num1[0]['num']);


        //***当前在外省市,湖北地区 总数
        $hubei_cur_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%湖北%' and gat='大陆'   and `study_type` != '全日制'";
        $hubei_cur_count_num2 = Db::query($hubei_cur_count_num_str2);
        //var_dump($hubei_cur_count_num1[0]['num']);

        //***当前在外省市,湖北地区 总数
        $un_hubei_cur_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` NOT like '%北京%' and `cur_place` NOT like '%湖北%' and `cur_place` NOT like '%大陆%' and gat='大陆'  and `study_type` != '全日制'";
        $un_hubei_cur_count_num2 = Db::query($un_hubei_cur_count_num_str2);
        //var_dump($un_hubei_cur_count_num[0]['num']);

        //***当前在外省市,湖北地区,港澳台等 总数
        $un_hubei_outsea_cur_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` NOT like '%北京%' and `cur_place` NOT like '%湖北%' and `cur_place` NOT like '%大陆%' and gat!='大陆'  and `study_type` != '全日制'";
        $un_hubei_outsea_cur_count_num2 = Db::query($un_hubei_outsea_cur_count_num_str2);
        //var_dump($un_hubei_outsea_cur_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_dalu_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and gat='大陆'  and `study_type` != '全日制'";
        $out_sea_dalu_count_num2 = Db::query($out_sea_dalu_count_num_str2);
        //var_dump($out_sea_dalu_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_gat_count_num_str2 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and gat!='大陆'  and `study_type` != '全日制'";
        $out_sea_gat_count_num2 = Db::query($out_sea_gat_count_num_str2);
        //var_dump($out_sea_gat_count_num1[0]['num']);
        

        $this->assign("type_count_num2", $type_count_num2[0]['num']);
        $this->assign("bj_count_num2", $bj_count_num2[0]['num']);
        $this->assign("unbj_count_num2", $unbj_count_num2[0]['num']);
        $this->assign("out_sea_count_num2", $out_sea_count_num2[0]['num']);

        $this->assign("bj_home_type1_count_num2", $bj_home_type1_count_num2[0]['num']);
        $this->assign("bj_home_type2_count_num2", $bj_home_type2_count_num2[0]['num']);
        $this->assign("unbj_home_type1_count_num2", $unbj_home_type1_count_num2[0]['num']);
        $this->assign("unbj_home_type2_count_num2", $unbj_home_type2_count_num2[0]['num']);
        $this->assign("hubei_cur_count_num2", $hubei_cur_count_num2[0]['num']);
        $this->assign("un_hubei_cur_count_num2", $un_hubei_cur_count_num2[0]['num']);
        $this->assign("un_hubei_outsea_cur_count_num2", $un_hubei_outsea_cur_count_num2[0]['num']);
        $this->assign("out_sea_dalu_count_num2", $out_sea_dalu_count_num2[0]['num']);
        $this->assign("out_sea_gat_count_num2", $out_sea_gat_count_num2[0]['num']);
        $this->assign("bj_gat_count_num2", $bj_gat_count_num2[0]['num']);
        $this->assign("bj_huji_type1_count_num2", $bj_huji_type1_count_num2[0]['num']);
        $this->assign("unbj_huji_type1_count_num2", $unbj_huji_type1_count_num2[0]['num']);




        //以下为非全日制
        $type_count_num_str3 ="SELECT count(*) as num FROM `a2020_student`";
        $type_count_num3 = Db::query($type_count_num_str3);
        //var_dump($type_count_num2[0]['num']);

        $bj_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%北京%' ";
        $bj_count_num3 = Db::query($bj_count_num_str3);
        //var_dump($bj_count_num2[0]['num']);

        $unbj_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` not like '%北京%' and `cur_place`  not like '%大陆%' ";
        $unbj_count_num3 = Db::query($unbj_count_num_str3);
        //var_dump($unbj_count_num2[0]['num']);

        $out_sea_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' ";
        $out_sea_count_num3 = Db::query($out_sea_count_num_str3);
        //var_dump($out_sea_count_num2[0]['num']);



        //***在京,北京居住地,大陆生,总数
        $bj_home_type1_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' ";
        $bj_home_type1_count_num3 = Db::query($bj_home_type1_count_num_str3);
        //var_dump($bj_home_type1_count_num1[0]['num']);


        //***在京,北京户籍,大陆生,总数
        $bj_huji_type1_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `jiguansheng` like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' ";
        $bj_huji_type1_count_num3 = Db::query($bj_huji_type1_count_num_str3);
        //var_dump($bj_home_type1_count_num1[0]['num']);

        //***在京,非北京户籍,大陆生,总数
        $unbj_huji_type1_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `jiguansheng` not like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' ";
        $unbj_huji_type1_count_num3 = Db::query($unbj_huji_type1_count_num_str3);
        //var_dump($bj_home_type1_count_num1[0]['num']);


        //***在京,北京居住地,港澳台,总数
        $bj_home_type2_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` like '%北京%'  and`cur_place` like '%北京%' and gat!='大陆' ";
        $bj_home_type2_count_num3 = Db::query($bj_home_type2_count_num_str3);
        //var_dump($bj_home_type2_count_num1[0]['num']);

        //***在京,非北京居住地,大陆生,总数
        $unbj_home_type1_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` not like '%北京%'  and`cur_place` like '%北京%' and gat='大陆' ";
        $unbj_home_type1_count_num3 = Db::query($unbj_home_type1_count_num_str3);
        //var_dump($unbj_home_type1_count_num1[0]['num']);

        //***在京,非北京居住地,港澳台,总数
        $unbj_home_type2_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `home_place` not like '%北京%'  and`cur_place` like '%北京%' and gat!='大陆' ";
        $unbj_home_type2_count_num3 = Db::query($unbj_home_type2_count_num_str3);
        //var_dump($unbj_home_type2_count_num1[0]['num']);

        //***在京,港澳台,总数
        $bj_gat_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%北京%' and gat!='大陆'";
        $bj_gat_count_num3 = Db::query($bj_gat_count_num_str3);
        //var_dump($bj_gat_count_num1[0]['num']);


        //***当前在外省市,湖北地区 总数
        $hubei_cur_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` like '%湖北%' and gat='大陆'  ";
        $hubei_cur_count_num3 = Db::query($hubei_cur_count_num_str3);
        //var_dump($hubei_cur_count_num1[0]['num']);

        //***当前在外省市,湖北地区 总数
        $un_hubei_cur_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` NOT like '%北京%' and `cur_place` NOT like '%湖北%' and `cur_place` NOT like '%大陆%' and gat='大陆' ";
        $un_hubei_cur_count_num3 = Db::query($un_hubei_cur_count_num_str3);
        //var_dump($un_hubei_cur_count_num[0]['num']);

        //***当前在外省市,湖北地区,港澳台等 总数
        $un_hubei_outsea_cur_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place` NOT like '%北京%' and `cur_place` NOT like '%湖北%' and `cur_place` NOT like '%大陆%' and gat!='大陆' ";
        $un_hubei_outsea_cur_count_num3 = Db::query($un_hubei_outsea_cur_count_num_str3);
        //var_dump($un_hubei_outsea_cur_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_dalu_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and gat='大陆'";
        $out_sea_dalu_count_num3 = Db::query($out_sea_dalu_count_num_str3);
        //var_dump($out_sea_dalu_count_num1[0]['num']);

        //***当前在大陆以外地区总数
        $out_sea_gat_count_num_str3 ="SELECT count(*) as num FROM `a2020_student` WHERE `cur_place`  like '%大陆%' and gat!='大陆'  ";
        $out_sea_gat_count_num3 = Db::query($out_sea_gat_count_num_str3);
        //var_dump($out_sea_gat_count_num1[0]['num']);


        $this->assign("type_count_num3", $type_count_num3[0]['num']);
        $this->assign("bj_count_num3", $bj_count_num3[0]['num']);
        $this->assign("unbj_count_num3", $unbj_count_num3[0]['num']);
        $this->assign("out_sea_count_num3", $out_sea_count_num3[0]['num']);

        $this->assign("bj_home_type1_count_num3", $bj_home_type1_count_num3[0]['num']);
        $this->assign("bj_home_type2_count_num3", $bj_home_type2_count_num3[0]['num']);
        $this->assign("unbj_home_type1_count_num3", $unbj_home_type1_count_num3[0]['num']);
        $this->assign("unbj_home_type2_count_num3", $unbj_home_type2_count_num3[0]['num']);
        $this->assign("hubei_cur_count_num3", $hubei_cur_count_num3[0]['num']);
        $this->assign("un_hubei_cur_count_num3", $un_hubei_cur_count_num3[0]['num']);
        $this->assign("un_hubei_outsea_cur_count_num3", $un_hubei_outsea_cur_count_num3[0]['num']);
        $this->assign("out_sea_dalu_count_num3", $out_sea_dalu_count_num3[0]['num']);
        $this->assign("out_sea_gat_count_num3", $out_sea_gat_count_num3[0]['num']);
        $this->assign("bj_gat_count_num3", $bj_gat_count_num3[0]['num']);
        $this->assign("bj_huji_type1_count_num3", $bj_huji_type1_count_num3[0]['num']);
        $this->assign("unbj_huji_type1_count_num3", $unbj_huji_type1_count_num3[0]['num']);

        return  $this->fetch();
    
    }

    public function updatePassword()
    {

        return  $this->fetch();

    }

    public function updatepw()
    {
        $secure_code="沧海猎人";

        $username = Session::get('username');

        $oldpsswd = trim(Request::instance()->post('old_passwd'));
        $password1 = trim(Request::instance()->post('new_passwd1'));
        $password2 = trim(Request::instance()->post('new_passwd2'));

        if(preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $password1) == 0){
            $this->error("密码必须为字母和数字组合,长度不能小于8位,两次输入要一致");   
        }
        if(preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $password2) == 0){
            $this->error("密码必须为字母和数字组合,长度不能小于8位,两次输入要一致");
        }
        if($password1 != $password2){
            $this->error("密码必须为字母和数字组合,长度不能小于8位,两次输入要一致");
        }
        if(strlen($password1)<8 ){
            $this->error("密码必须为字母和数字组合,长度不能小于8位,两次输入要一致");
        }

        $md5password = md5($secure_code.md5($oldpsswd));
        //使用数组方式，防止SQL注入漏洞
        $map['username']=$username;
        $map['password']=$md5password;
        $res = Db::table('admin_user')
            ->where($map)
            ->select();
        //echo Db::getLastSql();
        if(count($res) == 0)
        {
            $this->error("原密码不正确");
        }

        $md5passwordnew = md5($secure_code.md5($password1));
        $data['password']=$md5passwordnew;
        $data['need_m_pass']=0;
        //使用数组方式，防止SQL注入漏洞
        $res = Db::table('admin_user')
            ->where('username','=',$username)
            ->update($data);
        //echo Db::getLastSql();

        if($res !== false)
        {
            $service = new \app\admin\service\Log();
            $service->write("2","修改密码成功");
            
            $this->success("密码更新成功,请重新登录!",url('admin/login/index'));
        }else{
            $service = new \app\admin\service\Log();
            $service->write("2","修改密码失败");
            $this->error("数据错误,请重试");
        }
    }
    
}
