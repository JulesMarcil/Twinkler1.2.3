<?php

namespace Tk\ExpenseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\ExpenseBundle\Entity\Expense;
use Tk\ExpenseBundle\Form\PaybackType;

class PaybackController extends Controller
{
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

                $em = $this->getDoctrine()->getManager();
        		$em->persist($expense);
        		$em->flush();

                foreach($expense->getUsers() as $member){
                    if ($member->getUser() == $this->getUser()){
                        $email = null;
                    } else if($member->getUser()){
                        $email = $member->getUser()->getEmail();
                    }else if($member->getEmail()){
                        $email = $member->getEmail();
                    }else{
                        $email = null;
                    }
                    if($email){
                        $message = \Swift_Message::newInstance()
                            ->setSubject($expense->getAuthor()->getName().' tagged you in an expense on Twinkler')
                            ->setFrom(array('noreply@twinkler.co' => 'Twinkler'))
                            ->setTo($email)
                            ->setContentType('text/html')
                            ->setBody($this->renderView(':emails:addExpense.email.twig', array('expense' => $expense, 'member' => $member, 'share' => $this->container->get('tk_expense.expenses')->youGet($member, $expense))));
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

    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $expense = $em->getRepository('TkExpenseBundle:Expense')->find($id);
        
        $em->remove($expense);
        $em->flush();

        return $this->redirect($this->generateUrl('tk_expense_homepage'));
    }
}