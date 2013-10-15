<?php

namespace Tk\GroupBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Tk\GroupBundle\Entity\TGroup,
    Tk\GroupBundle\Form\TGroupType,
    Tk\UserBundle\Entity\Member;

class DashboardController extends Controller
{
    public function summaryModalAction()
    {
        return $this->render('TkGroupBundle:Dashboard:sendSummaryModal.html.twig');
    }
}
