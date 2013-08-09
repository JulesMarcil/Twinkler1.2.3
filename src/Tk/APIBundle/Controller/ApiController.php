<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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
                $picture_path = $user->getPicture()->getWebPath();
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
                    $group_members[] = array('id' => $m->getId(), 'name' => $m->getName());
                }
                $groups[] = array('id' => $group->getId(), 'name' => $group->getName(), 'currency' => $group->getCurrency()->getSymbol(), 'members' => $group_members, 'activeMember' => array('id' => $member->getId(), 'name' => $member->getName()));
            }
            return new JsonResponse($groups);
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));
    }
}