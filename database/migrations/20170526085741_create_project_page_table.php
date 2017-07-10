<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateProjectPageTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // create the table
        $table = $this->table('project_page',array('comment' => '项目文章表'));
        $table
            ->addColumn('project_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '项目id，关联project.id',
                )
            )
            ->addColumn('pre_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '上级id',
                )
            )
            ->addColumn('path', 'string',
                array(
                    'length' => 255,
                    'comment' => 'id路径',
                )
            )
            ->addColumn('title', 'string',
                array(
                    'length' => 255,
                    'comment' => '页面名称',
                )
            )
            ->addColumn('sort', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'default' => 0,
                    'comment' => '排序，数字越小排越前',
                )
            )
            ->addColumn('type', 'integer',
                array(
                    'length' => MysqlAdapter::INT_TINY,
                    'default' => 0,
                    'comment' => '类型 1api 2数据字典 3错误码 4文章 5目录',
                )
            )
            ->addColumn('level', 'integer',
                array(
                    'length' => MysqlAdapter::INT_TINY,
                    'default' => 0,
                    'comment' => '等级',
                )
            )
            ->addColumn('create_user_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '创建人用户id',
                )
            )
            ->addColumn('status', 'integer',
                array(
                    'length' => MysqlAdapter::INT_TINY,
                    'comment' => '状态 1有效 0删除',
                )
            )
            ->addColumn('created_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP'))
            ->addColumn('updated_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP','update'=>'CURRENT_TIMESTAMP'))
            ->addColumn('deleted_at', 'timestamp', array('null' => true))
            ->create();
    }
}
