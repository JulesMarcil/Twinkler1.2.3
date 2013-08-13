<?php

namespace Tk\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tk\ChatBundle\Entity\Message;

class APPChatController extends Controller
{
    public function getMessagesAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
        if($user) {

        	$data = $this->getRequest()->query->all();
        	$group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($data['currentGroupId']);

        	if ($group){

	        	$messages_array = array();

	        	$messages = $group->getMessages();
	        	foreach($messages as $message){
	        		$message_array = array('author' => $message->getAuthor()->getName(),
	        							   'body'   => $message->getBody(),
	        							   'time'   => $message->getTimestamp(),
	        							   'type'   => 'message'
	        							   );
	        		$messages_array[] = $message_array;
	        	}
	        	
	        	$expenses = $group->getExpenses();
	        	foreach($expenses as $expense){
	        		$message_array = array('author' => $expense->getAuthor()->getName(),
	        							   'body'   => $expense->getAuthor()->getName().' added an expense: '.$expense->getName().' - '.$expense->getAmount().''.$group->getCurrency()->getSymbol(),
	        							   'time'   => $expense->getAddedDate()->getTimestamp(),
	        							   'type'   => 'expense'
	        							   );
	        		$messages_array[] = $message_array;
	        	}

	        	usort($messages_array, array('Tk\ChatBundle\Controller\APPChatController', 'cmp'));

				return new JsonResponse($messages_array);
			}
			return new JsonResponse(array('message' => 'Current group not found'));	
        }	
        return new JsonResponse(array('message' => 'User is not identified'));
    }

    private function cmp($a, $b){
    	if ($a['time'] == $b['time']){
    		return 0;
    	}
    	return ($a['time'] < $b['time']) ? -1 : 1;
    }

    public function postMessageAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
        if($user) {
        	$data = $this->getRequest()->query->all();
        	$doctrine = $this->getDoctrine(); 
        	$group = $doctrine->getRepository('TkGroupBundle:TGroup')->find($data['currentGroupId']);
        	$member = $doctrine->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);

        	if ($group){

        		$message = New Message();
        		$message->setTimestamp(time());
        		$message->setBody($data['messageBody']);
        		$message->setAuthor($member);
        		$message->setGroup($group);

        		$em = $doctrine->getManager();
        		$em->persist($message);
        		$em->flush();

        		return new JsonResponse(array('message' => 'Message added successfully'));
			}
			return new JsonResponse(array('message' => 'Current group not found'));
        }
        return new JsonResponse(array('message' => 'User is not identified'));
    }
}
