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

    public function sendAction(Request $request)
    {
    	$data = $request->request->all();


    	$params = array ('beneficiary' => array( 'identifier' => $data['beneficiary'] ), 
						 'amount'      => floatval($data['amount']), 
						 );

    	if($data['transmitter']){
    		$params['transmitter'] = array( 'identifier' => $data['transmitter'] );
    	}
    	if($data['message']){
    		$params['message'] = $data['transmitter'];
    	}

    	if ($data['access_token']) {
			// Build Http query using params
			$query = http_build_query ($params);
			 
			// Create Http context details
			$contextData = array ( 
							'protocol_version'=> '1.1',
			                'method'          => 'POST',
			                'header'          => "Connection: close\r\n".
			                			         "Content-Type: application/json\r\n".
			                                     "Content-Length: ".strlen($query)."\r\n".
			                                     "Authorization: Bearer ".$data['access_token'],
			                'content'         => $query );
			 
			// Create context resource for our request
			$context = stream_context_create (array ( 'http' => $contextData ));
			 
			// Read page rendered as result of your POST request
			$result =  file_get_contents (
			                  'https://rest2.s-money.net/api/payments',  // page url
			                  false,
			                  $context);

			return new Response($result);
		}
		return new JsonResponse(array('error' => 'no access token provided'));
    }

    public function sentAction(Request $request)
    {
    	$data = $request->query->all();

    	if ($data['access_token']) {

    		// Build Http query using no params
    		$params = array();
			$query = http_build_query ($params);
			 
			// Create Http context details
			$contextData = array ( 
							'protocol_version'=> '1.1',
			                'method'          => 'GET',
			                'header'          => "Connection: close\r\n".
			                			         "Content-Type: application/json\r\n".
			                                     "Content-Length: ".strlen($query)."\r\n".
			                                     "Authorization: Bearer ".$data['access_token'],
			                'content'         => $query );
			 
			// Create context resource for our request
			$context = stream_context_create (array ( 'http' => $contextData ));
			 
			// Read page rendered as result of your POST request
			$result =  file_get_contents (
			                  'https://rest2.s-money.net/api/payments',  // page url
			                  false,
			                  $context);

			return new Response($result);
    	} 
    	return new Jsonresponse(array('error' => 'no access token provided'));
    }

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
    		$params['message'] = $data['transmitter'];
    	}

    	if ($data['access_token']) {

			// Build Http query using params
			$query = http_build_query ($params);
			 
			// Create Http context details
			$contextData = array ( 
							'protocol_version'=> '1.1',
			                'method'          => 'POST',
			                'header'          => "Connection: close\r\n".
			                			         "Content-Type: application/json\r\n".
			                                     "Content-Length: ".strlen($query)."\r\n".
			                                     "Authorization: Bearer ".$data['access_token'],
			                'content'         => $query );
			 
			// Create context resource for our request
			$context = stream_context_create (array ( 'http' => $contextData ));
			 
			// Read page rendered as result of your POST request
			$result =  file_get_contents (
			                  'https://rest2.s-money.net/api/paymentrequests',  // page url
			                  false,
			                  $context);

			return new Response($result);
		}
		return new JsonResponse(array('error' => 'no access token provided'));
    }

    public function requestedAction(Request $request)
    {
    	$data = $request->query->all();

    	if ($data['access_token']) {

    		// Build Http query using no params
    		$params = array();
			$query = http_build_query ($params);
			 
			// Create Http context details
			$contextData = array ( 
							'protocol_version'=> '1.1',
			                'method'          => 'GET',
			                'header'          => "Connection: close\r\n".
			                			         "Content-Type: application/json\r\n".
			                                     "Content-Length: ".strlen($query)."\r\n".
			                                     "Authorization: Bearer ".$data['access_token'],
			                'content'         => $query );
			 
			// Create context resource for our request
			$context = stream_context_create (array ( 'http' => $contextData ));
			 
			// Read page rendered as result of your POST request
			$result =  file_get_contents (
			                  'https://rest2.s-money.net/api/paymentrequests',  // page url
			                  false,
			                  $context);

			return new Response($result);
    	} 
    	return new Jsonresponse(array('error' => 'no access token provided'));
    }
}
