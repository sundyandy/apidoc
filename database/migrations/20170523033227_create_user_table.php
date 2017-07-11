<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateUserTable extends Migrator
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
        $table = $this->table('user',array('comment' => '用户表'));
        $table
            ->addColumn('username', 'string',
                    array(
                    'length' => 255,
                    'comment' => '用户名',
                    )
                )
            ->addColumn('salt', 'char',
                array(
                    'length' => 8,
                    'comment' => '加密盐值',
                )
            )
            ->addColumn('password', 'char',
                array(
                    'length' => 32,
                    'comment' => '密码（加密后）',
                )
            )
            ->addColumn('realname', 'string',
                array(
                    'length' => 255,
                    'comment' => '昵称、真实姓名',
                )
            )
            ->addColumn('status', 'integer',
                array(
                    'length' => MysqlAdapter::INT_TINY,
                    'comment' => '用户状态 1可用 0冻结/删除',
                )
            )
            ->addColumn('created_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP'))
            ->addColumn('updated_at', 'timestamp',array('default'=>'CURRENT_TIMESTAMP','update'=>'CURRENT_TIMESTAMP'))
            ->addColumn('deleted_at', 'timestamp', array('null' => true))
            ->addIndex(array('username'),array('unique' => true))
            ->create();
    }
}
