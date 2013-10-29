<?php

namespace Tk\GroupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tk\GroupBundle\Entity\TGroup;
use Tk\GroupBundle\Form\TGroupType;
use Tk\UserBundle\Entity\Member;
use Tk\UserBundle\Entity\User;

class AddMembersController extends Controller
{
    public function addMembersAction()
    {   
        $new_ids = array();
        $this->get('session')->set('new_ids', $new_ids);

        return $this->render('TkGroupBundle:Creation:addMembers.html.twig');      
    }

    public function addFriendAction(Request $request)
    {
        $data = $request->query->all();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('TkUserBundle:User')->findOneByFacebookId($data['id']);
        $group = $this->getUser()->getCurrentMember()->getTGroup();

        if ($user) {    
            if($user->isInGroup($group)){
                return new JsonResponse(array('error' => $data['name'].' is already in the group'));
            }
        } else {
            
            $user = new User();
            $user->setUsername($data['name']);
            $user->setEmail($data['username'].'@facebook.com');
            $user->setFacebookId($data['id']);
            $user->setEnabled(true);
            $user->setPassword('');
        }

        $member = new Member();
        $member->setUser($user);
        $member->setName($user->getUsername());
        $member->setTGroup($group);
        $user->setCurrentMember($member);

        $em->persist($user);
        $em->persist($member);
        $em->flush();

        $session = $this->get('session');
        $new_ids = $session->get('new_ids');           
        $new_ids[] = $data['id'];
        $session->set('new_ids', $new_ids);

        return new JsonResponse(array('id' => $data['id'], 'name' => $data['name']));
    }

    public function removeAddedMemberAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('TkUserBundle:Member')->find($id);
        
        $member->setActive(false);
        $em->flush();
        
        $session = $this->get('session');
        $new_ids = $session->get('new_ids');
        $pos = array_search($member->getUser()->getId(), $new_ids);
        unset($new_ids[$pos]);
        $session->set('new_ids', $new_ids);

        return $this->redirect($this->generateUrl('tk_group_add_members')); 
    }

    public function validateMembersAction()
    {
        $user = $this->getUser();
        $group = $user->getCurrentMember()->getTGroup();

        $mailer = $this->get('mailer');
        $repo = $this->getDoctrine()->getManager()->getRepository('TkUserBundle:User');

        foreach($this->get('session')->get('new_ids') as $id){

            $u = $repo->findOneByFacebookId($id);

            if($u->getEmail()){
                $message = \Swift_Message::newInstance();
                $message->setSubject($user.' added you to a group on Twinkler')
                        ->setFrom(array('no-reply@twinkler.co' => 'Twinkler'))
                        ->setTo($u->getEmail())
                        ->setContentType('text/html')
                        ->setBody($this->renderView(':emails:addedToGroup.email.twig', array('user' => $user, 
                                                                                             'dest' => $u)))
                ;
                $mailer->send($message);
            }
        }
        return $this->redirect($this->generateUrl('tk_group_homepage'));
    }
}
