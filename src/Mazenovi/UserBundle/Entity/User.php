<?php
// src/Mazenovi/UserBundle/Entity/User.php

namespace Mazenovi\UserBundle\Entity;

use FOS\UserBundle\Propel\User as PropelUser;

// @todo how to use this user class?
class User extends PropelUser
{
    private $dn;
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    public function getDn()
    {
        return $this->dn;
    }
}