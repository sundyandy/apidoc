<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/19
 * Time: 16:01
 */

namespace app\index\controller;


use think\Controller;
use think\Request;

class Base extends Controller
{
    public function __construct(){
        $this->checkLogin();
    }

    /**
     * 检查登录
     * @return bool|void
     */
    public function checkLogin(){
        $request = Request::instance();
        $c = $request->controller();
        $a = $request->action();
        $uri = strtolower($c.'/'.$a);
        $white = config('config.no_login_check');
        if(!in_array($uri,$white)){
            if(empty(session('user_id'))){
                return $this->error('请先登录',url('/user/login'));
            }
        }else{
            return true;
        }
    }
}