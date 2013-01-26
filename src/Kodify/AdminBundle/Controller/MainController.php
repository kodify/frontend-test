<?php

namespace Kodify\AdminBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MainController extends Controller
{
    /**
     * @Route("/", name="main_dashboard")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainDashboardAction()
    {

        return $this->render(
            'KodifyAdminBundle:Default:main.html.twig',
            array('userName' => $this->getUser()->getUserName())
        );
    }
}
