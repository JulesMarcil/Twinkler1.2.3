<?php

namespace Tk\GroupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Tk\GroupBundle\Entity\TGroup,
    Tk\GroupBundle\Form\TGroupType;

class DashboardController extends Controller
{
    public function summaryModalAction()
    {
        return $this->render('TkGroupBundle:Dashboard:sendSummaryModal.html.twig');
    }

    public function summarySendAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $mailer = $this->get('mailer');

        $user = $this->getUser();
        $group = $user->getCurrentMember()->getTGroup();

        $expenses_service = $this->container->get('tk_expense.expenses');
        $debts = $expenses_service->getCurrentDebts($group);

        $m = $data['_message'];

        foreach($group->getMembers() as $member){
            
            if (isset($data['_send_email_'.$member->getId()])) {

                $u = $member->getUser();
                if($u){
                    $email = $u->getEmail();
                } else {
                    $email = $data['_email_'.$member->getId()];
                    $member->setEmail($email); 
                    $em->persist($member);
                }

                if($email) {

                    $message = \Swift_Message::newInstance();
                    $message->setSubject($user.' sent you a summary on Twinkler')
                            ->setFrom(array('no-reply@twinkler.co' => $user.' from Twinkler'))
                            ->setTo($email)
                            ->setContentType('text/html')
                            ->setBody($this->renderView(':emails:summary.email.twig', array(
                                'user'    => $user,
                                'group'   => $group,
                                'member'  => $member,
                                'debts'   => $debts,
                                'message' => $m
                                )));
                    $mailer->send($message);
                }

            }
        }
        $em->flush();
        return $this->redirect($this->generateUrl('tk_group_dashboard'));
    }

    private function removeMemberAction($member, $em)
    {
        $u = $member->getUser();
        $group = $member->getTGroup();
        $user = $this->getUser();

        $member->setActive(false);
        $em->flush();

        if($u){
            if ($u->getCurrentMember() == $member){
                $u->setCurrentMember(null);
            }
            if ($u != $user){

                $mailer = $this->get('mailer');

                $message = \Swift_Message::newInstance();
                $message->setSubject($user.' removed you from a group')
                        ->setFrom(array('no-reply@twinkler.co' => 'Twinkler'))
                        ->setTo($u->getEmail())
                        ->setContentType('text/html')
                        ->setBody($this->renderView(':emails:removedFromGroup.email.twig', array('user'   => $user, 
                                                                                                 'member' => $member,
                                                                                                 'group'  => $group)))
                ;
                $mailer->send($message);
            }
        }
    }


    public function editAction()
    {
        $group = $this->getUser()->getCurrentMember()->getTGroup();

        $form = $this->createForm(new TGroupType(), $group);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($group);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_chat_homepage'));
            }
        }

        return $this->render('TkGroupBundle:GroupActions:edit.html.twig', array(
            'form' => $form->createView(),
            ));        
    }

    public function closeGroupAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('TkGroupBundle:TGroup')->find($id);
        $user = $this->getUser();

        if ($this->getUser()->getCurrentMember()->getTGroup() != $group){
            throw new AccessDeniedException('You are not allowed to do this');
        }
        else{   
            $user->setCurrentMember(null);
            $group->setActive(0);
            $em->flush();               
        }
        return $this->redirect($this->generateUrl('tk_user_homepage')); 
    }
}




        