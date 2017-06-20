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
}