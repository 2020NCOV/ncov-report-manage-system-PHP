<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;

class Index extends Base
{
    public function index()
    {
       return  $this->fetch();
    }
  
  
    public function index2()
    {
        //***全日制总数
        $type_count_num_str1 ="SELECT count(*) as num FROM `a2020_student` WHERE  `study_type` = '全日制'";
        $type_count_num1 = Db::query($type_count_num_str1);
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

        $oldpsswd = Request::instance()->post('old_passwd');
        $password1 = Request::instance()->post('new_passwd1');
        $password2 = Request::instance()->post('new_passwd2');

        if(preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $password1) == 0){
            $this->error("密码必须为字母和数字组合,长度不能小于8位,两次输入要一致");   
        }
        if(preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $password2) == 0){
            $this->error("密码必须为字母和数字组合,长度不能小于8位,两次输入要一致");
        }
        if($password1 != $password2){
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
