<?php

namespace Smoney\ActionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Smoney\ActionBundle\Entity\Payment;
use Tk\ExpenseBundle\Entity\Expense;

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
	    			$members[] = $key;
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

            $session = $this->get('session');
            $session->set('receivers', $members);


    		return $this->render('SmoneyActionBundle:Actions:confirm.html.twig');
    	}  	
    }

    public function confirmAction(Request $request)
    {
    	$expenses_service = $this->container->get('tk_expense.expenses');
  		$member = $this->getUser()->getCurrentMember();
	    $my_debts = $expenses_service->getMyCurrentDebts($member);
	    $payments = array();
        $paybacks = array();

        $session = $this->get('session');
        $receivers = $session->get('receivers');

	    foreach($my_debts as $debt){
	    	foreach($receivers as $id){
                $m = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($id);
	    		if($debt[2] == $m){
	    			$payment = new Payment();
	    			$payment->setDate(new \Datetime('now'));
	    			$payment->setAmount($debt[1]);
	    			$payment->setPayer($member);
	    			$payment->setReceiver($m);

		    		$payments[] = $payment;

                    $expense = new Expense();
                    $expense->setType('payback');
                    $expense->setName('Payback S-money');
                    $expense->setAuthor($member);
                    $expense->setOwner($member);
                    $expense->setAmount($debt[1]);
                    $expense->setUsers($m);
                    $expense->setGroup($member->getTGroup());
                    $expense->setAddedDate(new \DateTime('now'));
                    $expense->setDate(new \Datetime('today'));
                    $expense->setActive(true);

                    $paybacks[] = $expense;
		    	}
	    	}
	    }

	    $defaultData = null;
	    $form = $this->createFormBuilder($defaultData)->getForm();

	    if ($request->isMethod('POST')) {

	        $form->bind($request);

	        if ($form->isValid()) {
	           	
                $response = $this->sendRequests($payments, $paybacks);

                if($response['success']){
                    return $this->redirect($this->generateUrl('smoney_action_confirmed'));
                }
	        }
	    }

    	return $this->render('SmoneyActionBundle:Actions:confirmationForm.html.twig', array(
			    				'form'     => $form->createView(),
			    				'payments' => $payments
			    			));
    }

    public function confirmedAction()
    {
    	return $this->render('SmoneyActionBundle:Actions:confirmed.html.twig');
    }

    private function sendRequests($payments, $paybacks)
    {
        $token = $this->getToken();

        if($token){
            $em = $this->getDoctrine()->getManager();

            //throw new NotFoundHttpException("Payments = ".count($payments));

            for ($i = 0; $i<count($payments); $i++){
                $payment = $payments[$i];

                $amount = 100*($payment->getAmount());
                $receiver = $payment->getReceiver()->getPhone();
                $requester = null;
                $message = null;

                $request = $this->request($token, $amount, $receiver, $requester, $message);
                
                if ($request['success']){
                    $payment->setConfirmed(1);
                    $em->persist($payment);

                    $payback = $paybacks[$i];
                    $em->persist($payback);
                }  
            }
            $em->flush();
            return array('success' => 'Requests have been sent to confirm payments');
        } else {
            return array('error' => 'Impossible to get a valid token from S-money');
        }
    }

    private function request($token, $amount, $receiver, $requester, $message)
    {
        $params = array ('receiver' => array( 'identifier' => $receiver ), 
                         'amount'      => floatval($amount), 
                         );

        if($requester){
            $params['requester'] = array( 'identifier' => $requester );
        }
        if($message){
            $params['message'] = $message;
        }

        $json = json_encode($params);

        $curl = curl_init('https://rest2.s-money.net/api/paymentrequests');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true); 
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($json),
            'Authorization: Bearer '.$token)                                                                       
        );

        $result = curl_exec($curl);
        curl_close($curl);

        return array('success' => $result);
    }

    private function getToken()
    {
        $params = array ('grant_type' => 'password', 
                         'username'   => 'twinkler',
                         'password'   => hash('sha256', '123450'),
                         'scope'      => 'all',
                         );
        
        // Build Http query using params
        $query = http_build_query ($params);
         
        // Create Http context details
        $contextData = array ( 
                        'protocol_version'=> '1.1',
                        'method'          => 'POST',
                        'header'          => "Connection: close\r\n".
                                             "Content-Type: application/x-www-form-urlencoded\r\n".
                                             "Content-Length: ".strlen($query)."\r\n".
                                             "Authorization: Basic ".base64_encode('twinkler:9dgDN9EoixFH2Ut1B95g4KdV7xQbFi'),
                        'content'         => $query );
         
        // Create context resource for our request
        $context = stream_context_create (array ( 'http' => $contextData ));
         
        // Read page rendered as result of your POST request
        $result =  file_get_contents (
                          'https://rest2.s-money.net/oauth/token',  // page url
                          false,
                          $context);

        $result =  json_decode($result);
        return $result->access_token;
    }
}
