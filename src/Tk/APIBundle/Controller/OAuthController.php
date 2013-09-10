<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use Tk\APIBundle\Entity\AccessToken,
	Tk\APIBundle\Entity\RefreshToken,
	Tk\UserBundle\Entity\User;

use	FOS\UserBundle\Controller\RegistrationController;

class OAuthController extends Controller
{
    public function facebookTokenAction(Request $request)
    {
    	$facebook_access_token = $request->query->get('facebook_access_token');

    	$fbk = $this->get('fos_facebook.api');
    	$fbk->setAccessToken($facebook_access_token);
		$fbdata = $fbk->api('/me');

		$user = $this->getDoctrine()->getRepository('TkUserBundle:User')->findOneBy(array('facebookId' => $fbdata['id']));

		if (!$user) {
			if (!empty($fbdata)) {
                $user = new User();
                $user->setEnabled(true);
                $user->setPassword('');
 
                $user->setFBData($fbdata); // Ici on passe les données Facebook à notre classe User afin de la mettre à jour
            }

           	$em = $this->getDoctrine()->getManager();
           	$em->persist($user);
           	$em->flush();
		}
		return $this->getTokenAction($user);
	}

	public function appRegisterAction(Request $request)
	{
		$userManager = $this->container->get('fos_user.user_manager');
		$data = $this->getRequest()->query->all();

	    $user = $userManager->createUser();

	    $user->setUsername($data['username']);
	    $user->setEmail($data['email']);
	    $user->setPlainPassword($data['password']);
	    $user->setEnabled(true);
	    $user->setPicture($this->getDoctrine()->getRepository('TkUserBundle:ProfilePicture')->find(1));

	    $userManager->updateUser($user, true);

	    $mailer = $this->get('mailer');

	    $message = \Swift_Message::newInstance();
        $message->setSubject('Thank you for joining')
                ->setFrom(array('emily@twinkler.co' => 'Emily from Twinkler'))
                ->setTo($user->getEmail())
                ->setContentType('text/html')
                ->setBody($this->renderView(':emails:joinedApp.email.twig', array('user' => $user)))
        ;
        $mailer->send($message);

	    return $this->getTokenAction($user);
	}

	private function getTokenAction($user)
	{
		$client = $this->getDoctrine()->getRepository('TkAPIBundle:Client')->find(9);

		$now = new \Datetime('now');
		$now = $now->getTimestamp();

		$access_token = $this->genAccessToken();
		$token = new AccessToken();
		$token->setToken($access_token);
		$token->setClient($client);
		$token->setUser($user);
		$token->setExpiresAt($now+360);

		$refresh_token = $this->genAccessToken();
		$refresh = new RefreshToken();
		$refresh->setToken($refresh_token);
		$refresh->setClient($client);
		$refresh->setUser($user);
		$refresh->setExpiresAt($now+1209600);

		$em = $this->getDoctrine()->getManager();
		$em->persist($token);
		$em->persist($refresh);
		$em->flush();

        return new JsonResponse(array('access_token'  => $token->getToken(), 
							          'refresh_token' => $refresh->getToken()
							        	));
    }

    private function genAccessToken() 
    {
		if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
		  $randomData = file_get_contents('/dev/urandom', false, null, 0, 100);
		} elseif (function_exists('openssl_random_pseudo_bytes')) { // Get 100 bytes of pseudo-random data
		  $bytes = openssl_random_pseudo_bytes(100, $strong);
		  if (true === $strong && false !== $bytes) {
		    $randomData = $bytes;
		  }
		}
		// Last resort: mt_rand
		if (empty($randomData)) { // Get 108 bytes of (pseudo-random, insecure) data
		  $randomData = mt_rand().mt_rand().mt_rand().uniqid(mt_rand(), true).microtime(true).uniqid(mt_rand(), true);
		}
		return rtrim(strtr(base64_encode(hash('sha256', $randomData)), '+/', '-_'), '=');
	}
}
