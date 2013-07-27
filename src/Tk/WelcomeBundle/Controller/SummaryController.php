<?php

namespace Tk\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SummaryController extends Controller
{
    public function summaryAction($id, $token)
    {
        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($id);

        if ($token != $group->getInvitationToken()){
            throw new AccessDeniedException('You try to access a wrong Url');
        }else{
            $expenses_service = $this->container->get('tk_expense.expenses');

            return $this->render('TkWelcomeBundle:Invitation:summary.html.twig', array(
                'group'               => $group,
                'all_expenses'        => $group->getExpenses(),
                'total_paid'          => $expenses_service->getTotalPaid($group),
                'debts'               => $expenses_service->getCurrentDebts($group),
                ));
        }
    }
}
