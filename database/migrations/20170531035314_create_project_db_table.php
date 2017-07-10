<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateProjectDbTable extends Migrator
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
        $table = $this->table('project_db',array('comment' => '数据字典'));
        $table
            ->addColumn('project_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '项目id，关联project.id',
                )
            )
            ->addColumn('page_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '页面id，关联project_page.id',
                )
            )
            ->addColumn('title', 'string',
                array(
                    'length' => 255,
                    'comment' => '字典名称',
                )
            )
            ->addColumn('description', 'string',
                array(
                    'length' => 255,
                    'comment' => '备注、说明',
                )
            )
            ->addColumn('content', 'text',
                array(
                    'comment' => '内容（json格式）',
                )
            )
            ->addColumn('create_user_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => '创建者id',
                )
            )
            ->addColumn('created_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP'))
            ->addColumn('updated_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP','update'=>'CURRENT_TIMESTAMP'))
            ->create();
    }
}
