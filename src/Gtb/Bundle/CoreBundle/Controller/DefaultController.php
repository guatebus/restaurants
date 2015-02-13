<?php

namespace Gtb\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GtbCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
