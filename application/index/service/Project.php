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
}