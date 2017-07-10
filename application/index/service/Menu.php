<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/29
 * Time: 16:53
 */

namespace app\index\service;
use think\Model;

class Menu extends Model
{
    /**
     * 菜单列表
     * @param $projectID
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function lists($projectID){
        $level1 = db('project_page')
            ->where(['project_id'=>$projectID,'pre_id'=>0,'status'=>1])
            ->order('sort asc')
            ->select();
        foreach($level1 as $key=>$line){
            $level2 = db('project_page')
                ->where(['project_id'=>$projectID,'pre_id'=>$line['id'],'status'=>1])
                ->order('sort asc')
                ->select();
            if(!empty($level2)){
                $level1[$key]['has_child'] = 1;
                $level1[$key]['child'] = $level2;
                foreach($level2 as $key2=>$line2){
                    $level3 = db('project_page')
                        ->where(['project_id'=>$projectID,'pre_id'=>$line2['id'],'status'=>1])
                        ->order('sort asc')
                        ->select();
                    if(!empty($level3)) {
                        $level1[$key]['child'][$key2]['has_child'] = 1;
                        $level1[$key]['child'][$key2]['child'] = $level3;
                    }else{
                        $level1[$key]['child'][$key2]['has_child'] = 0;
                        $level1[$key]['child'][$key2]['child'] = [];
                    }
                }
            }else{
                $level1[$key]['has_child'] = 0;
                $level1[$key]['child'] = [];
            }
        }
        return $level1;
    }

    /**
     * 添加菜单
     * @param $menuArray
     * @return array
     */
    public function add($menuArray){
        $add = [
            'project_id' => $menuArray['project_id'],
            'pre_id' => $menuArray['pre_id'],
            'title' => $menuArray['title'],
            'type' => $menuArray['type'],
            'sort' => $menuArray['sort'],
            'create_user_id' => session('user_id'),
            'status' => 1,
        ];
        $id = db('project_page')->insertGetId($add);
        if(empty($menuArray['pre_id'])){//顶级
            db('project_page')
                ->where(['id'=>$id])
                ->update(['path'=>$id,'level'=>1]);
        }else{//非顶级
            $pre =  db('project_page')->where(['id'=>$menuArray['pre_id']])->find();
            db('project_page')
                ->where(['id'=>$id])
                ->update(['path'=>$pre['path'].','.$id,'level'=>$pre['level']+1]);
        }
        $add['id'] = $id;
        return $add;
    }

    /**
     * 修改菜单
     * @param $menuArray
     * @return int|string
     */
    public function edit($menuArray){
        $add = [
            'title' => $menuArray['title'],
            'sort' => $menuArray['sort'],
        ];
        return db('project_page')
            ->where(['id'=>$menuArray['id']])
            ->update($add);
    }

    /**
     * 单条明细
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function info($id){
        return db('project_page')
            ->where(['id'=>$id])
            ->find();
    }

    /**
     * 删除
     * @param $menuID
     * @return int|string
     */
    public function del($menuID){
        $del = [
            'status' => 0,
            'deleted_at' => date('Y-m-d H:i:s'),
        ];
        $del = db('project_page')
            ->where(['id'=>$menuID])
            ->update($del);
        //写log
        $projectService = new \app\index\service\Project();
        $projectService->addOperateLog($menuID,$projectService::OPERATE_TYPE_DEL);
        return $del;
    }

    /**
     * 检查某个id是否有下级
     * @param $menuID
     * @return int|string
     */
    public function checkChild($menuID){
        return db('project_page')
            ->where(
                [
                    'pre_id'=>$menuID,
                    'status'=>1
                ]
            )
            ->count();
    }

    /**
     * 获取默认页面
     * @param $projectID
     * @return array|false|\PDOStatement|string|Model
     */
    public function getDefault($projectID){
        $map['project_id']  = $projectID;
        $map['type']  = ['<',5];
        $map['status']  = 1;
        return db('project_page')
            ->where($map)
            ->order('sort asc,id asc')
            ->find();
    }
}