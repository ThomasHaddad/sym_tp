<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/old", name="old-homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
