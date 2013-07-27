<?php

namespace Tk\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TkUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
