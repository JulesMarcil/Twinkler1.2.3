<?php

namespace Tk\GroupBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\GroupBundle\Entity\TGroup;
use Tk\GroupBundle\Form\TGroupType;
use Tk\UserBundle\Entity\Member;


class DefaultController extends Controller
{
    public function indexAction()
    {
        if(!$this->getUser()->getCurrentMember()){
            return $this->redirect($this->generateUrl('tk_user_homepage'));
        }else{
            return $this->render('TkGroupBundle:Default:settings.html.twig');
        }
    }

    public function ajaxContentAction()
    {
      return $this->render('TkGroupBundle:Default:content.html.twig');
    }

    public function switchAction($id)
    {
    	$this->changeCurrentMemberAction($id);

        $route = $this->get('request')->get('route');
        return $this->redirect($this->generateUrl($route));
    }

    public function goToAction($id)
    {
        $this->changeCurrentMemberAction($id);

        return $this->redirect($this->generateUrl('tk_group_homepage'));
    }

    public function goToExpensesAction($id)
    {
        $this->changeCurrentMemberAction($id);

        return $this->redirect($this->generateUrl('tk_expense_homepage'));
    }

    public function goToListsAction($id)
    {
        $this->changeCurrentMemberAction($id);

        return $this->redirect($this->generateUrl('tk_list_homepage'));
    }

    private function changeCurrentMemberAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $member = $em->getRepository('TkUserBundle:Member')->find($id);
        $user = $this->getUser();
        $user->setCurrentMember($member);
        $em->flush();
    }

    public function newAction()
    {
        $group = new TGroup();
        $group->setDate(new \Datetime('now'));

        $form = $this->createForm(new TGroupType(), $group);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();

                $user = $this->getUser();
                $member = new Member();
                $member->setUser($user);
                $member->setName($user->getUsername());
                $member->setTGroup($group);
                $user->setCurrentMember($member);
                $group->setInvitationToken($group->generateInvitationToken());
                $em->persist($group);
                $em->persist($member);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_group_add_members'));
            }
        }

        return $this->render('TkGroupBundle:Creation:new.html.twig', array(
            'form' => $form->createView(),
            ));        
    }

    public function editAction()
    {
        $group = $this->getUser()->getCurrentMember()->getTGroup();

        $form = $this->createForm(new TGroupType(), $group);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($group);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_group_homepage'));
            }
        }

        return $this->render('TkGroupBundle:GroupActions:edit.html.twig', array(
            'form' => $form->createView(),
            ));        
    }

    public function addMemberAction()
    {   
        $defaultData = array('name' => '', 'email' => '');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', 'text')
            ->add('email', 'email', array(
                'required' => false
                ))
            ->getForm();

        $request = $this->get('request');

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

            $data = $form->getData();
            $member = new Member();
            $member->setName($data['name']);
            $member->setEmail($data['email']);
            $member->setInvitationToken($member->generateInvitationToken());
            $member->setTGroup($this->getUser()->getCurrentMember()->getTGroup());

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($member);
            $em->flush();

            return $this->render("TkGroupBundle:Creation:showAddedMember.html.twig", array(
                'member' => $member,
            ));
        }}

        return $this->render('TkGroupBundle:GroupActions:addMember.html.twig', array(
            'form' => $form->createView(),
            ));        
    }

    public function addMembersAction()
    {   
        $new_ids = array();
        $this->get('session')->set('new_ids', $new_ids);

        return $this->render('TkGroupBundle:Creation:addMembers.html.twig');      
    }

    public function removeMemberRequestAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $member = $em->getRepository('TkUserBundle:Member')->find($id);

        if ($this->getUser()->getCurrentMember()->getTGroup() != $member->getTGroup()){
            throw new AccessDeniedException('You are not allowed to do this');
        }else{
            $this->removeMemberAction($member, $em);
            return $this->redirect($this->generateUrl('tk_group_homepage'));
        }
    }

    private function removeMemberAction($member, $em)
    {
        if($member->getUser()){
            $member->getUser()->setCurrentMember(null);
        }
        $n = sizeof($member->getMyExpenses()) + sizeof($member->getForMeExpenses());
        if ($n == 0){
            $em->remove($member);
        }else{
            $member->setActive(false);
        }
        $em->flush();
    }

    public function closeGroupAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $group = $em->getRepository('TkGroupBundle:TGroup')->find($id);

        if ($this->getUser()->getCurrentMember()->getTGroup() != $group){
            throw new AccessDeniedException('You are not allowed to do this');
        }
        else{
            foreach($group->getMembers() as $member){
                $this->removeMemberAction($member, $em);
            }               
        }
        return $this->redirect($this->generateUrl('tk_user_homepage')); 
    }

    public function addFriendAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $friend = $em->getRepository('TkUserBundle:User')->find($id);

        $member = new Member();
        $member->setUser($friend);
        $member->setName($friend->getUsername());
        $member->setTGroup($this->getUser()->getCurrentMember()->getTGroup());
        $friend->setCurrentMember($member);

        $em->persist($member);
        $em->flush();

        $session = $this->get('session');
        $new_ids = $session->get('new_ids');
        $new_ids[] = $id;
        $session->set('new_ids', $new_ids);

        return $this->render("TkGroupBundle:Creation:showAddedMember.html.twig", array(
            'member' => $member,
        ));
    }

    public function removeAddedMemberAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $member = $em->getRepository('TkUserBundle:Member')->find($id);
        $this->removeMemberAction($member, $em);
        
        if($member->getUser()){
            $session = $this->get('session');
            $new_ids = $session->get('new_ids');
            $pos = array_search($member->getUser()->getId(), $new_ids);
            print($pos);
            unset($new_ids[$pos]);
            $session->set('new_ids', $new_ids);
        }

        return $this->redirect($this->generateUrl('tk_group_add_members')); 
    }

    public function validateMembersAction()
    {
        $user = $this->getUser();
        $group = $user->getCurrentMember()->getTGroup();
        $mailer = $this->get('mailer');

        foreach($group->getMembers() as $member){
            if(!$member->getUser() and $member->getEmail()){
                $message = \Swift_Message::newInstance();
                $message->setSubject($user.' sent you an invitation on Twinkler')
                        ->setFrom(array('jules@twinkler.co' => 'Jules from Twinkler'))
                        ->setTo($member->getEmail())
                        ->setContentType('text/html')
                        ->setBody($this->renderView(':emails:invitationEmail.email.twig', array('invited' => $member, 'member' => $user->getCurrentMember())))
                ;
                $mailer->send($message);
            }else{
            }
        }

        $repo = $this->getDoctrine()->getEntityManager()->getRepository('TkUserBundle:User');

        foreach($this->get('session')->get('new_ids') as $id){

            $u = $repo->find($id);

            $message = \Swift_Message::newInstance();
            $message->setSubject($user.' added you to a group on Twinkler')
                    ->setFrom(array('jules@twinkler.co' => 'Jules from Twinkler'))
                    ->setTo($u->getEmail())
                    ->setContentType('text/html')
                    ->setBody($this->renderView(':emails:addedToGroup.email.twig', array('user' => $u, 'member' => $user->getCurrentMember())))
            ;
            $mailer->send($message);
        }

        return $this->redirect($this->generateUrl('tk_group_homepage'));
    }

    public function sendReminderEmailAction()
    {

    }
}
