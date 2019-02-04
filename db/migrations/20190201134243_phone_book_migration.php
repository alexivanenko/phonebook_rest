<?php

use Phinx\Migration\AbstractMigration;

class PhoneBookMigration extends AbstractMigration
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
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
	    $users = $this->table('phonebook');
	    $users->addColumn('first_name', 'string', ['limit' => 20])->addIndex('first_name')
			->addColumn('last_name', 'string', ['limit' => 20, 'null' => true])
			->addColumn('number', 'string', ['limit' => 30])
			->addColumn('country_code', 'string', ['limit' => 2])
			->addColumn('timezone', 'string', ['limit' => 20])
			->addColumn('inserted_on', 'datetime')
			->addColumn('updated_on', 'datetime', ['null' => true])
			->create();
    }
}
