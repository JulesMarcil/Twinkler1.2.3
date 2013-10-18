<?php

namespace Tk\GroupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

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
}




        