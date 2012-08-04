<?php

namespace Mazenovi\UserBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'fos_user_extra' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Mazenovi.UserBundle.Model.map
 */
class FosUserExtraTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Mazenovi.UserBundle.Model.map.FosUserExtraTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('fos_user_extra');
        $this->setPhpName('FosUserExtra');
        $this->setClassname('Mazenovi\\UserBundle\\Model\\FosUserExtra');
        $this->setPackage('src.Mazenovi.UserBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignPrimaryKey('FOS_USER_ID', 'FosUserId', 'INTEGER' , 'fos_user', 'ID', true, null, null);
        $this->addColumn('HOME_PAGE', 'HomePage', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'FOS\\UserBundle\\Propel\\User', RelationMap::MANY_TO_ONE, array('fos_user_id' => 'id', ), null, null);
    } // buildRelations()

} // FosUserExtraTableMap
