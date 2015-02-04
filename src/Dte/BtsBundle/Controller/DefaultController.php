<?php

namespace Dte\BtsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DteBtsBundle:Default:index.html.twig');
    }
}
