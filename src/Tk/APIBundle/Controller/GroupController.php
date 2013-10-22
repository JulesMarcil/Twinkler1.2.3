<?php

namespace Tk\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse;

use Tk\UserBundle\Entity\User,
    Tk\UserBundle\Entity\Member,
    Tk\GroupBundle\Entity\TGroup;

class GroupController extends Controller
{
    public function postGroupAction(Request $request)
    {
        $data = $request->request->all();
        $em   = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $group_name     = $data['name'];
        $currency_id    = $data['currency'];

        $currency = $this->getDoctrine()->getRepository('TkGroupBundle:Currency')->find($currency_id);

        if ($currency) {

            $group = new TGroup();
            $group->setName($group_name);
            $group->setCurrency($currency);
            $group->setInvitationToken($group->generateInvitationToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

        }else{
            return new JsonResponse(array('error' => 'currency not found'));
        }

        $member = new Member();
        $member->setUser($user);
        $member->setName($user->getUsername());
        $member->setTGroup($group);
        $group->addMember($member);
        $em->persist($member);
        $user->setCurrentMember($member);
        $em->flush();

        $group2 = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group->getId());
        return new JsonResponse($this->returnGroupAction($group2, $member));
    }

    private function returnGroupAction($group, $member)
    {
        $group_members = array();
        foreach($group->getMembers() as $m){
            $group_members[] = array('id'          => $m->getId(), 
                                     'name'        => $m->getName(), 
                                     'picturePath' => $m->getPicturePath(), 
                                     'balance'     => $m->getBalance());
        }
        return array('id'           => $group->getId(), 
                     'name'         => $group->getName(), 
                     'currency'     => array('id'     => $group->getCurrency()->getId(),
                                             'name'   => $group->getCurrency()->getName(),
                                             'symbol' => $group->getCurrency()->getSymbol()),
                     'members'      => $group_members, 
                     'activeMember' => array('id'          => $member->getId(), 
                                             'name'        => $member->getName(), 
                                             'picturePath' => $member->getPicturePath()),
                     'link'         => $group->getInvitationToken()
                    );
    }

    public function addFriendsAction(Request $request)
    {
        $data = $request->request->all();
        $em   = $this->getDoctrine()->getManager();
        $u = $this->getUser();

        $group_id = $data['group'];
        $friends  = $data['friends'];

        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group_id);

        if($group){           

            $em = $this->getDoctrine()->getmanager();
            $repo = $this->getDoctrine()->getRepository('TkUserBundle:User');

            foreach($friends as $friend){

                $user = $repo->findByFacebookId($friend['id']);

                if(!$user){
                    
                    $user = new User();
                    $user->setEnabled(true);
                    $user->setPassword('');
                    $user->setFirstname($friend['first_name']);
                    $user->setLastname($friend['last_name']);
                    $user->setUsername($friend['name']);
                    $user->setFacebookId($friend['id']);
                    $user->setEmail($friend['username'].'@facebook.com');

                    $em->persist($user);
                    $em->flush();
                }

                $member = new Member();
                $member->setUser($user);
                $member->setName($user->getUsername());
                $member->setTGroup($group);

                $group->addMember($member);
                $user->setCurrentMember($member);

                $em->persist($member);
                $em->flush();

                $user = null;
            }

            $group2 = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($group->getId());
            return new JsonResponse($this->returnGroupAction($group2, $u->getCurrentMember()));

        } else {
            return new JsonResponse(array('error' => 'group not found'));
        }

    }
}