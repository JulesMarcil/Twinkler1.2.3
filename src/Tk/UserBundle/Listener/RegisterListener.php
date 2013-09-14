<?php
 
namespace Tk\UserBundle\Listener;
 
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

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
	
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(Doctrine $doctrine)
	{
		$this->em = $doctrine->getEntityManager();
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
        /*
        $user = $event->getForm()->getData();
	    
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

        $picture = $this->em ->getRepository('TkUserBundle:ProfilePicture')->find(1);	        
        $user->setPicture($picture);

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
}