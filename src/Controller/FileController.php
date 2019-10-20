<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
#use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\File;
use Omarev\TvBundle\Form\FilesType;

//use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;

use App\Repository\FileRepository;
use App\Service\MediaDuration;
/**
 * Files controller.
 *
 */
class FileController extends AbstractController
{
    /**
     * Displays a form to create a new Files entity.
     * @Route("/files/upload", methods={"GET"}, name="file_new")
     */
    public function newAction()
    {
        return $this->render('file/upload.html.twig');
    }

    /**
     * @Route("/files/upload", methods={"POST"}, name="file_upload")
     */
    function uploadAction(Request $request, MediaDuration $mediaDuration)
    {
        $media_dir = $this->getParameter('media_dir');

        $output = array('uploaded' => false);


        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse('No file', 400);
        }

        //$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originalFilename = $file->getClientOriginalName();

        $resumableFilename = $request->get('resumableFilename');
        $resumableChunkNumber = $request->get('resumableChunkNumber');
        $resumableTotalChunks = $request->get('resumableTotalChunks');
        $chunkFileName = $resumableFilename.'.part.'.$resumableChunkNumber;

        if ($file->move($media_dir, $chunkFileName)) {
           $output['uploaded'] = true;
           $output['fileName'] = $chunkFileName;
        }

        //$fileSystem = new Filesystem();

        if ($resumableChunkNumber >= $resumableTotalChunks){
            for ($i=1; $i <= $resumableTotalChunks; $i++) {

                $chunkFileName = $media_dir . DIRECTORY_SEPARATOR . $resumableFilename.'.part.'.$i;
                $chunk = file_get_contents($chunkFileName);

                if (false === $chunk) {
                    throw new \Exception("Error Processing Request", 1);
                }

                $fileName = $media_dir . DIRECTORY_SEPARATOR . $originalFilename;// . '.' . //$file->guessExtension();

                file_put_contents($fileName, $chunk, FILE_APPEND);

                if ($resumableTotalChunks != $resumableChunkNumber) {
                    @unlink($chunkFileName);
                }
                // $fileSystem->appendToFile(
                //     $media_dir . DIRECTORY_SEPARATOR . $originalFilename,
                //     file_get_contents($resumableFilename.'.part.'.$i)
                // );
            }

            $duration = $mediaDuration->getDuration($fileName);

            $entity = new File();
            $entity->setName(basename($originalFilename));
            $entity->setFile($originalFilename);
            $entity->setDuration($duration);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }

        return new JsonResponse($output, 201);
    }
}
