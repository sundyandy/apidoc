<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/20
 * Time: 15:44
 */

namespace app\index\service;
use think\Model;

class Project extends Model
{
    const OPERATE_TYPE_ADD = 'add';
    const OPERATE_TYPE_MODIFY = 'modify';
    const OPERATE_TYPE_DEL = 'del';

    /**
     * @param $projectArray
     * @return int|string
     */
    public function add($projectArray){
        $projectArray['create_user_id'] = session('user_id');
        $projectArray['status'] = 1;
        return db('project')->insert($projectArray);
    }

    /**
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function my(){
        return db('project')
            ->where(['create_user_id'=>session('user_id'),'status'=>1])
            ->select();
    }

    /**
     * 项目基本信息
     * @param $projectID
     * @return array|false|\PDOStatement|string|Model
     */
    public function info($projectID){
        return db('project')
            ->where(['id'=>$projectID])
            ->find();
    }

    /**
     * 检查用户在项目中的权限
     * @param $projectInfo
     * @param int $userID
     * @return mixed
     */
    public function checkUserProjectAuth($projectInfo,$userID=0){
        $auth['view'] = $auth['edit'] = 0;
        if($projectInfo['create_user_id'] == $userID){//创建者拥有查看与编辑权限
            $auth['view'] = $auth['edit'] = 1;
            return $auth;
        }
        if($projectInfo['open_type'] == '1'){//公开
            $auth['view'] = 1;
            if(empty($userID)){
                $auth['edit'] = 0;
            }else{
                $check = db('project_user')
                    ->where(['project_id'=>$projectInfo['id'],'user_id'=>$userID,'status'=>1])
                    ->find();
                if(empty($check)){
                    $auth['edit'] = 0;
                }else{
                    if($check['type'] == 1){//只读
                        $auth['view'] = 1;
                        $auth['edit'] = 0;
                    }else{
                        $auth['view'] = 1;
                        $auth['edit'] = 1;
                    }
                }
            }
        }else{//不公开
            if(empty($userID)){
                $auth['view'] = $auth['edit'] = 0;
            }else{
                $check = db('project_user')
                    ->where(['project_id'=>$projectInfo['id'],'user_id'=>$userID,'status'=>1])
                    ->find();
                if(empty($check)){
                    $auth['view'] = $auth['edit'] = 0;
                }else{
                    if($check['type'] == 1){//只读
                        $auth['view'] = 1;
                        $auth['edit'] = 0;
                    }else{
                        $auth['view'] = 1;
                        $auth['edit'] = 1;
                    }
                }
            }
        }
        return $auth;
    }


    /**
     * 添加操作记录
     * @param $pageID
     * @param $type
     * @return bool
     */
    public function addOperateLog($pageID,$type){
        $menuService = new \app\index\service\Menu();
        $projectInfo = $menuService->info($pageID);
        $projectID = $projectInfo['project_id'];
        $operatorID = session('user_id');
        $operateDate = date('Y-m-d');
        $check = db('project_operate_log')
            ->where(
                [
                    'project_id' => $projectID,
                    'page_id' => $pageID,
                    'operator_id' => $operatorID,
                    'operator_date' => $operateDate,
                    'type' => $type
                ]
            )
            ->find();
        if(empty($check)){
            $add = [
                'project_id' => $projectID,
                'page_id' => $pageID,
                'operator_id' => $operatorID,
                'operator_date' => $operateDate,
                'type' => $type
            ];
            db('project_operate_log')->insertGetId($add);
        }
        return true;
    }

    /**
     * 项目操作记录
     * @param $projectID
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getProjectOperateLog($projectID){
        return db('project_operate_log')
            ->join('project_page','project_page.id = project_operate_log.page_id')
            ->join('user','user.id = project_operate_log.operator_id')
            ->field(['project_operate_log.*,project_page.id as page_id,project_page.title,project_page.type as page_type,user.realname'])
            ->where(['project_operate_log.project_id' => $projectID])
            ->order('id desc')
            ->select();
    }

    /**
     * 获取项目成员表
     * @param $projectID
     * @param int $type
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getProjectUser($projectID,$type=1){
        $map['project_id'] = $projectID;
        if($type){
            $map['status'] = $type;
        }
        return db('project_user')->where($map)->select();
    }


    public function setProjectUser($projectID,$userID,$auth){
        $check = db('project_user')
            ->where(
                [
                    'project_id' => $projectID,
                    'user_id' => $userID,
                    'status' => 1
                ]
            )
            ->find();
        if(!empty($check)){
            if(!empty($auth)){
                $where = [
                    'project_id' => $projectID,
                    'user_id' => $userID,
                ];
                db('project_user')->where($where)->update(['type' => $auth]);
            }else{
                $where = [
                    'project_id' => $projectID,
                    'user_id' => $userID,
                ];
                db('project_user')->where($where)->update(['status' => 0,'deleted_at' => date('Y-m-d H:i:s')]);
            }
        }else{
            if(!empty($auth)){
                $add = [
                    'project_id' => $projectID,
                    'user_id' => $userID,
                    'type' => $auth,
                    'status' => 1
                ];
                db('project_user')->insert($add);
            }
        }
        return true;
    }


    /**
     * 我参与的项目
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function myJoin(){
        $myJoinProjects = db('project_user')
            ->where(
                [
                    'user_id'=> session('user_id'),
                    'status' => 1
                ]
            )
            ->select();
        if(!empty($myJoinProjects)){
            foreach($myJoinProjects as $line){
                $projectIDs[] = $line['project_id'];
            }
            $projectIDs = array_unique($projectIDs);
            $projectIDs = implode(',',$projectIDs);
            $map['id'] = ['exp','IN ('.$projectIDs.') '];
            $map['status'] = 1;
            return db('project')
                ->where($map)
                ->select();
        }else{
            return [];
        }
    }

    /**
     * 通过project_api_request.id，反推所在项目（project）的信息
     * @param $apiParamID
     * @return array|false|\PDOStatement|string|Model
     */
    public function getProjectFromApiParamID($apiParamID){
        $projectApi = db('project_api_request')
            ->join('project_api', 'project_api_request.api_id=project_api.id')
            ->field('project_api.*')
            ->where(['project_api_request.id'=>$apiParamID])
            ->find();
        return $this->info($projectApi['project_id']);
    }

    /**
     * 删除project_api_request一行
     * @param $apiParamID
     * @return int
     */
    public function delApiParam($apiParamID){
        return db('project_api_request')
            ->where(['project_api_request.id'=>$apiParamID])
            ->delete();
    }

}