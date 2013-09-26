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

        $group_id       = $data['id'];
        $group_name     = $data['name'];
        $currency_id    = $data['currency'];
        $add_members    = $data['addMembers'];
        $add_friends    = $data['addFriends'];
        $remove_members = $data['removeMembers'];

        if ($group_id == 0) {

            //new group
            $group = $this->addGroupAction($group_name, $currency_id);
            $em->persist($group);

            $member = new Member();
            $member->setUser($user);
            $member->setName($user->getUsername());
            $member->setTGroup($group);
            $group->addMember($member);
            $em->persist($member);

        } else {
            //existing group
            $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);
            $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['activeMember']);
        }

        $user->setCurrentMember($member);

        if ($group) {

            foreach ($add_members as $name) {
                if($name != '-1') {
                    $m = new Member();
                    $m->setName($name);
                    $m->setTGroup($group);
                    $m->setInvitationToken($member->generateInvitationToken());
                    $group->addMember($m);
                    $em->persist($m);
                }
            }

            $mailer = $this->get('mailer'); 

            foreach ($add_friends as $id) {
                if($id != '-1') {
                    $u = $this->getDoctrine()->getRepository('TkUserBundle:User')->find($id);

                    $m = new Member();
                    $m->setUser($u);
                    $m->setName($u->getUsername());
                    $m->setTGroup($group);
                    $group->addMember($m);
                    $em->persist($m);

                    $u->setCurrentMember($m);

                    $message = \Swift_Message::newInstance();
                    $message->setSubject($user.' added you to a group on Twinkler')
                            ->setFrom(array('no-reply@twinkler.co' => 'Twinkler'))
                            ->setTo($u->getEmail())
                            ->setContentType('text/html')
                            ->setBody($this->renderView(':emails:addedToGroup.email.twig', array('user'   => $user, 
                                                                                                 'dest'   => $u)))
                    ;   
                    $mailer->send($message);
                }
            }

            foreach ($remove_members as $id) {
                if($id != '-1') {
                    $em = $this->getDoctrine()->getManager();
                    $m = $em->getRepository('TkUserBundle:Member')->find($id);

                    $u = $m->getUser();

                    if($u and ($u->getCurrentMember() == $member)) {    
                        $u->setCurrentMember(null);
                    }

                    $m->setActive(false);

                    if ($u and ($u != $user)) {

                        $message = \Swift_Message::newInstance();
                        $message->setSubject($user.' removed you from a group')
                                ->setFrom(array('no-reply@twinkler.co' => 'Twinkler'))
                                ->setTo($u->getEmail())
                                ->setContentType('text/html')
                                ->setBody($this->renderView(':emails:removedFromGroup.email.twig', array('user'   => $user, 
                                                                                                         'member' => $m)))
                        ;
                        $mailer->send($message);
                    }
                }
            }

            $em->flush();

            $group2 = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group->getId());

            return new JsonResponse($this->returnGroupAction($group2, $member));
        } else {
            return new JsonResponse(array('message' => 'error in code, no group created or found'));
        }
    }

    private function addGroupAction($group_name, $currency_id)
    {
        $currency = $this->getDoctrine()->getRepository('TkGroupBundle:Currency')->find($currency_id);

        if ($currency) {

            $group = new TGroup();
            $group->setName($group_name);
            $group->setCurrency($currency);
            $group->setInvitationToken($group->generateInvitationToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            return $group;
        } else {
            return null;
        }
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
                                             'picturePath' => $member->getPicturePath()),
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
                $this->removeMemberAction($member, $em);
            }               
            return new JsonResponse(array('message' => 'group '.$group->getName().' closed successfully'));
        } else {
            return new JsonResponse(array('message' => 'group not found'));   
        }
    }

    private function removeMemberAction($member, $em)
    {

        $u = $member->getUser();
        $user = $this->getUser();

        if($u and ($u->getCurrentMember() == $member)) {    
            $u->setCurrentMember(null);
        }

        $member->setActive(false);
        $em->flush();

        if ($u and ($u != $user)) {

            $message = \Swift_Message::newInstance();
            $message->setSubject($user.' removed you from a group')
                    ->setFrom(array('no-reply@twinkler.co' => 'Twinkler'))
                    ->setTo($u->getEmail())
                    ->setContentType('text/html')
                    ->setBody($this->renderView(':emails:removedFromGroup.email.twig', array('user'   => $user, 
                                                                                             'member' => $member)))
            ;
            $mailer->send($message);
        }
    }

    public function getFriendsAction()
    {
        $user = $this->getUser();
        $friends = $user->getFriends();
        $friends_array = array();

        foreach($friends as $friend) {
            $friends_array[] = array('id' => $friend->getId(),
                                     'name' => $friend->getUsername(),
                                     'picturePath' => $friend->getMembers()->first()->getPicturePath()
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
            $expense->addUser($member);
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