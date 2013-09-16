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

        $session = $event->getRequest()->getSession();

        $id = $session->get('invitation_id');
        if ($id) {
            $member = $this->em->getRepository('TkUserBundle:Member')->find($id);

            $add = true;
            $user_members = $user->getMembers();
            if ($user_members) {
                foreach($user->getMembers() as $user_member){
                    if ($user_member->getTGroup() == $member->getTGroup()){ 
                        $add = false;
                    }
                }
            }

            if ($add){
                $member->setUser($user);
                $member->setName($user->getUsername());
                $member->setInvitationToken(null);
                $member->setActive(1);
                $user->setCurrentMember($member);
                $this->em->persist($user);
                $this->em->flush();
            }

            $session->remove('invitation_id');
            $session->remove('invitation_member');
        }

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

        /*
	    
	    $currency = $this->em->getRepository('TkGroupBundle:Currency')->find(1);	

        $group = new TGroup();
        $group->setName('Twinkler team (example)');
        $group->setCurrency($currency);
        $group->setInvitationToken($group->generateInvitationToken());

        $jules  = $this->em->getRepository('TkUserBundle:User')->find(1);
        $arnaud = $this->em->getRepository('TkUserBundle:User')->find(2);

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
		$message1->setBody('messageBody1');
		$message1->setAuthor($jules_member);
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
		$message2->setBody('messageBody2');
		$message2->setAuthor($arnaud_member);
		$message2->setGroup($group);

        $this->em->persist($user);
        $this->em->persist($group);
        $this->em->persist($member);
        $this->em->persist($jules_member);
        $this->em->persist($arnaud_member);
        $this->em->persist($message1);
        $this->em->persist($expense);
        $this->em->persist($message2);
        $this->em->flush();
        */
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