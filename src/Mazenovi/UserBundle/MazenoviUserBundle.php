<?php

namespace Mazenovi\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MazenoviUserBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
