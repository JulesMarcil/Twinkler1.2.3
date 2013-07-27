<?php
 
namespace Tk\UserBundle\Listener;
 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
 
/**
 * Custom login listener.
 */
class LoginListener
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(SecurityContext $securityContext, Doctrine $doctrine)
	{
		$this->securityContext = $securityContext;
		$this->em              = $doctrine->getEntityManager();
	}
	
	/**
	 * Do the magic.
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
			// user has just logged in
		}
		
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			// user has logged in using remember_me cookie
		}
		
		// do some other magic here
		$user = $event->getAuthenticationToken()->getUser();
		
		// ...
		$session = $event->getRequest()->getSession();
		$id = $session->get('invitation_id');
		if ($id != null) {
			$member = $this->em->getRepository('TkUserBundle:Member')->find($id);

			$add = true;
	        foreach($user->getMembers() as $user_member){
	            if ($user_member->getTGroup() == $member->getTGroup()){ 
	            	$add = false;
	            }else{}
	        }

	        if ($add){
		        $member->setUser($user);
		        $member->setName($user->getUsername());
				$member->setInvitationToken(null);
		        $user->setCurrentMember($member);
				$this->em->flush();
			}
		}

		if ( $user->getPicture() == null ) {
	        $picture = $this->em ->getRepository('TkUserBundle:ProfilePicture')->find(1);
	        
	        $user->setPicture($picture);
	        $this->em->persist($user);
	        $this->em->flush();	
		}
	}
}