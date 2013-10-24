<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use Tk\GroupBundle\Entity\TGroup,
    Tk\UserBundle\Entity\Member,
    Tk\ExpenseBundle\Entity\Expense,
    Tk\UserBundle\Entity\Feedback;

class ApiController extends Controller
{
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
        $user = $this->getUser();
        if($user) {

            $user->setLastAppLogin(new \Datetime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            if ( $user->getPicture() == null ) {
                $em = $this->getDoctrine()->getManager();
                $picture = $em->getRepository('TkUserBundle:ProfilePicture')->find(1);
                $user->setPicture($picture);
                $em->persist($user);
                $em->flush(); 
            }

            $facebook_id = $user->getFacebookId();
            if($facebook_id) {
                $picture_path = $facebook_id;
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

    public function getFriendsAction()
    {
        $user = $this->getUser();
        $friends = $user->getFriends();
        $friends_array = array();

        foreach($friends as $friend) {
            $friends_array[] = array('id' => $friend->getId(),
                                     'name' => $friend->getUsername(),
                                     'picturePath' => $friend->getPicturePath()
                                     );
        }

        return new JsonResponse($friends_array);
    }

    public function getExpensesAction()
    {
        $data = $this->getRequest()->query->all();
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
        $group = $member->getTGroup();
        $expenses = $group->getExpenses();

        $response_array = array();

        foreach($expenses as $expense){
            $response_array[] = $this->returnExpenseArray($expense, $member);
        }

        return new JsonResponse(array('balance' => $member->getBalance(), 'expenses' => $response_array));
    }

    public function postExpenseAction()
    {
        $data = $this->getRequest()->request->all();
        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($data['currentGroupId']);
        $member_repo = $this->getDoctrine()->getRepository('TkUserBundle:Member');
        $owner = $member_repo->find($data['owner_id']);
        $author = $member_repo->find($data['author_id']);

        $timestamp = $data['date'];
        $date = new \DateTime('today');
        $date->setTimestamp($timestamp);

        $expense = new Expense();
        $expense->setType('expense');
        $expense->setAmount($data['amount']);
        $expense->setName($data['name']);
        $expense->setAddedDate(new \DateTime('now'));
        $expense->setDate($date);
        $expense->setActive(true);
        $expense->setAuthor($author);
        $expense->setOwner($owner);
        $expense->setGroup($group);

        foreach($data['member_ids'] as $id) {
            $member = $member_repo->find($id);
            $expense->addUsers($member);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($expense);
        $em->flush();

        return new JsonResponse(array('balance' => $author->getBalance(),
                                      'expense' => $this->returnExpenseArray($expense, $author)));
    }

    public function deleteExpenseAction()
    {
        $data = $this->getRequest()->request->all();
        $expense = $this->getDoctrine()->getRepository('TkExpenseBundle:Expense')->find($data['id']);
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);

        if ($expense) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($expense);
            $em->flush();

            return new JsonResponse(array('balance' => $member->getBalance()));
        }
        return new JsonResponse(array('message' => 'Expense not found, maybe it has already been removed'));
    }

    private function returnExpenseArray($expense, $member)
    {
        $members = array();
        foreach($expense->getUsers() as $m){
            $members[] = array('id'          => $m->getId(), 
                               'name'        => $m->getName(), 
                               'picturePath' => $m->getPicturePath());
        }

        if ($expense->getOwner() == $member) {
            $name = 'You';
        } else {
            $name = $expense->getOwner()->getName();
        }

        return array('id'        => $expense->getId(),
                     'name'      => $expense->getName(),
                     'amount'    => $expense->getAmount(),
                     'owner'     => array('id'          => $expense->getOwner()->getId(), 
                                          'name'        => $name, 
                                          'picturePath' => $expense->getOwner()->getPicturePath()),
                     'date'      => $expense->getDate()->getTimestamp(),
                     'members'   => $members,
                     'active'    => $expense->getActive(),
                     'author'    => $expense->getAuthor()->getName(),
                     'addedDate' => $expense->getAddedDate()->getTimestamp(),
                     'share'     => $this->container->get('tk_expense.expenses')->youGet($member, $expense),
                     );
    }

    public function getDashboardInfoAction()
    {
        $data = $this->getRequest()->query->all();
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
        $group = $member->getTGroup();
        $service = $this->container->get('tk_expense.expenses');

        $members = array();
        foreach($group->getMembers() as $m) {
            $members[] = array('id'          => $m->getId(),
                               'name'        => $m->getName(),
                               'picturePath' => $m->getPicturePath(),
                               'balance'     => $m->getBalance()
                                );
        }

        $dashboard = array('total_paid'      => $service->getTotalPaid($group),
                           'member_paid'     => $service->getTotalPaidByMe($member),
                           'members'         => $members,
                           'currentMemberId' => $data['currentMemberId']);

        return new JsonResponse($dashboard);
    }

    public function postFeedbackAction()
    {
        $data = $this->getRequest()->request->all();

        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($data['group_id']);

        if ($group) {
            $feedback = new Feedback();
            $feedback->setAuthor($this->getUser());
            $feedback->setGroup($group);
            $feedback->setType($data['type']);
            $feedback->setText($data['text']);
            $feedback->setDate(new \Datetime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            return new JsonResponse(array('message' => 'feedback added succesfully'));
        } else {
            return new JsonResponse(array('message' => 'group not found'));
        }
    }
}