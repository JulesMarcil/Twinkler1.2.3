<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\JsonResponse;

use Tk\GroupBundle\Entity\TGroup,
    Tk\UserBundle\Entity\Member,
    Tk\ListBundle\Entity\Lists;

class ApiController extends Controller
{
    public function articlesAction()
    {
        $articles = array('article1', 'article2', 'article3');
        return new JsonResponse($articles);
    }

    public function userAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user) {
            return new JsonResponse(array(
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ));
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));

    }

    public function getProfileAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user) {

            $facebook_id = $user->getFacebookId();
            if($facebook_id) {
                $picture_path = 'facebook';
            }else{
                $picture_path = $user->getPicture()->getPath();
            }

            return new JsonResponse(array(
                'id' => $user->getId(),
                'name' => $user->getUsername(),
                'friendNumber' => sizeof($user->getFriends()),
                'picturePath' => $picture_path
            ));
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));

    }

    public function getGroupsAction()
    {
        $user = $this->getUser();

        if ($user) {
            $members = $user->getMembers();
            $groups = array();

            foreach($members as $member){
                $group = $member->getTGroup();
                $group_members = array();
                foreach($group->getMembers() as $m){
                    $group_members[] = array('id' => $m->getId(), 'name' => $m->getName(), 'picturePath' => $m->getPicturePath());
                }
                $groups[] = array('id' => $group->getId(), 'name' => $group->getName(), 'currency' => $group->getCurrency()->getSymbol(), 'members' => $group_members, 'activeMember' => array('id' => $member->getId(), 'name' => $member->getName(), 'picturePath' => $member->getPicturePath()));
            }
            return new JsonResponse($groups);
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));
    }

    public function addGroupAction()
    {
        $user = $this->getUser();

        
        $data = $this->getRequest()->query->all();

        
        $currency = $this->getDoctrine()->getRepository('TkGroupBundle:Currency')->find($data['currency_id']);
        $group_name = $data['group_name'];

        if ($user and $currency and $group_name) {

            $group = new TGroup();
            $group->setDate(new \Datetime('now'));
            $group->setName($group_name);
            $group->setCurrency($currency);

            $member = new Member();
            $member->setUser($user);
            $member->setName($user->getUsername());
            $member->setTGroup($group);

            $user->setCurrentMember($member);
            $group->setInvitationToken($group->generateInvitationToken());

            $todolist = new Lists();
            $todolist->setName('Todo List');
            $todolist->setGroup($group);

            $shoppinglist = new Lists();
            $shoppinglist->setName('Shopping List');
            $shoppinglist->setGroup($group);

            $em = $this->getDoctrine()->getManager();
            $em->persist($todolist);
            $em->persist($shoppinglist);
            $em->persist($group);
            $em->persist($member);
            $em->flush();

            return new JsonResponse(array('message' => 'Group added successfully'));
        } else if (!$user) {
            return new JsonResponse(array('message' => 'User not found'));
        } else if (!$currency) {
            return new JsonResponse(array('message' => 'Currency not found'));
        } else if (!$group_name) {
            return new JsonResponse(array('message' => 'Group name not specified'));
        } else {
            return new JsonResponse(array('message' => 'Unidentified error'));
        }
    }
}