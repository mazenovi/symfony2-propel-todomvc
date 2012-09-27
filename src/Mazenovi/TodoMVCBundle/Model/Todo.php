<?php

namespace Mazenovi\TodoMVCBundle\Model;

use Mazenovi\TodoMVCBundle\Model\om\BaseTodo;
use FOS\UserBundle\Propel\UserQuery;


/**
 * Skeleton subclass for representing a row from the 'todo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.src.Mazenovi.TodoMVCBundle.Model
 */
class Todo extends BaseTodo
{

	private $username = 'anonymous';
    private $permissions = array();

    public function getUsername()
    {
        $user = UserQuery::create()->findPk($this->fos_user_id);
        return $user->getUsername();
    }
    
    public function setUsername()
    {
        return true;
    }

    public function addPermission($perm)
    {
        array_push($this->permissions, $perm);
    }

    public function addFieldPermission($perm, $field)
    {
        if(!array_key_exists('fields', $this->permissions))
        {
            $this->permissions['fields'] = array();
        }
        if(!array_key_exists($field, $this->permissions['fields']))
        {
            $this->permissions['fields'][$field] = array();
        }
        array_push($this->permissions['fields'][$field], $perm);
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setPermissions()
    {
        return true;
    }

} // Todo
