<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/20
 * Time: 15:44
 */

namespace app\index\service;
use think\Model;
use think\Request;

class Page extends Model
{
    public function info($pageID){
        $menuService = new \app\index\service\Menu();
        $menuInfo = $menuService->info($pageID);
        switch($menuInfo['type']){
            case '1': //api
                $info = $this->getApiDoc(1,$pageID);
                $info['menu'] = $menuInfo;
                $content = $this->_apiView($info);
                break;
        }
    }

    /**
     * @param $projectID
     * @param $pageID
     * @return mixed
     */
    public function getApiDoc($projectID,$pageID){
        $userService = new \app\index\service\User();
        //基本信息
        $api['info'] = db('project_api')
            ->where(
                [
                    'project_id' => $projectID,
                    'page_id' => $pageID,
                ]
            )
            ->find();
        //作者
        $api['author'] = $userService->info($api['info']['create_user_id']);
        //入参
        $api['request'] = db('project_api_request')
            ->where(
                [
                    'api_id' => $api['info']['id'],
                ]
            )
            ->select();
        //回参
        $api['response'] = db('project_api_response')
            ->where(
                [
                    'api_id' => $api['info']['id'],
                ]
            )
            ->find();
        return $api;
    }

    /**
     * @param $apiInfo
     * @return string
     */
    public function _apiView($apiInfo){
        $content = '
            <h1>'.$apiInfo['menu']['title'].'<small>'.$apiInfo['author']['realname'].'</small></h1>
        ';
        $content.= '<hr>';
        $content.= '
            <h3>简述</h3>
            <blockquote>
                '.$apiInfo['info']['descrition'].'
            </blockquote>
        ';
        $content.= '<hr>';
        $content.= '
            <h3>请求URI</h3>
            <blockquote>
                '.$apiInfo['info']['uri'].'
            </blockquote>
        ';
        $content.= '<hr>';
        $content.= '
            <h3>请求方法</h3>
            <blockquote>
                '.$apiInfo['info']['method'].'
            </blockquote>
        ';
        $content.= '<hr>';
        $content.= '
            <h3>入参</h3>
            <table class="table table-striped">
        ';
        foreach($apiInfo['request'] as $request){
            $content.='
                <tr>
                    <td>'.$request['param'].'</td>
                    <td>'.$request['is_must'].'</td>
                    <td>'.$request['type'].'</td>
                    <td>'.$request['remark'].'</td>
                </tr>
            ';
        }
        $content.= '       
            </table>
        ';
        return $content;
    }

    /**
     * 添加project_api表
     * @param $apiArray
     * @return int|string
     */
    public function addApiInfo($apiArray){
        $id = $apiArray['api_id'];
        unset($apiArray['api_id']);
        if(!empty($id)){
            db('project_api')->where(['id'=>$id])->update($apiArray);
            return $id;
        }else{
            return db('project_api')->insertGetId($apiArray);
        }
    }

    /**
     * 添加project_api_request表
     * @param $requestArray
     * @return int|string
     */
    public function addApiRequest($requestArray){
        return db('project_api_request')->insertGetId($requestArray);
    }

    /**
     * 修改project_api_request表
     * @param $id
     * @param $requestArray
     * @return int|string
     */
    public function editApiRequest($id,$requestArray){
        return db('project_api_request')->where(['id'=>$id])->update($requestArray);
    }

    /**
     * 获取文章
     * @param $projectID
     * @param $pageID
     * @return mixed
     */
    public function getArticle($projectID,$pageID){
        $userService = new \app\index\service\User();
        //基本信息
        $db['info'] = db('project_document')
            ->where(
                [
                    'project_id' => $projectID,
                    'page_id' => $pageID,
                ]
            )
            ->find();
        //作者
        $db['author'] = $userService->info($db['info']['create_user_id']);
        return $db;
    }

    /**
     * @param $dbArray
     * @return int|string
     */
    public function addArticle($dbArray){
        $id = $dbArray['db_id'];
        unset($dbArray['db_id']);
        if(!empty($id)){
            db('project_document')->where(['id'=>$id])->update($dbArray);
            return $id;
        }else{
            return db('project_document')->insertGetId($dbArray);
        }
    }


}