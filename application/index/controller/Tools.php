<?php
/**
 * Created by PhpStorm.
 * User: liangshengfeng
 * Date: 2017/7/10
 * Time: 13:59
 */

namespace app\index\controller;
use think\image\Exception;
use think\Request;

class Tools extends Base
{
    /**
     * 获取数据库与表信息
     * @param Request $request
     * @return \think\response\View
     */
    public function createdMDDbDoc(Request $request){
        if(request()->isPost()){
            $hosts = input('post.hosts');
            $port = input('post.port');
            $username = input('post.username');
            $password = input('post.password');
            $dbname = input('post.dbname');
            try{
                $mysqli = new \mysqli($hosts,$username,$password,$dbname,$port);
                //设置编码
                $mysqli->set_charset("utf8");

                $sql = 'select table_name from information_schema.tables where table_schema = \''.$dbname.'\'';
                $query = $mysqli->query($sql);
                $content = '';
                $i = 1;
                while($row = $query->fetch_assoc()){
                    $tableInfo = $mysqli->query('select table_name,table_comment from information_schema.tables where table_schema = \''.$dbname.'\' and table_name = \''.$row['table_name'].'\' order by table_name asc ');
                    while($tableRow = $tableInfo->fetch_assoc()){
                        $content.= '## '.$i.'.'.$tableRow['table_name'].' ['.$tableRow['table_comment'].']';
                        $content.= "\n\n";
                        $content.= $this->showTable($mysqli,$tableRow['table_name']);
                        $i++;
                    }

                }

            }catch (\Exception $e){
                $content = $e->getMessage();
            }
            return view('/tools/db',
                [
                    'hosts' => $hosts,
                    'port' => $port,
                    'username' => $username,
                    'password' => $password,
                    'dbname' => $dbname,
                    'content'=>$content
                ]
            );
        }else{
            return view('/tools/db');
        }
    }

    /**
     * * 显示某个表的字段信息（md格式）
     * @param $mysqli
     * @param $tableName
     * @return string
     */
    public function showTable($mysqli,$tableName)
    {
        $content = '';
        $query = $mysqli->query('show  full  columns from  ' . $tableName);
        $content.= '|字段|类型|默认值|Key|备注|';
        $content.= "\n";
        $content.= '|---|---|---|---|---|';
        $content.= "\n";
        while($line = $query->fetch_assoc()){
            $content.= '|'.$line['Field'];
            $content.= '|'.$line['Type'];
            $content.= '|'.$line['Default'];
            $content.= '|'.$line['Key'];
            $content.= '|'.$line['Extra'].' '.$line['Comment'];
            $content.= "\n";
        }
        $content.= "\n\n";
        return $content;
    }
}