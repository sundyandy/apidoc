<?php

/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/6/20
 * Time: 10:26
 */
namespace app\index\service;
use think\Model;
class User extends Model
{
    /**
     * @param $userArray
     * @return int|string
     */
    public function reg($userArray){
        $userArray['salt'] = getRandChar(8);
        $userArray['password'] = createPassword($userArray['password'],$userArray['salt']);
        $userArray['status'] = 1;
        return db('user')->insert($userArray);
    }

    /**
     * @param $username
     * @return array|false|\PDOStatement|string|Model
     */
    public function getUserByName($username){
        return db('user')
            ->where(['username'=>$username])
            ->find();
    }

    /**
     * @param $userID
     * @return array|false|\PDOStatement|string|Model
     */
    public function info($userID){
        return db('user')
            ->where(['id'=>$userID])
            ->find();
    }

    /**
     * 用户列表
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function lists(){
        return db('user')->select();
    }
}