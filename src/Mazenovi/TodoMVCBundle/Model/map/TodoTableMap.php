<?php

namespace Mazenovi\TodoMVCBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'todo' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Mazenovi.TodoMVCBundle.Model.map
 */
class TodoTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'src.Mazenovi.TodoMVCBundle.Model.map.TodoTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
		// attributes
		$this->setName('todo');
		$this->setPhpName('Todo');
		$this->setClassname('Mazenovi\\TodoMVCBundle\\Model\\Todo');
		$this->setPackage('src.Mazenovi.TodoMVCBundle.Model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('CONTENT', 'Content', 'LONGVARCHAR', false, null, null);
		$this->addColumn('DONE', 'Done', 'BOOLEAN', false, 1, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // TodoTableMap
