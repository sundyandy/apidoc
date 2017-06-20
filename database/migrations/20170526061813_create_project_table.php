<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateProjectTable extends Migrator
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
        $table = $this->table('project',array('comment' => '项目表'));
        $table
            ->addColumn('project_name', 'string',
                array(
                    'length' => 255,
                    'comment' => '项目名称',
                )
            )
            ->addColumn('description', 'string',
                array(
                    'length' => 255,
                    'comment' => '项目描述',
                )
            )
            ->addColumn('create_user_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '创建人用户id',
                )
            )
            ->addColumn('open_type', 'integer',
                array(
                    'length' => MysqlAdapter::INT_TINY,
                    'comment' => '公开类型 1所有人可看 2只允许部分人看（viewer_user_ids）',
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
