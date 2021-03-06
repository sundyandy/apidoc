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
        $myJoin = $projectService->myJoin();
        return view('/project/lists',[
            'lists'=>$lists,
            'join_lists'=>$myJoin,
        ]);
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

    /**
     * 项目明细
     * @param Request $request
     * @return \think\response\View
     */
    public function info(Request $request){
        $projectService = new \app\index\service\Project();
        $menuService = new \app\index\service\Menu();
        //项目详情
        $projectID = Request::instance()->param('id');
        $projectInfo = $projectService->info($projectID);

        //检查用户的项目权限
        $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
        if($auth['view'] == 0){
            $this->error('无权限查看本项目');
        }

        //左侧菜单
        $projectMenu = $menuService->lists($projectID);

        //页面
        $pageID = Request::instance()->param('page_id');
        if(!$pageID){
            //显示操作记录
            $getDefault = $projectService->getProjectOperateLog($projectID);
            return view('/page/default',
                [
                    'project_info'=>$projectInfo,
                    'menu_list'=>$projectMenu,
                    'auth' => $auth,
                    'content' => $getDefault
                ]
            );
        }else{
            $menuService = new \app\index\service\Menu();
            $pageService = new \app\index\service\Page();
            $pageInfo = $menuService->info($pageID);
            if($pageInfo['type'] == $menuService::TYPE_API){
                //api基本信息
                $apiInfo = $pageService->getApiDoc($projectID,$pageID);
                //输出
                return view('/page/api_show',
                    [
                        'project_info'=>$projectInfo,
                        'page_info'=>$pageInfo,
                        'api_info'=>$apiInfo,
                        'page_id'=>$pageID,
                        'menu_list'=>$projectMenu,
                        'auth' => $auth,
                    ]
                );
            }
            if($pageInfo['type'] == $menuService::TYPE_ARTICLE){
                //基本信息
                $articleInfo = $pageService->getArticle($projectID,$pageID);
                if(!empty($articleInfo['info']['apis'])){
                    $apis = $menuService->getApisByIDs($articleInfo['info']['apis']);
                }else{
                    $apis = [];
                }
                //输出
                return view('/page/article_show',
                    [
                        'project_info'=>$projectInfo,
                        'page_info'=>$pageInfo,
                        'article_info'=>$articleInfo,
                        'page_id'=>$pageID,
                        'menu_list'=>$projectMenu,
                        'auth' => $auth,
                        'apis' => $apis,
                    ]
                );
            }
        }
    }


    /**
     * 项目菜单列表
     * @param Request $request
     * @return \think\response\View
     */
    public function menu(Request $request){
        $menuService = new \app\index\service\Menu();
        if(request()->isPost()){
            $add = input('post.');
            $return = $menuService->add($add);
            $type = input('post.type');
            switch ($type){
                case '1':
                    $url = url('project/apidocedit',['page_id'=>$return['id']]);
                    break;
                case '4':
                    $url = url('project/articleedit',['page_id'=>$return['id']]);
                    break;
                default:
                    $url = '';
                    break;
            }
            $this->success('添加成功',$url,'',1);
        }else{
            $projectService = new \app\index\service\Project();
            //项目详情
            $projectID = Request::instance()->param('project_id');
            $projectInfo = $projectService->info($projectID);

            //检查用户的项目权限
            $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
            if($auth['edit'] == 0){
                $this->error('无权限编辑本项目');
            }

            //菜单
            $projectMenu = $menuService->lists($projectID);
            $projectMenuV2 = $menuService->listsV2($projectID);
            ############ for test ############
//            echo '<pre>';print_r($projectMenu);die('###');
            ############ for test ############
            //输出
            return view('/project/menuV2',
                [
                    'project_info'=>$projectInfo,
                    'menu_list'=>$projectMenu,
                    'menu_list_v2'=>$projectMenuV2,
                    'auth' => $auth
                ]
            );
        }
    }

    /**
     * 编辑菜单
     * @param Request $request
     */
    public function menuEdit(Request $request){
        $menuService = new \app\index\service\Menu();
        if(request()->isPost()){
            $projectService = new \app\index\service\Project();
            //项目详情
            $id = input('post.id');
            $menuInfo = $menuService->info($id);
            if(empty($menuInfo)){
                $this->error('编辑条目不存在');
            }
            if(empty($menuInfo['status'])){
                $this->error('编辑条目已删除');
            }
            $projectInfo = $projectService->info($menuInfo['project_id']);

            //检查用户的项目权限
            $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
            if($auth['edit'] == 0){
                $this->error('无权限编辑本项目');
            }

            //编辑
            $edit = input('post.');
            $menuService->edit($edit);
            $this->success('编辑成功',null,'',1);
        }else{
            $this->error('缺少参数');
        }
    }

    public function menuDel(Request $request){
        $menuService = new \app\index\service\Menu();
        $menuID = Request::instance()->param('id');
        if($menuID){
            //菜单详情
            $menuInfo = $menuService->info($menuID);
            if(empty($menuInfo)){
                $this->error('条目不存在');
            }
            if(empty($menuInfo['status'])){
                $this->error('条目已删除');
            }
            $checkIsChild = $menuService->checkChild($menuID);
            if($checkIsChild){
                $this->error('条目拥有下级，不允许删除');
            }
            //项目详情
            $projectService = new \app\index\service\Project();
            $projectInfo = $projectService->info($menuInfo['project_id']);

            //检查用户的项目权限
            $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
            if($auth['edit'] == 0){
                $this->error('无权限编辑本项目');
            }

            //编辑
            $menuService->del($menuID);
            $this->success('删除成功');
        }else{
            $this->error('缺少参数');
        }
    }

    public function editPage(Request $request){
        $pageID = Request::instance()->param('id');
        $type = Request::instance()->param('type');
        if($type == '1'){
            $this->redirect(url('project/apiDocEdit',['page_id'=>$pageID]));
        }
        if($type == '4'){
            $this->redirect(url('project/articleEdit',['page_id'=>$pageID]));
        }
    }

    /**
     * api文档
     * @param Request $request
     * @return \think\response\View
     */
    public function apiDocEdit(Request $request){
        $projectService = new \app\index\service\Project();
        $menuService = new \app\index\service\Menu();
        $pageService = new \app\index\service\Page();
        if(request()->isPost()){
            //基础信息
            $apiInfo = [
                'project_id' => input('post.project_id'),
                'page_id' => input('post.page_id'),
                'descrition' => input('post.descrition',''),
                'uri' => input('post.uri',''),
                'method' => input('post.method',''),
                'response' => input('post.response',''),
                'remark' => input('post.api_remark',''),
                'create_user_id' => session('user_id'),
                'status' => 1,
                'api_id' => input('post.api_id',0),
            ];
            $apiID = $pageService->addApiInfo($apiInfo);

            //request new
            if(isset($_POST['new_param'])){
                $requestCount = count($_POST['new_param']);
                if($requestCount && !empty($_POST['new_param'][0])){
                    $param = array_values($_POST['new_param']);
                    $is_must = array_values($_POST['new_is_must']);
                    $type = array_values($_POST['new_type']);
                    $remark = array_values($_POST['new_remark']);
                    for($i=0;$i<$requestCount;$i++){
                        $request = [
                            'api_id' => $apiID,
                            'param' => $param[$i],
                            'is_must' => $is_must[$i],
                            'type' => $type[$i],
                            'remark' => $remark[$i],
                        ];
                        $pageService->addApiRequest($request);
                    }
                }
            }
            if(!empty($_POST['param'])){
                foreach($_POST['param'] as $key=>$param){
                    $request = [
                        'param' => input("post.param.{$key}",''),
                        'is_must' => input("post.is_must.{$key}",0),
                        'type' => input("post.type.{$key}",'string'),
                        'remark' => input("post.remark.{$key}",''),
                    ];
                    $pageService->editApiRequest($key,$request);
                }
            }


            $this->success('编辑成功');

        }else{
            //页面详情
            $pageID = Request::instance()->param('page_id');
            $pageInfo = $menuService->info($pageID);
            if(empty($pageInfo) || $pageInfo['status'] <> '1'){
                $this->error('无条目不存在');
            }
            if($pageInfo['type'] == '5'){
                $this->error('无条目为目录');
            }
            //项目详情
            $projectInfo = $projectService->info($pageInfo['project_id']);
            //检查用户的项目权限
            $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
            if($auth['view'] == 0){
                $this->error('无权限查看本项目');
            }

            //api基本信息
            $apiInfo = $pageService->getApiDoc($pageInfo['project_id'],$pageID);
            //输出
            return view('/page/api',
                [
                    'project_info'=>$projectInfo,
                    'page_info'=>$pageInfo,
                    'api_info'=>$apiInfo,
                    'page_id'=>$pageID,
                ]
            );
        }

    }

    /**
     * 文章
     * @param Request $request
     * @return \think\response\View
     */
    public function articleEdit(Request $request){
        $projectService = new \app\index\service\Project();
        $menuService = new \app\index\service\Menu();
        $pageService = new \app\index\service\Page();
        if(request()->isPost()){
            //基础信息
            $articleInfo = [
                'project_id' => input('post.project_id'),
                'page_id' => input('post.page_id'),
                'description' => input('post.description',''),
                'content' => input('post.content',''),
                'create_user_id' => session('user_id'),
                'db_id' => input('post.db_id',0),
                'apis' => input('post.apis',null),
            ];
            $pageService->addArticle($articleInfo);
            $this->success('编辑成功');
        }else{
            //页面详情
            $pageID = Request::instance()->param('page_id');
            $pageInfo = $menuService->info($pageID);
            if(empty($pageInfo) || $pageInfo['status'] <> '1'){
                $this->error('无条目不存在');
            }
            if($pageInfo['type'] == '5'){
                $this->error('无条目为目录');
            }
            //项目详情
            $projectInfo = $projectService->info($pageInfo['project_id']);
            //检查用户的项目权限
            $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
            if($auth['view'] == 0){
                $this->error('无权限查看本项目');
            }

            //基本信息
            $articleInfo = $pageService->getArticle($pageInfo['project_id'],$pageID);

            //所有接口
            $apis = $menuService->getApis($pageInfo['project_id']);
            if(!empty($articleInfo['info']['apis'])){
                $selectdApis = $menuService->getApisByIDs($articleInfo['info']['apis']);
            }else{
                $selectdApis = [];
            }
            //输出
            return view('/page/article',
                [
                    'project_info'=>$projectInfo,
                    'page_info'=>$pageInfo,
                    'article_info'=>$articleInfo,
                    'page_id'=>$pageID,
                    'apis'=>$apis,
                    'selectd_apis'=>$selectdApis,
                ]
            );
        }

    }

    public function user(Request $request){
        $projectService = new \app\index\service\Project();
        $menuService = new \app\index\service\Menu();
        $pageService = new \app\index\service\Page();
        if(request()->isPost()){
            $type = $_POST['type'];
            $projectID = input('project_id');
            if(!empty($type)){
                foreach ($type as $key=>$value){
                    $projectService->setProjectUser($projectID,$key,$value);
                }
            }
            $this->success('编辑成功');
        }else{
            $projectID = Request::instance()->param('id');
            //项目详情
            $projectInfo = $projectService->info($projectID);
            //检查用户的项目权限
            $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
            if($auth['edit'] == 0){
                $this->error('无权限编辑本项目');
            }
            //左侧菜单
            $projectMenu = $menuService->lists($projectID);
            $userService = new \app\index\service\User();
            $userLists = $userService->lists();
            $projectUserListsTmp = $projectService->getProjectUser($projectID);
            if(!empty($projectUserListsTmp)){
                foreach($projectUserListsTmp as $line){
                    $projectUserLists[$line['user_id']] = $line['type'];
                }
            }else{
                $projectUserLists = [];
            }

            //输出
            return view('/project/user',
                [
                    'project_info'=>$projectInfo,
                    'user_lists'=>$userLists,
                    'project_user_lists'=>$projectUserLists,
                    'menu_list'=>$projectMenu,
                    'auth' => $auth,
                ]
            );
        }
    }

    /**
     * 删除入参
     * @param Request $request
     */
    public function delParam(Request $request){
        $projectService = new \app\index\service\Project();

        $paramID = input('param_id');
        $projectInfo = $projectService->getProjectFromApiParamID($paramID);
        //检查用户的项目权限
        $auth = $projectService->checkUserProjectAuth($projectInfo,session('user_id'));
        if($auth['edit'] == 0){
            $this->error('无权限编辑本项目');
        }
        //删除
        $projectService->delApiParam($paramID);
        $this->success('删除成功');


    }
}