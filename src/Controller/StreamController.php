<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

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
        $url = $this->generateUrl('hls_stream');

        // add the port number to the url
        $stream_url = str_replace(
            $url,
            ':8583' . $url,
            $this->generateUrl('hls_stream', [], UrlGenerator::ABSOLUTE_URL)
        );

        return $this->render('stream/index.html.twig', ['stream_url' => $stream_url]);
    }
}
