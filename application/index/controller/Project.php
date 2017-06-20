<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/20
 * Time: 11:37
 */

namespace app\index\controller;


use think\Request;

class Project extends Base
{

    /**
     * 我建立的项目
     * @param Request $request
     * @return \think\response\View
     */
    public function index(Request $request){
        $projectService = new \app\index\service\Project();
        $lists = $projectService->my();
        return view('/project/lists',['lists'=>$lists]);
    }

    /**
     * 添加项目
     * @param Request $request
     * @return \think\response\View
     */
    public function add(Request $request){
        if(request()->isPost()){
            $projectService = new \app\index\service\Project();
            $projectService->add($request->post());
            $this->success('添加成功',url('/project/index'));
        }else{
            return view('/project/add');
        }
    }
}