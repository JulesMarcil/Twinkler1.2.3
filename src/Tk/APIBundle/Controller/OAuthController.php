<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use Tk\APIBundle\Entity\AccessToken,
	Tk\APIBundle\Entity\RefreshToken,
	Tk\UserBundle\Entity\User;

use Tk\UserBundle\Entity\Member,
	Tk\GroupBundle\Entity\TGroup,
	Tk\ExpenseBundle\Entity\Expense,
 	Tk\ChatBundle\Entity\Message;

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
                //$this->createDefaultGroup($user);
            }

           	$em = $this->getDoctrine()->getManager();
           	$em->persist($user);
           	$em->flush();
		}
		return $this->getTokenAction($user);
	}

	public function appRegisterAction(Request $request)
	{
		$data = $this->getRequest()->query->all();
		$user_test1 = $this->getDoctrine()->getRepository('TkUserBundle:User')->findByUsername($data['username']);		
		$user_test2 = $this->getDoctrine()->getRepository('TkUserBundle:User')->findByEmail($data['email']);	

		if ($user_test1) {
			$response = new JSONResponse('This username is already taken');
			$response->setStatusCode(500);
			return $response;
		} else if ($user_test2) {
			$response = new JSONResponse('This email is already taken');
			$response->setStatusCode(500);
			return $response;
		}

		$userManager = $this->container->get('fos_user.user_manager');

	    $user = $userManager->createUser();

	    $user->setUsername($data['username']);
	    $user->setEmail($data['email']);
	    $user->setPlainPassword($data['password']);
	    $user->setEnabled(true);
	    $user->setPicture($this->getDoctrine()->getRepository('TkUserBundle:ProfilePicture')->find(1));

	    $userManager->updateUser($user, true);

	    //$this->createDefaultGroup($user);

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

	private function createDefaultGroup($user)
	{
		$em = $this->getDoctrine()->getManager();
	    $currency = $em->getRepository('TkGroupBundle:Currency')->find(1);	

        $group = new TGroup();
        $group->setName('Twinkler team (example)');
        $group->setCurrency($currency);
        $group->setInvitationToken($group->generateInvitationToken());

        $jules  = $em->getRepository('TkUserBundle:User')->find(1);
        $arnaud = $em->getRepository('TkUserBundle:User')->find(2);

        $member = new Member();
        $member->setUser($user);
        $member->setName($user->getUsername());
        $member->setTGroup($group);

        $jules_member = new Member();
        $jules_member->setUser($jules);
        $jules_member->setName($jules->getUsername());
        $jules_member->setTGroup($group);

        $arnaud_member = new Member();
        $arnaud_member->setUser($arnaud);
        $arnaud_member->setName($arnaud);
        $arnaud_member->setTGroup($group);

        $message1 = New Message();
		$date = new \DateTime('now');
		$message1->setTimestamp($date->getTimestamp());
		$message1->setBody('As you can see I just added an expense for the Breakfast I paid to Jules yesterday, feel free to add yours or create a new group with your friends');
		$message1->setAuthor($arnaud_member);
		$message1->setGroup($group);

        $expense = new Expense();
        $expense->setAmount(20);
        $expense->setName('Breakfast at Tiffany\'s');
        $expense->setAddedDate(new \DateTime('now'));
        $expense->setDate(new \Datetime('yesterday'));
        $expense->setActive(true);
        $expense->setAuthor($arnaud_member);
        $expense->setOwner($arnaud_member);
        $expense->setGroup($group);
        $expense->addUser($jules_member);
        $expense->addUser($arnaud_member);

        $message2 = New Message();
        $date = new \DateTime('now');
		$message2->setTimestamp($date->getTimestamp());
		$message2->setBody('Hello '.$user->getUsername().' and welcome to Twinkler! You can use this chat to let us know what you think about Twinkler');
		$message2->setAuthor($jules_member);
		$message2->setGroup($group);

        $picture = $em ->getRepository('TkUserBundle:ProfilePicture')->find(1);	        
        $user->setPicture($picture);

        $em->persist($user);
        $em->persist($group);
        $em->persist($member);
        $em->persist($jules_member);
        $em->persist($arnaud_member);
        $em->persist($message1);
        $em->persist($expense);
        $em->persist($message2);
        $em->flush();
	}
}
