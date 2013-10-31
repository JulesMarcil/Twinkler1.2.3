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

        if ($user and $user->isInGroup($group)){
            return new JsonResponse(array('error' => $data['name'].' is already in the group'));
        } else if($this->isInGroup($group, $data['id'])){
            return new JsonResponse(array('error' => $data['name'].' is already in the group'));
        } else if($user){
            $member = new Member();
            $member->setUser($user);
            $member->setName($user->getUsername());
            
            $user->setCurrentMember($member);
            $em->persist($user);
            
            $session = $this->get('session');
            $new_ids = $session->get('new_ids');           
            $new_ids[] = $data['id'];
            $session->set('new_ids', $new_ids);

        } else {
            $member = new Member();
            $member->setName($data['name']);

        }

        $member->setFacebookId($data['id']);
        $member->setTGroup($group);
        $em->persist($member);
        $em->flush();

        return new JsonResponse(array('id' => $data['id'], 'name' => $data['name']));
    }

    private function isINGroup($group, $id){
        foreach($group->getmembers() as $member){
            if ($member->getFacebookId() == $id){
                return true;
            }
        }
        return false;
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

    public function facebookAction(Request $request)
    {
        $fbID = $request->query->get('fbID');
        $em = $this->getDoctrine()->getManager();
        $fbuser = $em->getRepository('TkUserBundle:User')->findOneByFacebookId($fbID); 
        
        if(!$fbuser){
            $user = $this->getUser();
            $user->setFacebookId($fbID);

            $em->persist($user);
            $em->flush();

            return new JsonResponse(array('message' => 'User connected'));
        } else {
            return new JsonResponse(array('error' => 'This facebook user already have an account on Twinkler. Please logout and login with facebook to access it.'));
        }
    }
}
