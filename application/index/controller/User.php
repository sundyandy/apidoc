<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/19
 * Time: 16:01
 */

namespace app\index\controller;

use app\index\controller\Base;
use think\Request;
use think\Session;
class User extends Base
{
    /**
     * 注册
     * @param Request $request
     * @return \think\response\View
     */
    public function reg(Request $request){
        if(request()->isPost()){
            $userService = new \app\index\service\User();
            $check = $userService->getUserByName(input('post.username'));
            if(!empty($check)){
                $this->error('相同的账号已存在');
            }
            $userArray = [
                'username' => input('post.username'),
                'password' => input('post.password'),
                'realname' => input('post.realname'),
            ];
            $reg = $userService->reg($userArray);
            $this->success('注册成功',url('user/login'));
        }else{
            return view('/user/reg');
        }
    }

    /**
     * 登录
     * @param Request $request
     * @return \think\response\View
     */
    public function login(Request $request){
        if(request()->isPost()){
            $userService = new \app\index\service\User();
            $check = $userService->getUserByName(input('post.username'));
            if(empty($check)){
                $this->error('账号不存在');
            }
            if($check['status'] != 1){
                $this->error('账号已被冻结');
            }

            $password = createPassword(input('post.password'),$check['salt']);
            if($password != $check['password']){
                $this->error('密码不正确');
            }
            Session::set('username',$check['username']);
            Session::set('realname',$check['realname']);
            Session::set('user_id',$check['id']);
            $this->success('登录成功',url('project/index'));
        }else{
            return view('/user/login');
        }
    }

    /**
     * 登出
     */
    public function logout(){
        Session::clear();
        return redirect('user/login');
    }

}