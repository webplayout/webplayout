<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Default controller.
 *
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="default_index")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
    //        'entities' => $result
        ]);
    }
}
