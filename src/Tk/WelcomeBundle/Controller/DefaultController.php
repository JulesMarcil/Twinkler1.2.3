<?php

namespace Tk\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\WelcomeBundle\Entity\Subscribe;

use Tk\GroupBundle\Entity\TGroup;
use Tk\GroupBundle\Form\TGroupType;
use Tk\ListBundle\Entity\Lists;

class DefaultController extends Controller
{
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
        
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($subscribe);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_user_homepage'));
            }
        }

        return $this->render('TkWelcomeBundle:Subscribe:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function aboutAction(){
        return $this->render('TkWelcomeBundle:Links:about.html.twig');
    }

    public function blogAction(){
        return $this->render('TkWelcomeBundle:Links:blog.html.twig');
    }

    public function blogPostAction($date)
    {
        return $this->render('TkWelcomeBundle:BlogPosts:post'.$date.'.html.twig');
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

                $em = $this->getDoctrine()->getEntityManager();

                $group->setInvitationToken($group->generateInvitationToken());
                $todolist = new Lists();
                $todolist->setName('Todo List');
                $todolist->setGroup($group);
                $shoppinglist = new Lists();
                $shoppinglist->setName('Shopping List');
                $shoppinglist->setGroup($group);
                $em->persist($todolist);
                $em->persist($shoppinglist);
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
