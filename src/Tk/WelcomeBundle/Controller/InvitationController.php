<?php

namespace Tk\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Tk\UserBundle\Entity\Member;

class InvitationController extends Controller
{
    public function chooseMemberAction($id, $token)
    {
        $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($id);

        if ($token != $group->getInvitationToken()){
            throw new AccessDeniedException('You try to access a wrong Url');
        }else{
            return $this->render('TkWelcomeBundle:Invitation:chooseMember.html.twig', array(
                'group'   => $group,
                ));
        }
    }

    public function chosenMemberAction($id, $token, $type)
    {
        if ($type == 'group') {

            $group = $this->getDoctrine()->getRepository('TkGroupBundle:TGroup')->find($id);

            if ($token != $group->getInvitationToken()){
                throw new AccessDeniedException('The invitation has expired or the url is wrong');
            } else {

                $member = new Member();
                $member->setName('');
                $member->setEmail('temp@twinkler.co');
                $member->setInvitationToken($member->generateInvitationToken());
                $member->setTGroup($group);
                $member->setActive(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($member);
                $em->flush();

                $token = $member->getInvitationToken();
            }

        } else if ($type == 'member') {

            $member = $this->getDoctrine()->getRepository('TkUserBundle:Member')->find($id);

        } else {

            throw new AccessDeniedException('You are trying to access a wrong url, please contact our support for debugging');
        }

        if ($token != $member->getInvitationToken()) {
            throw new AccessDeniedException('The invitation has expired or the url is wrong');
        } else {

            $user = $this->getUser();

            if ($user){

                return $this->setUser($user, $member);

            } else {

                $session = $this->get('session');
                $session->set('invitation_id', $member->getId());
                $session->set('invitation_member', $member->getName());
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
        }
    }

    private function setUser($user, $member) 
    {
        foreach($user->getMembers() as $user_member){
            if ($user_member->getTGroup() == $member->getTGroup()){
                $user->setCurrentMember($user_member);
                return $this->redirect($this->generateUrl('tk_expense_homepage'));
            }
        }

        $member->setUser($user);
        $member->setName($user->getUsername());
        $member->setInvitationToken(null);
        $user->setCurrentMember($member);
        $em = $this->getDoctrine()->getManager();
        $em->persist($member);
        $em->flush();

        return $this->redirect($this->generateUrl('tk_expense_homepage'));
    }
}
