<?php

namespace Tk\ExpenseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\ExpenseBundle\Entity\Expense;
use Tk\ExpenseBundle\Form\ExpenseType;

class ExpenseController extends Controller
{
    public function indexAction()
    {
        if(!$this->getUser()->getCurrentMember()){
            return $this->redirect($this->generateUrl('tk_user_homepage'));
        }else{

            $member = $this->getUser()->getCurrentMember();
            $expenses_service = $this->container->get('tk_expense.expenses');

            return $this->render('TkExpenseBundle::index.html.twig', array(
                'all_expenses'        => $expenses_service->getAllExpenses($member),
                'my_expenses'         => $expenses_service->getMyExpenses($member),
                'other_expenses'      => $expenses_service->getOtherExpenses($member),
                'total_paid'          => $expenses_service->getTotalPaid($member->getTGroup()),
                'total_paid_by_me'    => $expenses_service->getTotalPaidByMe($member),
                'total_paid_supposed' => $expenses_service->getTotalSupposedPaid($member),
                'total_paid_for_me'   => $expenses_service->getTotalPaidForMe($member),
                'debts'               => $expenses_service->getCurrentDebts($member->getTGroup()),
                ));
        }
    }

    public function ajaxContentAction()
    {
        $member = $this->getUser()->getCurrentMember();
        $expenses_service = $this->container->get('tk_expense.expenses');
        
        return $this->render('TkExpenseBundle::content.html.twig', array(
                'all_expenses'        => $expenses_service->getAllExpenses($member),
                'my_expenses'         => $expenses_service->getMyExpenses($member),
                'other_expenses'      => $expenses_service->getOtherExpenses($member),
                'total_paid'          => $expenses_service->getTotalPaid($member->getTGroup()),
                'total_paid_by_me'    => $expenses_service->getTotalPaidByMe($member),
                'total_paid_supposed' => $expenses_service->getTotalSupposedPaid($member),
                'total_paid_for_me'   => $expenses_service->getTotalPaidForMe($member),
                'debts'               => $expenses_service->getCurrentDebts($member->getTGroup()),
                ));
    }

    public function newAction()
    {
    	$member = $this->getUser()->getCurrentMember();
        $group = $member->getTGroup();

        $expense = new Expense();
        $expense->setAuthor($member);
        $expense->setOwner($member);
        $expense->setGroup($group);
        $expense->setAddedDate(new \DateTime('now'));
        $expense->setDate(new \Datetime('today'));
        $expense->setActive(true);

        $form = $this->createForm(new ExpenseType($group), $expense);
                     

        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) {          
        
            if (count($form->get('users')->getData()) > 0){

                $em = $this->getDoctrine()->getEntityManager();
        		$em->persist($expense);
        		$em->flush();

                foreach($expense->getUsers() as $member){
                    if($member->getUser()){
                        $email = $member->getUser()->getEmail();
                    }else if($member->getEmail()){
                        $email = $member->getEmail();
                    }else{
                        $email = null;
                    }
                    if($email){
                        $message = \Swift_Message::newInstance()
                            ->setSubject($expense->getAuthor()->getName().' tagged you in an expense on Twinkler')
                            ->setFrom(array('jules@twinkler.co' => 'Jules from Twinkler'))
                            ->setTo($email)
                            ->setContentType('text/html')
                            ->setBody($this->renderView(':emails:addExpense.email.twig', array('expense' => $expense, 'member' => $member)));
                        $this->get('mailer')->send($message);
                    }
                }
            }

        	return $this->redirect($this->generateUrl('tk_expense_homepage'));
    	}}

    	return $this->render('TkExpenseBundle::new.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $expense = $em->getRepository('TkExpenseBundle:Expense')->find($id);
        $group = $expense->getGroup();
        
        $form = $this->createForm(new ExpenseType($group), $expense);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) { 

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($expense);
            $em->flush();

            return $this->redirect($this->generateUrl('tk_expense_homepage'));
        }}

        return $this->render('TkExpenseBundle::edit.html.twig', array(
            'expense'             => $expense,
            'form'                => $form->createView(),
        ));
    }

    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $expense = $em->getRepository('TkExpenseBundle:Expense')->find($id);
        
        $em->remove($expense);
        $em->flush();

        return $this->redirect($this->generateUrl('tk_expense_homepage'));
    }
}