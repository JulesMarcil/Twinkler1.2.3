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
        	$member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
            $group = $member->getTGroup();

        	if ($group){

	        	$messages_array = array();

	        	$messages = $group->getMessages();
	        	foreach($messages as $message){
	        		$message_array = array('type'        => 'message',
                                     'author'      => $message->getAuthor()->getName(),
                                     'body'        => $message->getBody(),
                                     'time'        => $message->getTimestamp(),
                                     'picturePath' => $message->getAuthor()->getPicturePath()      							   
	        							   );
	        		$messages_array[] = $message_array;
	        	}
	        	
                /*
	        	$expenses = $group->getExpenses();
	        	foreach($expenses as $expense){

                    if ($expense->getOwner() == $member) {
                        $name = 'You';
                    } else {
                        $name = $expense->getOwner()->getName();
                    }

	        		$message_array = array('type'        => 'expense',
                                     'author'      => $expense->getAuthor()->getName(),
	        							             'time'        => $expense->getAddedDate()->getTimestamp(),
                                     'owner'       => $name,
                                     'amount'      => $expense->getAmount(),
                                     'name'        => $expense->getName(),
                                     'share'       => $this->container->get('tk_expense.expenses')->youGet($member, $expense),
                                     'picturePath' => $expense->getOwner()->getPicturePath()
	        							   );
	        		$messages_array[] = $message_array;
	        	}

	        	usort($messages_array, array('Tk\ChatBundle\Controller\APPChatController', 'cmp'));
                */

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

    public function getNewMessagesAction($count)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if($user) {

            $data = $this->getRequest()->query->all();
            $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($data['currentMemberId']);
            $group = $member->getTGroup();

            if ($group){

                $messages_array = array();

                $messages = $group->getMessages();
                foreach($messages as $message){
                    $message_array = array('type'        => 'message',

                                           'author'      => $message->getAuthor()->getName(),
                                           'body'        => $message->getBody(),
                                           'time'        => $message->getTimestamp(),
                                           'picturePath' => $message->getAuthor()->getPicturePath()                                    
                                           );
                    $messages_array[] = $message_array;
                }
                
                $expenses = $group->getExpenses();
                foreach($expenses as $expense){

                    if ($expense->getOwner() == $member) {
                        $name = 'You';
                    } else {
                        $name = $expense->getOwner()->getName();
                    }

                    $message_array = array('type'        => 'expense',
                                           'author'      => $expense->getAuthor()->getName(),
                                           'time'        => $expense->getAddedDate()->getTimestamp(),
                                           'owner'       => $name,
                                           'amount'      => $expense->getAmount(),
                                           'name'        => $expense->getName(),
                                           'share'       => $this->container->get('tk_expense.expenses')->youGet($member, $expense),
                                           'picturePath' => $expense->getOwner()->getPicturePath()
                                           );
                    $messages_array[] = $message_array;
                }

                usort($messages_array, array('Tk\ChatBundle\Controller\APPChatController', 'cmp'));

                $new_messages_array = array();
                for ($i = $count; $i < sizeof($messages_array); $i++) {
                    $new_messages_array[] = $messages_array[$i];
                }

                return new JsonResponse($new_messages_array);
            }
            return new JsonResponse(array('message' => 'Current group not found')); 
        }   
        return new JsonResponse(array('message' => 'User is not identified'));
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
