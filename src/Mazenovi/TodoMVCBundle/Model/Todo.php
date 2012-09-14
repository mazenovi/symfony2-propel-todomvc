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

	public function getDisplayName()
	{
		// @todo this value should be return in JSON response's todo
		return 'pipo';
	}

	public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        // @todo remove this part when serializer expose virtual_method
        $returned_value = parent::hydrate($row, $startcol = 0, $rehydrate = false);
        if($this->fos_user_id)
        {
        	$user = UserQuery::create()->findPk($this->fos_user_id);
        	$this->username = $user->getUsername();
       	}
        return $returned_value;
    }
    
} // Todo
