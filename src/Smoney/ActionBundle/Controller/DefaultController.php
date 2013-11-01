<?php

namespace Smoney\ActionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use Smoney\ActionBundle\Entity\Payment;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SmoneyActionBundle:Default:index.html.twig', array('name' => $name));
    }

    public function selectAction()
    {
    	if(!$this->getUser()->getCurrentMember()){
            return $this->redirect($this->generateUrl('tk_user_homepage'));
        }else{
            $member = $this->getUser()->getCurrentMember();
	    	$expenses_service = $this->container->get('tk_expense.expenses');
	        return $this->render('SmoneyActionBundle:Actions:select.html.twig', array(
	                    'debts' => $expenses_service->getMyCurrentDebts($member),
	                ));
	    }
    }

    public function numbersAction(Request $request)
    {
    	$data = $request->request->all();

    	$member = $this->getUser()->getCurrentMember();
	    $expenses_service = $this->container->get('tk_expense.expenses');
	    $my_debts = $expenses_service->getMyCurrentDebts($member);
	    $chosen_debts = array();

	    foreach($my_debts as $debt){
	    	if(isset($data[$debt[2]->getId()])){
	    		$chosen_debts[] = $debt;
	    	}
	    }

	    $members = array();
	    $members[] = $member;
	    foreach($chosen_debts as $debt){
	    	$members[] = $debt[2];
	    }

        return $this->render('SmoneyActionBundle:Actions:numbers.html.twig', array(
        			'debts'   => $chosen_debts,
        			'members' => $members
        		));
    }

    public function confirmNumbersAction(Request $request)
    {
    	$data = $request->request->all();
    	$em = $this->getDoctrine()->getmanager();
    	$invalid = false;
    	$missing = false;
    	$members = array();

    	foreach($data as $key => $value){
    		if($value){
    			//return new JsonResponse($value);
    			$length = strlen($value);
    			$int_value = (int)$value;
    			if (is_int($int_value) and ($length == 10)){
    				$member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($key);
	    			$member->setPhone($value);	
	    			$em->persist($member);
	    			$em->flush();
	    			$members[] = $member;
    			} else {
    				$invalid = true;
    			}
    		} else {
    			$missing = true;
    		}
    	} 

    	if($missing){
    		return new JsonResponse('Please fill in all numbers');
    	} else if ($invalid) {
    		return new JsonResponse('Invalid entry');
    	} else {
    		return $this->confirmAction($request, $members);
    	}  	
    }

    public function confirmAction($request, $members)
    {
    	$expenses_service = $this->container->get('tk_expense.expenses');
  		$member = $this->getUser()->getCurrentMember();
	    $my_debts = $expenses_service->getMyCurrentDebts($member);
	    $payments = array();

	    foreach($my_debts as $debt){
	    	foreach($members as $m){
	    		if($debt[2] == $m){
	    			$payment = new Payment();
	    			$payment->setDate(new \Datetime('now'));
	    			$payment->setAmount($debt[1]);
	    			$payment->setPayer($member);
	    			$payment->setReceiver($m);

		    		$payments[] = $payment;
		    	}
	    	}
	    }

	    $defaultData = array('message' => 'Type your message here');
	    $form = $this->createFormBuilder($defaultData)
	        ->add('name', 'text')
	        ->add('email', 'email')
	        ->add('message', 'textarea')
	        ->getForm();

	    if ($request->isMethod('POST')) {

	        $form->bind($request);

	        if ($form->isValid()) {
	           	
	           	$em = $this->getDoctrine()->getManager();
	           	foreach($payments as $payment){
	           		$em->persist($payment);
	           	}	            	            
	            $em->flush();

	            return $this->redirect($this->generateUrl('smoney_action_confirmed'));
	        }
	    }

    	return $this->render('SmoneyActionBundle:Actions:confirm.html.twig', array(
			    				'form'     => $form->createView(),
			    				'payments' => $payments
			    			));
    }

    public function confirmedAction()
    {
    	return $this->render('SmoneyActionBundle:Actions:confirmed.html.twig');
    }
}
