<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default controller.
 *
 * Handles the home page.
 */
class DefaultController extends Controller
{

    /**
     * Home page action.
     *
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction() {
        // replace this example code with whatever you need
        return [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ];
    }
}
