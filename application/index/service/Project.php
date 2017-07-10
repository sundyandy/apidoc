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

}