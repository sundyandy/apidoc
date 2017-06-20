<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateProjectApiRequestTable extends Migrator
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
        $table = $this->table('project_api_request',array('comment' => 'api入参表'));
        $table
            ->addColumn('api_id', 'integer',
                array(
                    'length' => MysqlAdapter::INT_REGULAR,
                    'comment' => 'api id，关联api.id',
                )
            )
            ->addColumn('param', 'string',
                array(
                    'length' => 255,
                    'comment' => '字段名称',
                )
            )
            ->addColumn('is_must', 'integer',
                array(
                    'length' => MysqlAdapter::INT_TINY,
                    'default' => 0,
                    'comment' => '是否必填。0否 1是',
                )
            )
            ->addColumn('type', 'string',
                array(
                    'length' => 255,
                    'comment' => '参数类型（string/int等）',
                )
            )
            ->addColumn('remark', 'string',
                array(
                    'length' => 255,
                    'comment' => '备注、说明',
                )
            )
            ->addColumn('created_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP'))
            ->addColumn('updated_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP','update'=>'CURRENT_TIMESTAMP'))
            ->create();
    }
}
