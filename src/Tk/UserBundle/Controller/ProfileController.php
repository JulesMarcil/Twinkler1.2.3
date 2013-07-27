<?php

namespace Tk\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tk\UserBundle\Entity\User;
use Tk\UserBundle\Form\UserType;
use Tk\UserBundle\Form\UsernameType;
use Tk\UserBundle\Entity\ProfilePicture;

class ProfileController extends Controller
{
    public function showAction(){ return $this->indexAction();}
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        if( !$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            return $this->render('TkWelcomeBundle:Default:index.html.twig');
        }else{
            return $this->render('TkUserBundle:Profile:show.html.twig');
        }
    }

    public function editAction($id)
    {
        $user = $this->getUser();

        if($user->getId() != $id) {
            throw new AccessDeniedException('You do not have access to this page');
        }

        $form = $this->createForm(new UserType(), $user);
        
        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) {

                foreach($user->getMembers() as $member){
                    $member->setName($user->getUsername());
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_user_homepage'));
        }}

        return $this->render('TkUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView(),
            )); 
    }

    public function editUsernameAction($id)
    {
        $user = $this->getUser();

        if($user->getId() != $id) {
            throw new AccessDeniedException('You do not have access to this page');
        }

        $form = $this->createForm(new UsernameType(), $user);
        
        $request = $this->get('request');

        if ($request->isMethod('POST')) {
            
            $form->bind($request);

            if ($form->isValid()) {

                foreach ($user->getMembers() as $member){
                    $member->setName($user->getUsername());
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_user_homepage'));
        }}

        return $this->render('TkUserBundle:Profile:editUsername.html.twig', array(
            'form' => $form->createView(),
            'balances' => $this->getBalancesAction(),
            )); 
    }

    public function editProfilePictureAction()
    {
        $profilepicture = new ProfilePicture();
        $form = $this->createFormBuilder($profilepicture)
                     ->add('file')
                     ->getForm()
        ;

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                
                $user = $this->getUser();
                $currentpicture = $user->getPicture();
                $profilepicture->upload($user);
                $user->setPicture($profilepicture);
                
                if ($currentpicture->getId() !== 1) {
                $em->remove($currentpicture);
                }
                
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('tk_user_homepage'));
            }
        }

        return $this->render('TkUserBundle:Profile:picture-form.html.twig', array(
            'form' => $form->createView(),
                ));
    }
}