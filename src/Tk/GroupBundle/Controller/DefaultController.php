<?php

namespace Tk\GroupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Tk\GroupBundle\Entity\TGroup,
    Tk\GroupBundle\Form\TGroupType,
    Tk\UserBundle\Entity\Member;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if(!$this->getUser()->getCurrentMember()){
            return $this->redirect($this->generateUrl('tk_user_homepage'));
        }else{
            return $this->redirect($this->generateUrl('tk_expense_homepage'));
        }
    }

    public function dashboardAction()
    {
        if(!$this->getUser()->getCurrentMember()){
            return $this->redirect($this->generateUrl('tk_user_homepage'));
        }else{
            $member = $this->getUser()->getCurrentMember();
            $expenses_service = $this->container->get('tk_expense.expenses');
            return $this->render('TkGroupBundle:Dashboard:dashboard.html.twig', array(
                    'debts'         => $expenses_service->getCurrentDebts($member->getTGroup())
                ));
        }
    }

    public function ajaxContentAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        return $this->render('TkGroupBundle:Dashboard:content.html.twig', array(
                'debts'         => $expenses_service->getCurrentDebts($member->getTGroup())
            ));
    }

    public function switchAction($id)
    {
    	$this->changeCurrentMemberAction($id);

        $route = $this->get('request')->get('route');
        if ($route == 'tk_chat_homepage' or $route == 'tk_group_dashboard'){
            return $this->redirect($this->generateUrl($route));
        } else if ($route == 'tk_group_add'){
            return $this->redirect($this->generateUrl('tk_group_dashboard'));
        }else{
            return $this->redirect($this->generateUrl('tk_expense_homepage'));
        }
    }

    public function goToAction($id)
    {
        $this->changeCurrentMemberAction($id);

        return $this->redirect($this->generateUrl('tk_group_homepage'));
    }

    private function changeCurrentMemberAction($id)
    { 
        $user   = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $member = $em->getRepository('TkUserBundle:Member')->find($id);

        if ($member->getUser() == $user) {

            $user->setCurrentMember($member);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($member);
            $em->flush();    
        }
    }

    public function newAction()
    {
        $group = new TGroup();

        $form = $this->createForm(new TGroupType(), $group);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

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

    public function emailFormAction($id, $email)
    {   
        $user= $this->getUser();
        $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($id);
        $member->setEmail($email);

        $em = $this->getDoctrine()->getManager();
        $em->persist($member);
        $em->flush();

        $mailer = $this->get('mailer');
        $message = \Swift_Message::newInstance();
                $message->setSubject($user.' sent you an invitation on Twinkler')
                        ->setFrom(array('emily@twinkler.co' => 'Emily from Twinkler'))
                        ->setTo($member->getEmail())
                        ->setContentType('text/html')
                        ->setBody($this->renderView(':emails:invitationEmail.email.twig', array('member' => $member, 'member' => $user->getCurrentMember())))
                ;
                $mailer->send($message);

        return new jsonResponse(array('email' => $email));
             
    }
}
