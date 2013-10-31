<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use Tk\UserBundle\Entity\User,
    Tk\UserBundle\Entity\Member,
    Tk\GroupBundle\Entity\TGroup;

class GroupController extends Controller
{
    public function getGroupsAction()
    {
        $user = $this->getUser();

        if ($user) {
            $members = $user->getMembers();
            $groups = array();

            foreach($members as $member){
                $group = $member->getTGroup();
                $groups[] = $this->returnGroupAction($group, $member);
            }
            return new JsonResponse($groups);
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));
    }

    public function postGroupAction(Request $request)
    {
        $data = $request->request->all();
        $em   = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $group_name     = $data['name'];
        $currency_id    = $data['currency'];

        $currency = $this->getDoctrine()->getRepository('TkGroupBundle:Currency')->find($currency_id);

        if ($currency) {

            $group = new TGroup();
            $group->setName($group_name);
            $group->setCurrency($currency);
            $group->setInvitationToken($group->generateInvitationToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

        }else{
            return new JsonResponse(array('error' => 'currency not found'));
        }

        $member = new Member();
        $member->setUser($user);
        $member->setName($user->getUsername());
        $member->setTGroup($group);
        $group->addMember($member);
        $em->persist($member);
        $user->setCurrentMember($member);
        $em->flush();

        $group2 = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group->getId());
        return new JsonResponse($this->returnGroupAction($group2, $member));
    }

    public function addFriendsAction(Request $request)
    {
        $data = $request->request->all();
        $em   = $this->getDoctrine()->getManager();
        $u = $this->getUser();

        $group_id = $data['group'];
        $friends  = $data['friends'];

        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);

        if($group){           

            $em = $this->getDoctrine()->getmanager();
            $repo = $this->getDoctrine()->getRepository('TkUserBundle:User');

            foreach($friends as $friend){

                $user = $repo->findOneByFacebookId($friend['id']);

                if ($user and $user->isInGroup($group)){
                    //don't do anything
                } else if($user){

                    $member = new Member();
                    $member->setUser($user);
                    $member->setName($user->getUsername());
                    $member->setTGroup($group);
                    
                    $user->setCurrentMember($member);

                    $em->persist($member);
                    $em->flush();
                } else {
                    $member = new Member();
                    $member->setName($friend['name']);
                    $member->setFacebookId($friend['id']);
                    $member->setTGroup($group);

                    $em->persist($member);
                    $em->flush();
                }
                $user = null;
            }

            $group2 = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);
            return new JsonResponse($this->returnGroupAction($group2, null));

        } else {
            return new JsonResponse(array('error' => 'group not found'));
        }
    }

    public function addManualAction(Request $request)
    {
        $data = $request->request->all();
        $em   = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $group_id = $data['group'];
        $friend   = $data['member'];

        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);

        if($group){           

            $em = $this->getDoctrine()->getmanager();
            $repo = $this->getDoctrine()->getRepository('TkUserBundle:User');

            $member = new Member();
            $member->setName($friend['name']);
            $member->setEmail($friend['email']);
            $member->setInvitationToken($member->generateInvitationToken());
            $member->setTGroup($group);
            $em->persist($member);
            $em->flush();

            $mailer = $this->get('mailer');

            $message = \Swift_Message::newInstance();
            $message->setSubject($user.' sent you an invitation on Twinkler')
                    ->setFrom(array('emily@twinkler.co' => 'Emily from Twinkler'))
                    ->setTo($member->getEmail())
                    ->setContentType('text/html')
                    ->setBody($this->renderView(':emails:invitationEmail.email.twig', array('member' => $member, 
                                                                                            'user'    => $user)))
            ;
            $mailer->send($message);

            $group2 = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);
            return new JsonResponse($this->returnGroupAction($group2, null));

        } else {
            return new JsonResponse(array('error' => 'group not found'));
        }
    }

    public function removeFriendsAction(Request $request)
    {
        $data = $request->request->all();
        $friends = $data['friends'];
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('TkUserBundle:Member');

        foreach ($friends as $id){

            $member = $repo->find($id);
            $user = $member->getUser();

            if($user and ($user->getCurrentMember() == $member)) {    
                $user->setCurrentMember(null);
            }
            $member->setActive(false);

            /*
            if ($user and ($user != $u)) {

                $message = \Swift_Message::newInstance();
                $message->setSubject($u.' removed you from a group')
                        ->setFrom(array('no-reply@twinkler.co' => 'Twinkler'))
                        ->setTo($user->getEmail())
                        ->setContentType('text/html')
                        ->setBody($this->renderView(':emails:removedFromGroup.email.twig', array('user'   => $u, 
                                                                                                 'member' => $member)))
                ;
                $mailer->send($message);
            }*/

            $em->flush();
        }

        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($data['group']);
        return new JsonResponse($this->returnGroupAction($group, null));
    }

    private function returnGroupAction($group, $member)
    {
        $group_members = array();

        foreach($group->getMembers() as $m){
            $group_members[] = array('id'          => $m->getId(), 
                                     'name'        => $m->getName(), 
                                     'picturePath' => $m->getPicturePath(), 
                                     'balance'     => $m->getBalance());
        }

        if ($member) {
            $activeMember = array('id'          => $member->getId(), 
                                  'name'        => $member->getName(), 
                                  'picturePath' => $member->getPicturePath());
        } else {
            $activeMember = null;
        }

        return array('id'           => $group->getId(), 
                     'name'         => $group->getName(), 
                     'currency'     => array('id'     => $group->getCurrency()->getId(),
                                             'name'   => $group->getCurrency()->getName(),
                                             'symbol' => $group->getCurrency()->getSymbol()),
                     'members'      => $group_members, 
                     'activeMember' => $activeMember,
                     'link'         => $group->getInvitationToken()
                    );
    }

    public function closeGroupAction()
    {
        $data = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();
        
        $member = $em->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
        $group = $member->getTGroup();

        if ($group) {
            foreach($group->getMembers() as $member){
                if ($user = $member->getUser()){
                    if ($user->getCurrentMember() == $member){
                        $user->setCurrentMember(null);
                        $em->persist($user);
                    }
                }
                $member->setActive(0);
                $em->persist($member);
            }  
            $em->flush();             
            return new JsonResponse(array('message' => 'group '.$group->getName().' closed successfully'));
        } else {
            return new JsonResponse(array('message' => 'group not found'));   
        }
    }
}