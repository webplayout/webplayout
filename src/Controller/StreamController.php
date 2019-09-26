<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Stream controller.
 *
 */
class StreamController extends AbstractController
{
    /**
     * @Route("/stream", methods={"GET"}, name="stream_index")
     */
    public function index(): Response
    {
        return $this->render('stream/index.html.twig');
    }
}
