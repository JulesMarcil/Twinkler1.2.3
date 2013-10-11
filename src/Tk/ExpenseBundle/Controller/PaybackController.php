<?php

namespace Tk\ExpenseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\ExpenseBundle\Entity\Expense;
use Tk\ExpenseBundle\Form\PaybackType;

class PaybackController extends Controller
{
    public function newAction($id1, $amount, $id2)
    {
        $member = $this->getUser()->getCurrentMember();
        $group = $member->getTGroup();

        $repo = $this->getDoctrine()->getRepository('TkUserBundle:Member');
        if($id1 == 0){
            $member1 = $member;
        } else {
            $member1 = $repo->find($id1);    
        }
        if($id2 == 0){
            $member2 = $member;
        } else {
            $member2 = $repo->find($id2);    
        }

        $expense = new Expense();
        $expense->setType('payback');
        $expense->setName('payback');
        $expense->setAuthor($member);
        $expense->setOwner($member1);
        $expense->setAmount($amount);
        $expense->setUsers($member2);
        $expense->setGroup($group);
        $expense->setAddedDate(new \DateTime('now'));
        $expense->setDate(new \Datetime('today'));
        $expense->setActive(true);

        $form = $this->createForm(new PaybackType($group), $expense);
                     
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

            return $this->redirect($this->generateUrl('tk_group_dashboard'));
        }}

        return $this->render('TkExpenseBundle:Payback:new.html.twig', array(
            'form'   => $form->createView(),
            'id1'    => $id1,
            'amount' => $amount,
            'id2'    => $id2
        ));
    }
}