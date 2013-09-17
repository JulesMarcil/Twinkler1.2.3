<?php

namespace Tk\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Tk\ChatBundle\Entity\Message;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$user = $this->getUser();
    	$member = $user->getCurrentMember();
    	$group = $member->getTGroup();

    	$last_expenses = $this->getLastExpenses($group);
    	$last_messages = $this->getLastMessages($group);

        return $this->render('TkChatBundle::index.html.twig', array(
        	'last_expenses' => $last_expenses,
        	'last_messages' => $group->getMessages(),
        	'count'			=> sizeof($group->getExpenses())-sizeof($last_expenses)
        	));
    }

    public function ajaxContentAction()
    {
    	$user = $this->getUser();
    	$member = $user->getCurrentMember();
    	$group = $member->getTGroup();

    	$last_expenses = $this->getLastExpenses($group);
    	$last_messages = $this->getLastMessages($group);

        return $this->render('TkChatBundle:Chat:content.html.twig', array(
        	'last_expenses' => $last_expenses,
        	'last_messages' => $last_messages,
        	'count'			=> sizeof($group->getExpenses())-sizeof($last_expenses)
        	));
    }

    public function ajaxNewAction()
    {
    	$user = $this->getUser();
    	$member = $user->getCurrentMember();
    	$group = $member->getTGroup();
    	$data = $this->getRequest()->query->all();

    	if ($data) {

	        $message = New Message();
			$message->setTimestamp(time());
			$message->setBody($data['new_message']);
			$message->setAuthor($member);
			$message->setGroup($group);

			$em = $this->getDoctrine()->getManager();
			$em->persist($message);
			$em->flush();
		} 
		return $this->messagesAction();
	}

	public function messagesAction()
	{
		$messages = $this->getUser()->getCurrentMember()->getTGroup()->getMessages();
		return $this->render('TkChatBundle:Chat:messageList.html.twig', array(
        	'last_messages' => $messages
        	));  
    }

    private function getLastExpenses($group)
    {
    	$expenses = $group->getExpenses()->toArray();
    	usort($expenses, array('Tk\ChatBundle\Controller\DefaultController', 'cmpExp'));
    	return array_slice($expenses, 0, 4); 
	}

    private function cmpExp($a, $b){
    	if ($a->getAddedDate() == $b->getAddedDate()){
    		return 0;
    	}
    	return ($a->getAddedDate() < $b->getAddedDate()) ? -1 : 1;
    }

    private function getLastMessages($group)
    {
    	$messages = $group->getMessages()->toArray();
    	usort($messages, array('Tk\ChatBundle\Controller\DefaultController', 'cmpMes'));
    	return array_slice($messages, 0, 4); 
	}

    private function cmpMes($a, $b){
    	if ($a->getTimestamp() == $b->getTimestamp()){
    		return 0;
    	}
    	return ($a->getTimestamp() < $b->getTimestamp()) ? -1 : 1;
    }
}
