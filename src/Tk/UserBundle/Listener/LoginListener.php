<?php
 
namespace Tk\UserBundle\Listener;
 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Routing\Router; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Tk\UserBundle\Entity\Member;
 
/**
 * Custom login listener.
 */
class LoginListener
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;

	/** @var \Symfony\Component\Routing\Router */
	private $router;
	
	/** @var \Symfony\Component\EventDispatcher\EventDispatcher */
	private $dispatcher;

	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 * @param Router          $router
	 * @param Dispatcher      $dispatcher
	 */

	public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Router $router, EventDispatcher $dispatcher)
	{
		$this->securityContext = $securityContext;
		$this->em              = $doctrine->getEntityManager();
		$this->router 		   = $router;
		$this->dispatcher 	   = $dispatcher;
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
		if ($id) {
			$member = $this->em->getRepository('TkUserBundle:Member')->find($id);

			$add = true;
	        foreach($user->getMembers() as $user_member){
	            if ($user_member->getTGroup() == $member->getTGroup()){ 
	            	$add = false;
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

            $session->remove('created_group_id');

            $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
		}
	}

	public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = new RedirectResponse($this->router->generate('tk_group_add_members'));
        $event->setResponse($response);
    }
}