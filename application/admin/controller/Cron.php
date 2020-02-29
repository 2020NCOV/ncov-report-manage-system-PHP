<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;
class Cron extends Controller{

   public function update_mp(){
   
            $map_org_where['access_type'] = 'mp';
            $map_org_where['is_del'] = 0;
            $res_org_list = Db::table('organization')
                        ->where($map_org_where)
                        ->select(); 
            if(count($res_org_list) >0){
                 foreach ($res_org_list as $list) {
                    $org_id = $list['id'];  //机构id
                    $template_code = $list['template_code'];
                    $template_table ="";
                   
                    $template= Db::table('report_template')
                         ->where('code', '=', $template_code ) 
                         ->find();
                    if(count($template)>0){
                         $template_table = $template['table_name'];
                        // echo $template_table;
                        // echo '-'.$list['template_code'];
                       //  echo '-'.$list['corpname'];
                        // echo '-'.$list['id'];
                    }else
                    {
                      return;
                    }
                   
                    
                    //遍历机构内所有用户的最新表单，并填充到白名单的report表的id字段
                    $wx_bind_table = "er_wx_department_bind_user";
                    $wx_user_table = "er_user_student_weixin";
                   
                    //$org_id 机构名称已赋值
                    $map_user['org_id']= $org_id;
                    $org_users= Db::table('org_whitelist')
                       ->where($map_user ) 
                       ->select();
                    if(count($org_users) >0){
                        foreach ($org_users as $list) {
                        	$wl_uid = $list['id']; // 白名单中的用户id
                            $userID = $list['userID'];//用户标识
                            //echo $userID;
                            //寻找微信id
                            $map_wx_users['isbind']=1;
                            $map_wx_users['dep_id']=$org_id;
                            $map_wx_users['username']=$userID;
                            $wx_users= Db::table($wx_bind_table)
                                     ->where($map_wx_users ) 
                                     ->select();
                           // if(count($wx_users)>0) echo $userID."记录数大于1";
                            if(count($wx_users)>0){
                               $wx_uid = $wx_users[0]['wx_uid'];
                              //到记录表中找到最新的reportid
                               $wx_user_reports= Db::table($template_table)
                                     ->where('wxuid','=',$wx_uid) 
                                     ->select();
  							   $wx_user_reports_count = count($wx_user_reports);
                               if($wx_user_reports_count > 0){
                                    // echo $userID."有记录";
                                 	//$report_id = $wx_user_reports[$wx_user_reports_count-1]['id'];
                              		//将id写到report
                                    $report_data['report_id']= $wx_user_reports[$wx_user_reports_count-1]['id'];
                                    $report_data['report_date']= $wx_user_reports[$wx_user_reports_count-1]['time'];
                                    Db::table('org_whitelist')
                                       ->where('id','=',$wl_uid) 
                                       ->update($report_data);
                               }
							   
                              
                            }
                        }
                    }
                 }
             }
   }
   
}