<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlertProjectDocument extends Migrator
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
        $table = $this->table('project_document');
        $table->addColumn('apis', 'string',
            array(
            'null' => true,
            'length' => 255,
            'comment' => '关联api的id，格式为以半角逗号分隔的字符串，如1,2,3',
            )
        )->update();
    }
}
