<?php

namespace Tk\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
	public function defaultAction()
	{
		if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
		    return $this->redirect($this->generateUrl('tk_user_homepage'));
		} else {
			$groups = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->findAll();
			$em = $this->getDoctrine()->getmanager();
			foreach($groups as $group){
				$members = $group->getMembers();
				$all_members = $group->getAllMembers();
				if (sizeof($members) > 0) {
					$group->setActive(1);	
				} else {
					$group->setActive(0);
					foreach($all_members as $m){
						$m->setActive(1);
						$em->persist($m);
					}
				}
				$em->persist($group);
			}
			$em->flush();
			return $this->redirect($this->generateUrl('tk_admin_homepage'));
		}	
	}

    public function indexAction()
    {
    	if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
		    return $this->redirect($this->generateUrl('tk_user_homepage'));
		} else {
	    	$group_id = $this->getRequest()->query->get('group_id');
	    	$user_id  = $this->getRequest()->query->get('user_id');

	    	if ($group_id) {
	    		$group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);
	    	} else {
	    		$group = null;
	    	}

	    	if ($user_id) {
	    		$user = $this->getDoctrine()->getRepository('TkUserBundle:user')->find($user_id);
	    	} else {
	    		$user = null;
	    	}

	    	$userStats = $this->userStatistics();

	        return $this->render('TkAdminBundle::index.html.twig', array(
	        	'userStats' => $this->userStatistics(),
	        	'groupStats'=> $this->groupStatistics(),
	        	'group'     => $group,
	        	'user'      => $user
	        	));
        }
    }

    private function userStatistics()
    {
    	$users = $this->getDoctrine()->getRepository('TkUserBundle:User')->findAll();

    	$total = 0;
    	$login_month = 0;
    	$login_week = 0;
		$login_day = 0;
		$facebook = 0;
		$no = 0;
		$one = 0;
		$two = 0;
		$three = 0;
		$groups = 0;

		$now_date = new \Datetime('now');
		$now = $now_date->getTimestamp();

		foreach($users as $user) {
			
			$total ++;
			$last_login_datetime = $user->getLastLogin();

			if ($last_login_datetime) {

				$last_login = $last_login_datetime->getTimestamp();
				
				if ($last_login + 86400 > $now) {
					$login_day   ++;
					$login_week  ++;
					$login_month ++;
				} else if ($last_login + 604800 > $now){
					$login_week  ++;
					$login_month ++;
				} else if ($last_login + 2629744 > $now){
					$login_month ++;
				} else {
					// Last login older than a month
				}
			}
			if ($user->getFacebookId()) {
				$facebook += 1;
			}

			$g = sizeof($user->getMembers());
			$groups+=$g;

			switch($g){
				case 0:
					$no ++;
					break;
				case 1:
					$one ++;
					break;
				case 2:
					$two ++;
					break;
				default:
					$three ++;
			}
		}

		return array($total,
    				$login_month,
    				$login_week,
					$login_day,
					$facebook,
					$no,
					$one,
					$two,
					$three,
					$groups
					);
    }

    private function groupStatistics()
    {
    	$groups = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->findAll();

    	$total = 0;
    	$active = 0;
    	$login_month = 0;
    	$login_week = 0;
		$login_day = 0;
		$no = 0;
		$one = 0;
		$two = 0;
		$three = 0;
		$four = 0;
		$members = 0;

		$now_date = new \Datetime('now');
		$now = $now_date->getTimestamp();

		foreach($groups as $group) {
			$total ++;
			$m = sizeof($group->getMembers());

			if ($m > 0) {
				
				$active ++;
				$members += $m;

				$last_login = 0;

				foreach($group->getMembers() as $member){

					if ($member->getUser() and $member->getUser()->getLastLogin()) {

						if ($member->getUser()->getLastLogin()->getTimestamp() > $last_login) {

							$last_login = $member->getUser()->getLastLogin()->getTimestamp();	
						}
					}
				}
				
				if ($last_login + 86400 > $now) {
					$login_day   ++;
					$login_week  ++;
					$login_month ++;
				} else if ($last_login + 604800 > $now){
					$login_week  ++;
					$login_month ++;
				} else if ($last_login + 2629744 > $now){
					$login_month ++;
				} else {
					// Last login older than a month
				}

				$g = sizeof($group->getMembers());
				switch($g){
					case 0:
						$no ++;
						break;
					case 1:
						$one ++;
						break;
					case 2:
						$two ++;
						break;
					case 3:
						$three ++;
						break;
					default:
						$four ++;
				}
			}
		}

		return array($total,
					$active,
    				$login_month,
    				$login_week,
					$login_day,
					$no,
					$one,
					$two,
					$three,
					$four,
					$members
					);
	}
}
