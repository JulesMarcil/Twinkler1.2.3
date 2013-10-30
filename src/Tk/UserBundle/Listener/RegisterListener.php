<?php
 
namespace Tk\UserBundle\Listener;
 
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;


use Symfony\Component\Routing\Router; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Symfony\Bundle\SwiftmailerBundle\Swift_Mailer;
use Symfony\Bundle\TwigBundle\TwigEngine;

use Tk\UserBundle\Entity\Member,
	Tk\GroupBundle\Entity\TGroup,
	Tk\ExpenseBundle\Entity\Expense,
 	Tk\ChatBundle\Entity\Message;
/**
 * Custom login listener.
 */
class RegisterListener implements EventSubscriberInterface
{
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
    /** @var \Symfony\Component\Routing\Router */
    private $router;
    
    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    private $dispatcher;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcher */
    private $mailer;

    /** @var \Symfony\Bundle\TwigBundle\TwigEngine */
    private $template;

	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
     * @param Router          $router
     * @param Dispatcher      $dispatcher
     * @param Mailer          $mailer
     * @param Template        $template
	 */

	public function __construct(Doctrine $doctrine, Router $router, EventDispatcher $dispatcher, \Swift_Mailer $mailer, TwigEngine $template)
	{
		$this->em         = $doctrine->getManager();
        $this->router     = $router;
        $this->dispatcher = $dispatcher;
        $this->mailer     = $mailer;
        $this->template   = $template;
	}

	public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }
	
	/**
	 * Do the magic.
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onRegistrationSuccess(FormEvent $event)
	{
        $user = $event->getForm()->getData();
        $picture = $this->em ->getRepository('TkUserBundle:ProfilePicture')->find(1);           
        $user->setPicture($picture);
        $this->em->persist($user);
        $this->em->flush();

        $message = \Swift_Message::newInstance();
        $message->setSubject('Thank you for joining')
                ->setFrom(array('emily@twinkler.co' => 'Emily from Twinkler'))
                ->setTo($user->getEmail())
                ->setContentType('text/html')
                ->setBody($this->template->render(':emails:joinedApp.email.twig', array('user' => $user)))
        ;
        $this->mailer->send($message);
       
        $fbid = $user->getFacebookId();
        $members = $this->em->getRepository('TkUserBundle:Member')->findByFacebookId($fbid);

        foreach($members as $member){
            if(!$member->getUser()){
                $member->setUser($user);
                $member->setName('caca');
                $this->em->persist($member);
                $this->em->flush();
            }
        }

        $session = $event->getRequest()->getSession();
        $group_id = $session->get('created_group_id');
        if ($group_id) {
            $group = $this->em->getRepository('TkGroupBundle:TGroup')->find($group_id);

            $member = new Member();
            $member->setUser($user);
            $member->setName($user->getUsername());
            $member->setTGroup($group);
            $user->setCurrentMember($member);
            $this->em->persist($member);
            $this->em->persist($user);
            $this->em->flush();
        } 

        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
	}

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $session = $event->getRequest()->getSession();
        $group_id = $session->get('created_group_id');
        if ($group_id) {
            $route = 'tk_group_add_members';
            $session->remove('created_group_id');
        } else {
            $route = 'tk_user_homepage';
        }

        $response = new RedirectResponse($this->router->generate($route));
        $event->setResponse($response);
    }
}