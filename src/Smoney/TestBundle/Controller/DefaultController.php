<?php

namespace Smoney\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\Exclude;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SmoneyTestBundle:Default:index.html.twig');
    }

    // --- GET AN ACCESS TOKEN --- //

    public function getTokenAction()
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

		return new Response($result);
    }

    // --- SEND MONEY --- //

    public function sendAction(Request $request)
    {
    	$data = $request->request->all();

    	if ($data['access_token']) {

    		$params = array ('beneficiary' => array( 'identifier' => $data['beneficiary'] ), 
						 'amount'      => floatval($data['amount']), 
						 );

	    	if($data['transmitter']){
	    		$params['transmitter'] = array( 'identifier' => $data['transmitter'] );
	    	}
	    	if($data['message']){
	    		$params['message'] = $data['message'];
	    	}

	    	$json = json_encode($params);

	    	$curl = curl_init('https://rest2.s-money.net/api/payments');
	    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    	curl_setopt($curl, CURLOPT_COOKIESESSION, true); 
	    	curl_setopt($curl, CURLOPT_POST, true);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	    	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json',                                                                                
			    'Content-Length: ' . strlen($json),
			    'Authorization: Bearer '.$data['access_token'])                                                                       
			);

	    	$result = curl_exec($curl);
	    	curl_close($curl);

	    	return new Response($result);	
    	}
    	
		return new JsonResponse(array('error' => 'no access token provided'));
    }

    // --- SEE HISTORY OF PAYMENTS MADE --- //

    public function sentAction(Request $request)
    {
    	$data = $request->query->all();

    	if ($data['access_token']) {

    		$curl = curl_init('https://rest2.s-money.net/api/payments');
	    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    	curl_setopt($curl, CURLOPT_COOKIESESSION, true);
	    	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json',
			    'Authorization: Bearer '.$data['access_token'])                                                                       
			);

	    	$result = curl_exec($curl);
	    	curl_close($curl);

			return new Response($result);
    	} 
    	return new Jsonresponse(array('error' => 'no access token provided'));
    }

    // --- SEND A REQUEST FOR MONEY --- //

    public function requestAction(Request $request)
    {
    	$data = $request->request->all();

    	$params = array ('receiver' => array( 'identifier' => $data['receiver'] ), 
						 'amount'      => floatval($data['amount']), 
						 );

    	if($data['requester']){
    		$params['requester'] = array( 'identifier' => $data['requester'] );
    	}
    	if($data['message']){
    		$params['message'] = $data['message'];
    	}

    	$json = json_encode($params);

    	if ($data['access_token']) {

			$curl = curl_init('https://rest2.s-money.net/api/paymentrequests');
	    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    	curl_setopt($curl, CURLOPT_COOKIESESSION, true); 
	    	curl_setopt($curl, CURLOPT_POST, true);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	    	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json',                                                                                
			    'Content-Length: ' . strlen($json),
			    'Authorization: Bearer '.$data['access_token'])                                                                       
			);

	    	$result = curl_exec($curl);
	    	curl_close($curl);

			return new Response($result);
		}
		return new JsonResponse(array('error' => 'no access token provided'));
    }

    // --- SEE HISTORY OF REQUESTS RECEIVED --- //

    public function requestedAction(Request $request)
    {
    	$data = $request->query->all();

    	if ($data['access_token']) {

    		$curl = curl_init('https://rest2.s-money.net/api/paymentrequests');
	    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    	curl_setopt($curl, CURLOPT_COOKIESESSION, true);
	    	curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json',
			    'Authorization: Bearer '.$data['access_token'])                                                                       
			);

	    	$result = curl_exec($curl);
	    	curl_close($curl);

			return new Response($result);
    	} 
    	return new Jsonresponse(array('error' => 'no access token provided'));
    }
}
