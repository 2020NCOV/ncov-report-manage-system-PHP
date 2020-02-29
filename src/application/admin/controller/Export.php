<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Session;
class Export extends Base
{
    public function index()
    {
        if(2 ==Session::get('role') || 3 ==Session::get('role') ){
          
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
 			 
            $a_msg="下载全部数据";
            if($admin_role == 3)
            {
              $a_msg='';
            }
          
           $res_org="";
            
            
            $map_dep['org_id'] = $org_id;
            $map_dep['level'] = 1;
            $map_dep['is_del'] = 0;
            if(3 == Session::get('role')){
             	 $map_dep['id'] = Session::get('dep_id');
            }
            $res_org = Db::table('org_dep')
                  ->where($map_dep)
                  ->order('dep_name')
                  ->select();
            
            //echo Db::getLastSql();
            //var_dump($res_org);
            $this->assign("dep_list", $res_org);
            $this->assign("a_msg", $a_msg);
          
          
            return  $this->fetch();
        }else
        {
            $this->error("无权访问");
        }

    }
  
    public function allstudent()
    {
        if(2 ==Session::get('role') ){
          
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
          
          
            //获取该企业使用的表单表名称
            $org_table =  Db::table('organization')
                ->where('id','=',$org_id)
                ->find();
              
            $org_table_name = '';
            if(count($org_table)>0){
                $org_table_name = "report_record_".$org_table['template_code'];
                $org_template_code = $org_table['template_code'];
            }else{
              $this->error("获取表单模板数据错误");
            }
          
            

           

            $service = new \app\admin\service\Log();
            $service->write("3","导出全部学生数据");
            
            //$time = time();
            //echo $dep_id;
          
         
            //这里引入PHPExcel文件注意路径修改
            vendor("PHPExcel");
            vendor("PHPExcel.Writer.Excel5");
            vendor("PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.IOFactory");
            $objExcel = new \PHPExcel();

            $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
            $objActSheet = $objExcel->getActiveSheet();
            $key = ord("A");
            if($org_template_code == "company_df"){
                  $dep_id = Request::instance()->param('id');
                    //如果是部门管理员，则dep_id 必须为本部门的dep_id
                  
                  $map['o.org_id'] = $org_id;
                  if($dep_id){
                      $map['o.sub1_department_id'] = $dep_id;
                  }
                  if(3 == Session::get('role')){
                      $dep_id = Session::get('dep_id');
                      $map['o.sub1_department_id'] = $dep_id;
                  }
            
                  $xlsData = Db::table('org_whitelist')
                      ->alias("o") //取一个别名
                      ->join('org_dep d', 'o.sub1_department_id = d.id','LEFT')
                      ->join($org_table_name." t", 't.id = o.report_id','LEFT')
                      //想要的字段
                      ->field('o.userID,o.name,o.gender,d.dep_name,o.report_date,o.report_id,t.current_temperature,t.is_return_school,t.return_company_date,t.plan_company_date,t.current_district_value,t.current_health_value,t.current_contagion_risk_value,t.psy_status,t.psy_demand,t.psy_knowledge,t.remarks,t.time')
                      ->where($map)  //必须本机构的部门 
                      ->order('userID')
                      ->select();

                  //var_dump($xlsData);
                  //return;
          
                  $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R");
                  $arrHeader = array('序号','标识码','姓名','性别','部门','填报日期','今日体温','目前是否返回公司所在地','返回公司所在地日期','计划返回公司地日期','目前所在地','目前本人身体状况','14天内是否接触传染途径','现在的心理状况','对心理咨询的需求','你最需要获得哪方面心理调适知识','备注','提交时间');
                  $lenth =  count($arrHeader);
                  for($i = 0;$i < $lenth;$i++) {
                      $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
                  };
                  $a=0;
                  foreach($xlsData as $k=>$v){
                      $k +=2;
                      $a +=1;
                      //处理查询出的数据
                      
                    
                      
                      $objActSheet->setCellValue('A'.$k, $a);
                      $objActSheet->setCellValue('B'.$k, $v['userID']);
                      $objActSheet->setCellValue('C'.$k, $v['name']);
                      $objActSheet->setCellValue('D'.$k, $v['gender']);
                      $objActSheet->setCellValue('E'.$k, $v['dep_name']);
                      $v['report_date'] = substr($v['report_date'],0,10);
                      $objActSheet->setCellValue('F'.$k, $v['report_date']);
                      $objActSheet->setCellValue('G'.$k, $v['current_temperature']);
                      if($v['is_return_school']==1)   $v['is_return_school'] ="是";
                      else  $v['is_return_school'] ="否";
                      $objActSheet->setCellValue('H'.$k, $v['is_return_school']);
                      $objActSheet->setCellValue('I'.$k, $v['return_company_date']);
                      $objActSheet->setCellValue('J'.$k, $v['plan_company_date']);
                      
                      $v['current_district_value'] =  $this->get_district_path($v['current_district_value']);
                      $objActSheet->setCellValue('K'.$k, $v['current_district_value']);
                    
                      switch ($v['current_health_value'])
                      {
                          case 1:
                              $v['current_health_value'] = '已确诊新型肺炎，治疗中';
                              break;
                          case 2:
                              $v['current_health_value'] = '疑似待确诊';
                              break;
                          case 3:
                              $v['current_health_value'] = "有被传染可能，隔离观察中";
                              break;
                          case 4:
                              $v['current_health_value'] = '有发烧、咳嗽等症状，经诊断非新型肺炎';
                              break;
                          case 5:
                              $v['current_health_value'] = '身体无异样';
                              break;
                      }
                      $objActSheet->setCellValue('L'.$k, $v['current_health_value']);
                    
                      switch ($v['current_contagion_risk_value'])
                      {
                          case 1:
                              $v['current_contagion_risk_value'] = '回湖北，回家、探亲';
                              break;
                          case 2:
                              $v['current_contagion_risk_value'] = '去湖北旅游、访友';
                              break;
                          case 3:
                              $v['current_contagion_risk_value'] = "接触过湖北回来的朋友或者疑似或者高危人";
                              break;
                          case 4:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，目前参与人都未发现异样，周边也无确认及疑似案例';
                              break;
                          case 5:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，当地已有确诊案例，存在可能被传染情形';
                              break;
                          case 6:
                              $v['current_contagion_risk_value'] = '其他可能被传染情形';
                              break;
                          case 7:
                              $v['current_contagion_risk_value'] = '无高危出行聚会或者聚会等行为，宅家，被传染可能性极小';
                              break;
                         
                      }
                      $objActSheet->setCellValue('M'.$k, $v['current_contagion_risk_value']);
                    
                      switch ($v['psy_status'])
                      {
                          case 1:
                              $v['psy_status'] = '挺好的';
                              break;
                          case 2:
                              $v['psy_status'] = '还可以';
                              break;
                          case 3:
                              $v['psy_status'] = "一般般";
                              break;
                          case 4:
                              $v['psy_status'] = '有点差';
                              break;
                          case 5:
                              $v['psy_status'] = '非常糟';
                              break;
                         
                      }
                      $objActSheet->setCellValue('N'.$k, $v['psy_status']);
                    
                    
                      switch ($v['psy_demand'])
                      {
                          case 1:
                              $v['psy_demand'] = '很需要';
                              break;
                          case 2:
                              $v['psy_demand'] = '偶尔需要';
                              break;
                          case 3:
                              $v['psy_demand'] = "无所谓";
                              break;
                          case 4:
                              $v['psy_demand'] = '暂不需要';
                              break;
                          case 5:
                              $v['psy_demand'] = '不需要';
                              break;
                         
                      }
                      $objActSheet->setCellValue('O'.$k, $v['psy_demand']);
                    
                      switch ($v['psy_knowledge'])
                      {
                          case 1:
                              $v['psy_knowledge'] = '不需要';
                              break;
                          case 2:
                              $v['psy_knowledge'] = '焦虑减压';
                              break;
                          case 3:
                              $v['psy_knowledge'] = "情绪管理";
                              break;
                          case 4:
                              $v['psy_knowledge'] = '学习成长';
                              break;
                          case 5:
                              $v['psy_knowledge'] = '家人相处';
                              break;
                          case 6:
                              $v['psy_knowledge'] = '其他';
                              break;
                         
                      }
                      $objActSheet->setCellValue('P'.$k, $v['psy_knowledge']);
                    
                      $objActSheet->setCellValue('Q'.$k, $v['remarks']);
                      $objActSheet->setCellValue('R'.$k, $v['time']);

                      $objActSheet->getRowDimension($k)->setRowHeight(20);
                  }

                  //设置表格的宽度
                  $objActSheet->getColumnDimension('A')->setWidth(15);
                  $objActSheet->getColumnDimension('B')->setWidth(25);
                  $objActSheet->getColumnDimension('C')->setWidth(15);
                  $objActSheet->getColumnDimension('D')->setWidth(10);
                  $objActSheet->getColumnDimension('E')->setWidth(25);
                  $objActSheet->getColumnDimension('F')->setWidth(25);
                  $objActSheet->getColumnDimension('G')->setWidth(15);
                  $objActSheet->getColumnDimension('H')->setWidth(25);
                  $objActSheet->getColumnDimension('I')->setWidth(25);
                  $objActSheet->getColumnDimension('J')->setWidth(25);
                  $objActSheet->getColumnDimension('K')->setWidth(25);
                  $objActSheet->getColumnDimension('L')->setWidth(25);
                  $objActSheet->getColumnDimension('M')->setWidth(25);
                  $objActSheet->getColumnDimension('N')->setWidth(25);
              	  $objActSheet->getColumnDimension('O')->setWidth(25);
                  $objActSheet->getColumnDimension('P')->setWidth(25);
                  $objActSheet->getColumnDimension('Q')->setWidth(25);
                  $objActSheet->getColumnDimension('R')->setWidth(25);
           
        	}
          
          
            if($org_template_code == "school_df"){
                  $dep_id = Request::instance()->param('id');
                    //如果是部门管理员，则dep_id 必须为本部门的dep_id
                  
                  $map['o.org_id'] = $org_id;
                  if($dep_id){
                      $map['o.sub1_department_id'] = $dep_id;
                  }
                  if(3 == Session::get('role')){
                      $dep_id = Session::get('dep_id');
                      $map['o.sub1_department_id'] = $dep_id;
                  }
            
                  $xlsData = Db::table('org_whitelist')
                      ->alias("o") //取一个别名
                      ->join('org_dep d', 'o.sub1_department_id = d.id','LEFT')
                      ->join($org_table_name." t", 't.id = o.report_id','LEFT')
                      //想要的字段
                      ->field('o.userID,o.name,o.gender,d.dep_name,o.report_date,o.report_id,t.current_temperature,t.is_return_school,t.return_time,t.return_dorm_num,t.return_district_value,t.return_traffic_info,t.current_district_value,t.current_health_value,t.current_contagion_risk_value,t.psy_status,t.psy_demand,t.psy_knowledge,t.remarks,t.time')
                      ->where($map)  //必须本机构的部门 
                      ->order('userID')
                      ->select();

                 // var_dump($xlsData);
                  //return;
          
                  $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T");
                  $arrHeader = array('序号','标识码','姓名','性别','部门','填报日期','今日体温','目前是否已返校','返校日期','宿舍号','从哪个城市返校','交通工具信息','目前所在地区','目前本人身体状况','14天内是否接触传染途径','现在的心理状况','对心理咨询的需求','你最需要获得哪方面心理调适知识','备注','提交时间');
                  $lenth =  count($arrHeader);
                  for($i = 0;$i < $lenth;$i++) {
                      $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
                  };
                  $a=0;
                  foreach($xlsData as $k=>$v){
                      $k +=2;
                      $a +=1;
                      //处理查询出的数据
                      
                    
                      
                      $objActSheet->setCellValue('A'.$k, $a);
                      $objActSheet->setCellValue('B'.$k, $v['userID']);
                      $objActSheet->setCellValue('C'.$k, $v['name']);
                      $objActSheet->setCellValue('D'.$k, $v['gender']);
                      $objActSheet->setCellValue('E'.$k, $v['dep_name']);
                      $v['report_date'] = substr($v['report_date'],0,10);
                      $objActSheet->setCellValue('F'.$k, $v['report_date']);
                      $objActSheet->setCellValue('G'.$k, $v['current_temperature']);
                      if($v['is_return_school']==1)   $v['is_return_school'] ="是";
                      else  $v['is_return_school'] ="否";
                      $objActSheet->setCellValue('H'.$k, $v['is_return_school']);
                      $objActSheet->setCellValue('I'.$k, $v['return_time']);
                      $objActSheet->setCellValue('J'.$k, $v['return_dorm_num']);
                      
                      $v['return_district_value'] =  $this->get_district_path($v['return_district_value']);
                      $objActSheet->setCellValue('K'.$k, $v['return_district_value']);
                    
                      $objActSheet->setCellValue('L'.$k, $v['return_traffic_info']);
                      $v['current_district_value'] =  $this->get_district_path($v['current_district_value']);
                      $objActSheet->setCellValue('M'.$k, $v['current_district_value']);
                    
                      switch ($v['current_health_value'])
                      {
                          case 1:
                              $v['current_health_value'] = '已确诊新型肺炎，治疗中';
                              break;
                          case 2:
                              $v['current_health_value'] = '疑似待确诊';
                              break;
                          case 3:
                              $v['current_health_value'] = "有被传染可能，隔离观察中";
                              break;
                          case 4:
                              $v['current_health_value'] = '有发烧、咳嗽等症状，经诊断非新型肺炎';
                              break;
                          case 5:
                              $v['current_health_value'] = '身体无异样';
                              break;
                      }
                      $objActSheet->setCellValue('N'.$k, $v['current_health_value']);
                    
                      switch ($v['current_contagion_risk_value'])
                      {
                          case 1:
                              $v['current_contagion_risk_value'] = '回湖北，回家、探亲';
                              break;
                          case 2:
                              $v['current_contagion_risk_value'] = '去湖北旅游、访友';
                              break;
                          case 3:
                              $v['current_contagion_risk_value'] = "接触过湖北回来的朋友或者疑似或者高危人";
                              break;
                          case 4:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，目前参与人都未发现异样，周边也无确认及疑似案例';
                              break;
                          case 5:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，当地已有确诊案例，存在可能被传染情形';
                              break;
                          case 6:
                              $v['current_contagion_risk_value'] = '其他可能被传染情形';
                              break;
                          case 7:
                              $v['current_contagion_risk_value'] = '无高危出行聚会或者聚会等行为，宅家，被传染可能性极小';
                              break;
                         
                      }
                      $objActSheet->setCellValue('O'.$k, $v['current_contagion_risk_value']);
                    
                      switch ($v['psy_status'])
                      {
                          case 1:
                              $v['psy_status'] = '挺好的';
                              break;
                          case 2:
                              $v['psy_status'] = '还可以';
                              break;
                          case 3:
                              $v['psy_status'] = "一般般";
                              break;
                          case 4:
                              $v['psy_status'] = '有点差';
                              break;
                          case 5:
                              $v['psy_status'] = '非常糟';
                              break;
                         
                      }
                      $objActSheet->setCellValue('P'.$k, $v['psy_status']);
                    
                    
                      switch ($v['psy_demand'])
                      {
                          case 1:
                              $v['psy_demand'] = '很需要';
                              break;
                          case 2:
                              $v['psy_demand'] = '偶尔需要';
                              break;
                          case 3:
                              $v['psy_demand'] = "无所谓";
                              break;
                          case 4:
                              $v['psy_demand'] = '暂不需要';
                              break;
                          case 5:
                              $v['psy_demand'] = '不需要';
                              break;
                         
                      }
                      $objActSheet->setCellValue('Q'.$k, $v['psy_demand']);
                    
                      switch ($v['psy_knowledge'])
                      {
                          case 1:
                              $v['psy_knowledge'] = '不需要';
                              break;
                          case 2:
                              $v['psy_knowledge'] = '焦虑减压';
                              break;
                          case 3:
                              $v['psy_knowledge'] = "情绪管理";
                              break;
                          case 4:
                              $v['psy_knowledge'] = '学习成长';
                              break;
                          case 5:
                              $v['psy_knowledge'] = '家人相处';
                              break;
                          case 6:
                              $v['psy_knowledge'] = '其他';
                              break;
                         
                      }
                      $objActSheet->setCellValue('R'.$k, $v['psy_knowledge']);
                    
                      $objActSheet->setCellValue('S'.$k, $v['remarks']);
                      $objActSheet->setCellValue('T'.$k, $v['time']);

                      $objActSheet->getRowDimension($k)->setRowHeight(20);
                  }

                  //设置表格的宽度
                  $objActSheet->getColumnDimension('A')->setWidth(15);
                  $objActSheet->getColumnDimension('B')->setWidth(25);
                  $objActSheet->getColumnDimension('C')->setWidth(15);
                  $objActSheet->getColumnDimension('D')->setWidth(10);
                  $objActSheet->getColumnDimension('E')->setWidth(25);
                  $objActSheet->getColumnDimension('F')->setWidth(25);
                  $objActSheet->getColumnDimension('G')->setWidth(15);
                  $objActSheet->getColumnDimension('H')->setWidth(25);
                  $objActSheet->getColumnDimension('I')->setWidth(25);
                  $objActSheet->getColumnDimension('J')->setWidth(25);
                  $objActSheet->getColumnDimension('K')->setWidth(25);
                  $objActSheet->getColumnDimension('L')->setWidth(25);
                  $objActSheet->getColumnDimension('M')->setWidth(25);
                  $objActSheet->getColumnDimension('N')->setWidth(25);
              	  $objActSheet->getColumnDimension('O')->setWidth(25);
                  $objActSheet->getColumnDimension('P')->setWidth(25);
                  $objActSheet->getColumnDimension('Q')->setWidth(25);
                  $objActSheet->getColumnDimension('R')->setWidth(25);
                  $objActSheet->getColumnDimension('S')->setWidth(25);
                  $objActSheet->getColumnDimension('T')->setWidth(25);
           
        	}


            date_default_timezone_set('PRC');
            $today = date('yymd_His');
            $outfile = $org_name."_健康信息表_".$today.".xlsx";
            ob_end_clean();
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="'.$outfile.'"');
            header("Content-Transfer-Encoding: binary");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            $objWriter->save('php://output');    //这里直接导出文件

        }else
        {
            $this->error("无权访问");
        }

    }

    public function mystudent()
    {
        if(2 ==Session::get('role') || 3 ==Session::get('role')){
          
            $admin_uid = Session::get('userid');  //用户id
            $admin_role = Session::get('role'); //  管理员角色
            $org_id = Session::get('org_id'); //   机构id
            $org_name = Session::get('org_name'); // 机构名称
          
          
            //获取该企业使用的表单表名称
            $org_table =  Db::table('organization')
                ->where('id','=',$org_id)
                ->find();
              
            $org_table_name = '';
            if(count($org_table)>0){
                $org_table_name = "report_record_".$org_table['template_code'];
                $org_template_code = $org_table['template_code'];
            }else{
              $this->error("获取表单模板数据错误");
            }
           

            $service = new \app\admin\service\Log();
            $service->write("3","导出全部学生数据");
            
            //$time = time();
            //echo $dep_id;
          
         
            //这里引入PHPExcel文件注意路径修改
            vendor("PHPExcel");
            vendor("PHPExcel.Writer.Excel5");
            vendor("PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.IOFactory");
            $objExcel = new \PHPExcel();

            $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
            $objActSheet = $objExcel->getActiveSheet();
            $key = ord("A");
            if($org_template_code == "company_df"){
                  $dep_id = Request::instance()->param('id');
                    //如果是部门管理员，则dep_id 必须为本部门的dep_id
                  
                  $map['o.org_id'] = $org_id;
                  if($dep_id){
                      $map['o.sub1_department_id'] = $dep_id;
                  }
                  if(3 == Session::get('role')){
                      $dep_id = Session::get('dep_id');
                      $map['o.sub1_department_id'] = $dep_id;
                  }
            
                  $xlsData = Db::table('org_whitelist')
                      ->alias("o") //取一个别名
                      ->join('org_dep d', 'o.sub1_department_id = d.id','LEFT')
                      ->join($org_table_name." t", 't.id = o.report_id','LEFT')
                      //想要的字段
                      ->field('o.userID,o.name,o.gender,d.dep_name,o.report_date,o.report_id,t.current_temperature,t.is_return_school,t.return_company_date,t.plan_company_date,t.current_district_value,t.current_health_value,t.current_contagion_risk_value,t.psy_status,t.psy_demand,t.psy_knowledge,t.remarks,t.time')
                      ->where($map)  //必须本机构的部门 
                      ->order('userID')
                      ->select();

                  //var_dump($xlsData);
                  //return;
          
                  $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R");
                  $arrHeader = array('序号','标识码','姓名','性别','部门','填报日期','今日体温','目前是否返回公司所在地','返回公司所在地日期','计划返回公司地日期','目前所在地','目前本人身体状况','14天内是否接触传染途径','现在的心理状况','对心理咨询的需求','你最需要获得哪方面心理调适知识','备注','提交时间');
                  $lenth =  count($arrHeader);
                  for($i = 0;$i < $lenth;$i++) {
                      $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
                  };
                  $a=0;
                  foreach($xlsData as $k=>$v){
                      $k +=2;
                      $a +=1;
                      //处理查询出的数据
                      
                    
                      
                      $objActSheet->setCellValue('A'.$k, $a);
                      $objActSheet->setCellValue('B'.$k, $v['userID']);
                      $objActSheet->setCellValue('C'.$k, $v['name']);
                      $objActSheet->setCellValue('D'.$k, $v['gender']);
                      $objActSheet->setCellValue('E'.$k, $v['dep_name']);
                      $v['report_date'] = substr($v['report_date'],0,10);
                      $objActSheet->setCellValue('F'.$k, $v['report_date']);
                      $objActSheet->setCellValue('G'.$k, $v['current_temperature']);
                      if($v['is_return_school']==1)   $v['is_return_school'] ="是";
                      else  $v['is_return_school'] ="否";
                      $objActSheet->setCellValue('H'.$k, $v['is_return_school']);
                      $objActSheet->setCellValue('I'.$k, $v['return_company_date']);
                      $objActSheet->setCellValue('J'.$k, $v['plan_company_date']);
                      
                      $v['current_district_value'] =  $this->get_district_path($v['current_district_value']);
                      $objActSheet->setCellValue('K'.$k, $v['current_district_value']);
                    
                      switch ($v['current_health_value'])
                      {
                          case 1:
                              $v['current_health_value'] = '已确诊新型肺炎，治疗中';
                              break;
                          case 2:
                              $v['current_health_value'] = '疑似待确诊';
                              break;
                          case 3:
                              $v['current_health_value'] = "有被传染可能，隔离观察中";
                              break;
                          case 4:
                              $v['current_health_value'] = '有发烧、咳嗽等症状，经诊断非新型肺炎';
                              break;
                          case 5:
                              $v['current_health_value'] = '身体无异样';
                              break;
                      }
                      $objActSheet->setCellValue('L'.$k, $v['current_health_value']);
                    
                      switch ($v['current_contagion_risk_value'])
                      {
                          case 1:
                              $v['current_contagion_risk_value'] = '回湖北，回家、探亲';
                              break;
                          case 2:
                              $v['current_contagion_risk_value'] = '去湖北旅游、访友';
                              break;
                          case 3:
                              $v['current_contagion_risk_value'] = "接触过湖北回来的朋友或者疑似或者高危人";
                              break;
                          case 4:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，目前参与人都未发现异样，周边也无确认及疑似案例';
                              break;
                          case 5:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，当地已有确诊案例，存在可能被传染情形';
                              break;
                          case 6:
                              $v['current_contagion_risk_value'] = '其他可能被传染情形';
                              break;
                          case 7:
                              $v['current_contagion_risk_value'] = '无高危出行聚会或者聚会等行为，宅家，被传染可能性极小';
                              break;
                         
                      }
                      $objActSheet->setCellValue('M'.$k, $v['current_contagion_risk_value']);
                    
                      switch ($v['psy_status'])
                      {
                          case 1:
                              $v['psy_status'] = '挺好的';
                              break;
                          case 2:
                              $v['psy_status'] = '还可以';
                              break;
                          case 3:
                              $v['psy_status'] = "一般般";
                              break;
                          case 4:
                              $v['psy_status'] = '有点差';
                              break;
                          case 5:
                              $v['psy_status'] = '非常糟';
                              break;
                         
                      }
                      $objActSheet->setCellValue('N'.$k, $v['psy_status']);
                    
                    
                      switch ($v['psy_demand'])
                      {
                          case 1:
                              $v['psy_demand'] = '很需要';
                              break;
                          case 2:
                              $v['psy_demand'] = '偶尔需要';
                              break;
                          case 3:
                              $v['psy_demand'] = "无所谓";
                              break;
                          case 4:
                              $v['psy_demand'] = '暂不需要';
                              break;
                          case 5:
                              $v['psy_demand'] = '不需要';
                              break;
                         
                      }
                      $objActSheet->setCellValue('O'.$k, $v['psy_demand']);
                    
                      switch ($v['psy_knowledge'])
                      {
                          case 1:
                              $v['psy_knowledge'] = '不需要';
                              break;
                          case 2:
                              $v['psy_knowledge'] = '焦虑减压';
                              break;
                          case 3:
                              $v['psy_knowledge'] = "情绪管理";
                              break;
                          case 4:
                              $v['psy_knowledge'] = '学习成长';
                              break;
                          case 5:
                              $v['psy_knowledge'] = '家人相处';
                              break;
                          case 6:
                              $v['psy_knowledge'] = '其他';
                              break;
                         
                      }
                      $objActSheet->setCellValue('P'.$k, $v['psy_knowledge']);
                    
                      $objActSheet->setCellValue('Q'.$k, $v['remarks']);
                      $objActSheet->setCellValue('R'.$k, $v['time']);

                      $objActSheet->getRowDimension($k)->setRowHeight(20);
                  }

                  //设置表格的宽度
                  $objActSheet->getColumnDimension('A')->setWidth(15);
                  $objActSheet->getColumnDimension('B')->setWidth(25);
                  $objActSheet->getColumnDimension('C')->setWidth(15);
                  $objActSheet->getColumnDimension('D')->setWidth(10);
                  $objActSheet->getColumnDimension('E')->setWidth(25);
                  $objActSheet->getColumnDimension('F')->setWidth(25);
                  $objActSheet->getColumnDimension('G')->setWidth(15);
                  $objActSheet->getColumnDimension('H')->setWidth(25);
                  $objActSheet->getColumnDimension('I')->setWidth(25);
                  $objActSheet->getColumnDimension('J')->setWidth(25);
                  $objActSheet->getColumnDimension('K')->setWidth(25);
                  $objActSheet->getColumnDimension('L')->setWidth(25);
                  $objActSheet->getColumnDimension('M')->setWidth(25);
                  $objActSheet->getColumnDimension('N')->setWidth(25);
              	  $objActSheet->getColumnDimension('O')->setWidth(25);
                  $objActSheet->getColumnDimension('P')->setWidth(25);
                  $objActSheet->getColumnDimension('Q')->setWidth(25);
                  $objActSheet->getColumnDimension('R')->setWidth(25);
           
        	}
          
          
            if($org_template_code == "default_df"){
                  $dep_id = Request::instance()->param('id');
                    //如果是部门管理员，则dep_id 必须为本部门的dep_id
                  
                  $map['o.org_id'] = $org_id;
                  if($dep_id){
                      $map['o.sub1_department_id'] = $dep_id;
                  }
                  if(3 == Session::get('role')){
                      $dep_id = Session::get('dep_id');
                      $map['o.sub1_department_id'] = $dep_id;
                  }
            
                  $xlsData = Db::table('org_whitelist')
                      ->alias("o") //取一个别名
                      ->join('org_dep d', 'o.sub1_department_id = d.id','LEFT')
                      ->join($org_table_name." t", 't.id = o.report_id','LEFT')
                      //想要的字段
                      ->field('o.userID,o.name,o.gender,d.dep_name,o.report_date,o.report_id,t.current_temperature,t.is_return_school,t.return_time,t.return_dorm_num,t.return_district_value,t.return_traffic_info,t.current_district_value,t.current_health_value,t.current_contagion_risk_value,t.psy_status,t.psy_demand,t.psy_knowledge,t.remarks,t.time')
                      ->where($map)  //必须本机构的部门 
                      ->order('userID')
                      ->select();

                 // var_dump($xlsData);
                  //return;
          
                  $letter =explode(',',"A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T");
                  $arrHeader = array('序号','标识码','姓名','性别','部门','填报日期','今日体温','目前是否已返校','返校日期','宿舍号','从哪个城市返校','交通工具信息','目前所在地区','目前本人身体状况','14天内是否接触传染途径','现在的心理状况','对心理咨询的需求','你最需要获得哪方面心理调适知识','备注','提交时间');
                  $lenth =  count($arrHeader);
                  for($i = 0;$i < $lenth;$i++) {
                      $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
                  };
                  $a=0;
                  foreach($xlsData as $k=>$v){
                      $k +=2;
                      $a +=1;
                      //处理查询出的数据
                      
                    
                      
                      $objActSheet->setCellValue('A'.$k, $a);
                      $objActSheet->setCellValue('B'.$k, $v['userID']);
                      $objActSheet->setCellValue('C'.$k, $v['name']);
                      $objActSheet->setCellValue('D'.$k, $v['gender']);
                      $objActSheet->setCellValue('E'.$k, $v['dep_name']);
                      $v['report_date'] = substr($v['report_date'],0,10);
                      $objActSheet->setCellValue('F'.$k, $v['report_date']);
                      $objActSheet->setCellValue('G'.$k, $v['current_temperature']);
                      if($v['is_return_school']==1)   $v['is_return_school'] ="是";
                      else  $v['is_return_school'] ="否";
                      $objActSheet->setCellValue('H'.$k, $v['is_return_school']);
                      $objActSheet->setCellValue('I'.$k, $v['return_time']);
                      $objActSheet->setCellValue('J'.$k, $v['return_dorm_num']);
                      
                      $v['return_district_value'] =  $this->get_district_path($v['return_district_value']);
                      $objActSheet->setCellValue('K'.$k, $v['return_district_value']);
                    
                      $objActSheet->setCellValue('L'.$k, $v['return_traffic_info']);
                      $v['current_district_value'] =  $this->get_district_path($v['current_district_value']);
                      $objActSheet->setCellValue('M'.$k, $v['current_district_value']);
                    
                      switch ($v['current_health_value'])
                      {
                          case 1:
                              $v['current_health_value'] = '已确诊新型肺炎，治疗中';
                              break;
                          case 2:
                              $v['current_health_value'] = '疑似待确诊';
                              break;
                          case 3:
                              $v['current_health_value'] = "有被传染可能，隔离观察中";
                              break;
                          case 4:
                              $v['current_health_value'] = '有发烧、咳嗽等症状，经诊断非新型肺炎';
                              break;
                          case 5:
                              $v['current_health_value'] = '身体无异样';
                              break;
                      }
                      $objActSheet->setCellValue('N'.$k, $v['current_health_value']);
                    
                      switch ($v['current_contagion_risk_value'])
                      {
                          case 1:
                              $v['current_contagion_risk_value'] = '回湖北，回家、探亲';
                              break;
                          case 2:
                              $v['current_contagion_risk_value'] = '去湖北旅游、访友';
                              break;
                          case 3:
                              $v['current_contagion_risk_value'] = "接触过湖北回来的朋友或者疑似或者高危人";
                              break;
                          case 4:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，目前参与人都未发现异样，周边也无确认及疑似案例';
                              break;
                          case 5:
                              $v['current_contagion_risk_value'] = '期间有过外出、旅游及聚会，当地已有确诊案例，存在可能被传染情形';
                              break;
                          case 6:
                              $v['current_contagion_risk_value'] = '其他可能被传染情形';
                              break;
                          case 7:
                              $v['current_contagion_risk_value'] = '无高危出行聚会或者聚会等行为，宅家，被传染可能性极小';
                              break;
                         
                      }
                      $objActSheet->setCellValue('O'.$k, $v['current_contagion_risk_value']);
                    
                      switch ($v['psy_status'])
                      {
                          case 1:
                              $v['psy_status'] = '挺好的';
                              break;
                          case 2:
                              $v['psy_status'] = '还可以';
                              break;
                          case 3:
                              $v['psy_status'] = "一般般";
                              break;
                          case 4:
                              $v['psy_status'] = '有点差';
                              break;
                          case 5:
                              $v['psy_status'] = '非常糟';
                              break;
                         
                      }
                      $objActSheet->setCellValue('P'.$k, $v['psy_status']);
                    
                    
                      switch ($v['psy_demand'])
                      {
                          case 1:
                              $v['psy_demand'] = '很需要';
                              break;
                          case 2:
                              $v['psy_demand'] = '偶尔需要';
                              break;
                          case 3:
                              $v['psy_demand'] = "无所谓";
                              break;
                          case 4:
                              $v['psy_demand'] = '暂不需要';
                              break;
                          case 5:
                              $v['psy_demand'] = '不需要';
                              break;
                         
                      }
                      $objActSheet->setCellValue('Q'.$k, $v['psy_demand']);
                    
                      switch ($v['psy_knowledge'])
                      {
                          case 1:
                              $v['psy_knowledge'] = '不需要';
                              break;
                          case 2:
                              $v['psy_knowledge'] = '焦虑减压';
                              break;
                          case 3:
                              $v['psy_knowledge'] = "情绪管理";
                              break;
                          case 4:
                              $v['psy_knowledge'] = '学习成长';
                              break;
                          case 5:
                              $v['psy_knowledge'] = '家人相处';
                              break;
                          case 6:
                              $v['psy_knowledge'] = '其他';
                              break;
                         
                      }
                      $objActSheet->setCellValue('R'.$k, $v['psy_knowledge']);
                    
                      $objActSheet->setCellValue('S'.$k, $v['remarks']);
                      $objActSheet->setCellValue('T'.$k, $v['time']);

                      $objActSheet->getRowDimension($k)->setRowHeight(20);
                  }

                  //设置表格的宽度
                  $objActSheet->getColumnDimension('A')->setWidth(15);
                  $objActSheet->getColumnDimension('B')->setWidth(25);
                  $objActSheet->getColumnDimension('C')->setWidth(15);
                  $objActSheet->getColumnDimension('D')->setWidth(10);
                  $objActSheet->getColumnDimension('E')->setWidth(25);
                  $objActSheet->getColumnDimension('F')->setWidth(25);
                  $objActSheet->getColumnDimension('G')->setWidth(15);
                  $objActSheet->getColumnDimension('H')->setWidth(25);
                  $objActSheet->getColumnDimension('I')->setWidth(25);
                  $objActSheet->getColumnDimension('J')->setWidth(25);
                  $objActSheet->getColumnDimension('K')->setWidth(25);
                  $objActSheet->getColumnDimension('L')->setWidth(25);
                  $objActSheet->getColumnDimension('M')->setWidth(25);
                  $objActSheet->getColumnDimension('N')->setWidth(25);
              	  $objActSheet->getColumnDimension('O')->setWidth(25);
                  $objActSheet->getColumnDimension('P')->setWidth(25);
                  $objActSheet->getColumnDimension('Q')->setWidth(25);
                  $objActSheet->getColumnDimension('R')->setWidth(25);
                  $objActSheet->getColumnDimension('S')->setWidth(25);
                  $objActSheet->getColumnDimension('T')->setWidth(25);
           
        	}


            date_default_timezone_set('PRC');
            $today = date('yymd_His');
            $outfile = $org_name."_健康信息表_".$today.".xlsx";
            ob_end_clean();
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="'.$outfile.'"');
            header("Content-Transfer-Encoding: binary");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            $objWriter->save('php://output');    //这里直接导出文件

        }else
        {
            $this->error("无权访问");
        }

    }
  
  	public function get_district_path($city_code){
        $pathstr= '';
     	$data = Db::table('com_district')
          ->where('value','=',$city_code)
          ->select();
        if(count($data)>0){
        	if($data[0]['level_id'] == 1){
            	$pathstr = $data[0]['name'];
            }else{
            	$pathstr = $data[0]['name'];
                $data = Db::table('com_district')
                        ->where('value','=',$data[0]['parent_id'])
                        ->select();
                if(count($data)>0){
                	$pathstr = $data[0]['name'].",".$pathstr;
                }
            }	
        }
     	return $pathstr;
     }

}
