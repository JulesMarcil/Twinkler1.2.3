<?php

namespace Tk\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\WelcomeBundle\Entity\Subscribe;

use Tk\GroupBundle\Entity\TGroup;
use Tk\GroupBundle\Form\TGroupType;

class DefaultController extends Controller
{
    public function indexAction(){

        $securityContext = $this->container->get('security.context');
        if( !$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){

            $request = $this->getRequest();
            $language = $request->getPreferredLanguage();
            $request->setLocale($language);

            return $this->render('TkWelcomeBundle:Default:index.html.twig');
        }else{
            return $this->render('TkUserBundle:Profile:show.html.twig');
        }

    }

    public function subscribeAction(){

        $subscribe = new Subscribe();
        $subscribe->setDate(new \DateTime('now'));

        $form = $this->createFormBuilder($subscribe)
                     ->add('email', 'email')
                     ->getForm();

        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) {          
        
                $em = $this->getDoctrine()->getManager();
                $em->persist($subscribe);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_user_homepage'));
            }
        }

        return $this->render('TkWelcomeBundle:Subscribe:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function blogAction(){
        return $this->render('TkWelcomeBundle:Default:blog.html.twig');
    }

    public function blogPostAction($date)
    {
        return $this->render('TkWelcomeBundle:BlogPosts:post'.$date.'.html.twig');
    }

    public function friendsAction(){
        return $this->render('TkWelcomeBundle:Links:friends.html.twig');
    }
    public function flatmatesAction(){
        return $this->render('TkWelcomeBundle:Links:flatmates.html.twig');
    }
    public function holidaysAction(){
        return $this->render('TkWelcomeBundle:Links:holidays.html.twig');
    }
    public function clubsAction(){
        return $this->render('TkWelcomeBundle:Links:clubs.html.twig');
    }

    public function aboutAction(){
        return $this->render('TkWelcomeBundle:Links:about.html.twig');
    }
    public function pressAction(){
        return $this->render('TkWelcomeBundle:Links:press.html.twig');
    }
    public function contactAction(){
        return $this->render('TkWelcomeBundle:Links:contact.html.twig');
    }

    public function newGroupAction()
    {
        $group = new TGroup();

        $form = $this->createForm(new TGroupType(), $group);

        $request = $this->get('request');

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $group->setInvitationToken($group->generateInvitationToken());
                $em = $this->getDoctrine()->getManager();
                $em->persist($group);
                $em->flush();

                $session = $this->get('session');
                $session->set('created_group_id', $group->getId());

                return $this->redirect($this->generateUrl('fos_user_registration_register'));
            }
        }

        return $this->render('TkWelcomeBundle:CreateGroup:groupName.html.twig', array(
            'form' => $form->createView(),
            ));        
    }
}
