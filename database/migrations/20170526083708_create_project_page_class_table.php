<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateProjectPageClassTable extends Migrator
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
        $table = $this->table('project_page_class',array('comment' => '项目文章分类表'));
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
            ->addColumn('class_name', 'string',
                array(
                    'length' => 255,
                    'comment' => '分类名称',
                )
            )
            ->addColumn('path', 'string',
                array(
                    'length' => 255,
                    'comment' => 'id路径',
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
