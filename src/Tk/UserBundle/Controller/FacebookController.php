<?php

namespace Tk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tk\UserBundle\Entity\User;

class FacebookController extends Controller
{
	public function loginButtonAction()
	{
		$fbk = $this->get('fos_facebook.api');
        $facebook_login = $fbk->getLoginUrl();

        return $this->render('TkUserBundle:Facebook:button.html.twig', array(
        	'facebook_login' => $facebook_login
        	));
	}

	public function loginUrlAction()
	{
		$fbk = $this->get('fos_facebook.api');
        $facebook_login = $fbk->getLoginUrl();

        return $this->render('TkUserBundle:Facebook:login_url.html.twig', array(
        	'facebook_login' => $facebook_login
        	));
	}
}