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

    public function getGroupsAction()
    {
        $user = $this->getDoctrine()->getRepository('TkUserBundle:User')->find(6);
        $members = $user->getMembers();
        $groups = array();

        foreach($members as $member){
            $group = $member->getTGroup();
            $group_members = array();
            foreach($group->getMembers() as $member){
                $group_members[] = array('id' => $member->getId(), 'name' => $member->getName());
            }
            $groups[] = array('id' => $group->getId(), 'name' => $group->getName(), 'currency' => $group->getCurrency()->getSymbol(), 'members' => $group_members);
        }
        return new JsonResponse($groups);
    }
}