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

    public function tokenAction()
    {
    	$request = $this->getRequest();
    	$data = $request->request->all();
		
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
		                                     "Content-Length: 81\r\n".
		                                     "Authorization: Basic ".base64_encode('twinkler:9dgDN9EoixFH2Ut1B95g4KdV7xQbFi'),
		                'content'         => $query );
		 
		// Create context resource for our request
		$context = stream_context_create (array ( 'http' => $contextData ));
		 
		// Read page rendered as result of your POST request
		$result =  file_get_contents (
		                  'https://rest2.s-money.net/oauth/token',  // page url
		                  false,
		                  $context);

		return $result;
    }
}
