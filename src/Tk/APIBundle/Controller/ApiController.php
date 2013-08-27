<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
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
                'id'           => $user->getId(),
                'name'         => $user->getUsername(),
                'friendNumber' => sizeof($user->getFriends()),
                'picturePath'  => $picture_path
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
                $groups[] = $this->returnGroupAction($group, $member);
            }
            return new JsonResponse($groups);
        }

        return new JsonResponse(array(
            'message' => 'User is not identified'
        ));
    }

    public function addGroupAction($group_name, $currency_id)
    {
        $user = $this->getUser();
        $currency = $this->getDoctrine()->getRepository('TkGroupBundle:Currency')->find($currency_id);

        if ($user and $currency and $group_name) {

            $group = new TGroup();
            $group->setDate(new \Datetime('now'));
            $group->setName($group_name);
            $group->setCurrency($currency);
            $group->setInvitationToken($group->generateInvitationToken());

            $member = new Member();
            $member->setUser($user);
            $member->setName($user->getUsername());
            $member->setTGroup($group);

            $user->setCurrentMember($member);

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

            return $group;
        } else {
            return null;
        }
    }

    public function postGroupAction(Request $request)
    {
        $data = $request->request->all();

        $group_id = $data['id'];
        $group_name = $data['name'];
        $currency_id = $data['currency'];
        $add_members = $data['addMembers'];

        if ($group_id == 0) {
            //new group
            $group = $this->addGroupAction($group_name, $currency_id);
        } else {
            //existing group
            $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);
        }

        if ($group) {

            $em = $this->getDoctrine()->getManager();

            foreach ($add_members as $name) {
                $member = new Member();
                $member->setName($name);
                $member->setTGroup($group);
                $member->setInvitationToken($member->generateInvitationToken());
                $em->persist($member);
            }
            $em->flush();
        }

        return new JsonResponse($this->returnGroupAction($group, $member)); 
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
        return array('id'           => $group->getId(), 
                     'name'         => $group->getName(), 
                     'currency'     => array('id'     => $group->getCurrency()->getId(),
                                             'name'   => $group->getCurrency()->getName(),
                                             'symbol' => $group->getCurrency()->getSymbol()),
                     'members'      => $group_members, 
                     'activeMember' => array('id'          => $member->getId(), 
                                             'name'        => $member->getName(), 
                                             'picturePath' => $member->getPicturePath())
                    );
    }

    public function getExpensesAction()
    {
        $data = $this->getRequest()->query->all();
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
        $group = $member->getTGroup();
        $expenses = $group->getExpenses();

        $response_array = array();

        foreach($expenses as $expense){

            $members = array();
            foreach($expense->getUsers() as $m){
                $members[] = array('id' => $m->getId(), 'name' => $m->getName(), 'picturePath' => $m->getPicturePath());
            }

            if ($expense->getOwner() == $member) {
                $name = 'You';
            } else {
                $name = $expense->getOwner()->getName();
            }

            $response_item = array(
                'name'      => $expense->getName(),
                'amount'    => $expense->getAmount(),
                'owner'     => array('id' => $expense->getOwner()->getId(), 'name' => $name, 'picturePath' => $expense->getOwner()->getPicturePath()),
                'date'      => $expense->getDate()->getTimestamp(),
                'members'   => $members,
                'active'    => $expense->getActive(),
                'author'    => $expense->getAuthor()->getName(),
                'addedDate' => $expense->getAddedDate()->getTimestamp(),
                'share'     => $this->container->get('tk_expense.expenses')->youGet($member, $expense),
                );
            $response_array[] = $response_item;
        }

        return new JsonResponse(array('balance' => $member->getBalance(), 'expenses' => $response_array));
    }

    public function getDashboardInfoAction()
    {
        $data = $this->getRequest()->query->all();
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
        $group = $member->getTGroup();

        $members = array();
        foreach($group->getMembers() as $m) {
            $members[] = array('id'          => $m->getId(),
                               'name'        => $m->getName(),
                               'picturePath' => $m->getPicturePath(),
                               'balance'     => $m->getBalance()
                                );
        }

        $dashboard = array('members' => $members,
            'currentMemberId' => $data['currentMemberId']);
        return new JsonResponse($dashboard);
    }
}