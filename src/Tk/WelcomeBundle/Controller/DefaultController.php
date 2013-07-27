<?php

namespace Tk\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\WelcomeBundle\Entity\Subscribe;

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
}
