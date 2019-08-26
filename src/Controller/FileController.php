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
/**
 * Files controller.
 *
 */
class FileController extends AbstractController
{
    // /**
    //  * Lists all Files entities.
    //  *
    //  * @Route("/files", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="file_index")
    //  */
    // public function index(FileRepository $files): Response
    // {
    //     $result = $files->findBy([], ['id' => 'DESC']);
    //
    //     return $this->render('file/index.html.twig', [
    //         'entities' => $result
    //     ]);
    // }

    // public function indexAction(FileRepository)
    // {
    //     //print_r(get_class_methods($request)); die;
    //     if ($request->isXmlHttpRequest()) {
    //         $query = $this->getDoctrine()
    //             ->getRepository('File')
    //             ->createQueryBuilder('f')
    //             ->getQuery();
    //
    //         return new JsonResponse($query->getArrayResult(), 200);
    //     }
    //
    //     $em = $this->getDoctrine()->getManager();
    //
    //     $entities = $em->getRepository('File')->findAll();
    //
    //     return $this->render('OmarevTvBundle:Files:index.html.twig', array(
    //         'entities' => $entities,
    //     ));
    // }

    /**
     * Finds and displays a Files entity.
     *
     * @Route("/files/{id}", methods={"GET"},requirements={"id": "\d+"}, name="file_show")
     */
    // public function showAction($id)
    // {
    //     $em = $this->getDoctrine()->getManager();
    //
    //     $entity = $em->getRepository('OmarevTvBundle:Files')->find($id);
    //
    //     if (!$entity) {
    //         throw $this->createNotFoundException('Unable to find Files entity.');
    //     }
    //
    //     $deleteForm = $this->createDeleteForm($id);
    //
    //     return $this->render('OmarevTvBundle:Files:show.html.twig', array(
    //         'entity'      => $entity,
    //         'delete_form' => $deleteForm->createView(),        ));
    // }

    /**
     * Displays a form to create a new Files entity.
     * @Route("/files/upload", methods={"GET"}, name="file_new")
     */
    public function newAction()
    {
        // $entity = new Files();
        // $form   = $this->createForm(new FilesType(), $entity);

        return $this->render('file/upload.html.twig', array(
            // 'entity' => $entity,
            // 'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Files entity.
     *
     */
    public function createAction(Request $request)
    {
        // $entity  = new Files();
        // $form = $this->createForm(new FilesType(), $entity);
        // $form->bind($request);
        //
        // if ($form->isValid()) {
        //     $em = $this->getDoctrine()->getManager();
        //     $em->persist($entity);
        //     $em->flush();
        //
        //     return $this->redirect($this->generateUrl('file_show', array('id' => $entity->getId())));
        // }

        return $this->render('file/upload.html.twig', array(
            //'entity' => $entity,
            //'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Files entity.
     *
     */
    // public function editAction($id)
    // {
    //     $em = $this->getDoctrine()->getManager();
    //
    //     $entity = $em->getRepository('OmarevTvBundle:Files')->find($id);
    //
    //     if (!$entity) {
    //         throw $this->createNotFoundException('Unable to find Files entity.');
    //     }
    //
    //     $editForm = $this->createForm(new FilesType(), $entity);
    //     $deleteForm = $this->createDeleteForm($id);
    //
    //     return $this->render('OmarevTvBundle:Files:edit.html.twig', array(
    //         'entity'      => $entity,
    //         'edit_form'   => $editForm->createView(),
    //         'delete_form' => $deleteForm->createView(),
    //     ));
    // }

    /**
     * Edits an existing Files entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OmarevTvBundle:Files')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Files entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new FilesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('files_edit', array('id' => $id)));
        }

        return $this->render('OmarevTvBundle:Files:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Files entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OmarevTvBundle:Files')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Files entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('files'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function importAction(Request $request)
    {
        $media_dir = $this->container->getParameter('media_dir');

        if ( "POST" === $request->getMethod() )
        {
            $post_files = $request->request->get('files',array());

            while($file = array_shift($post_files))
            {
                $command = $this->container->getParameter('ffmpeg_path') . ' -i ' . escapeshellarg($media_dir . $file) . ' 2>&1';

                $output = shell_exec($command);

                if(preg_match('/\n\s+Duration: (.*?),/', $output, $matches))
                {
                    // valid media file
                    $parts = explode(':',$matches[1]);

                    $time = 0;

                    $time += $parts[0]*60*60;
                    $time += $parts[1]*60;
                    $time += $parts[2];
                    $time = round($time);

                    $entity = new Files();

                    $entity->setName(basename($file));
                    $entity->setFile($file);
                    $entity->setDuration($time);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entity);
                    $em->flush();

                }
                else {
                    throw new \Exception('Metadata not found');
                }
            }
        }

        $command = 'cd ' . $media_dir . ' && find -type f \( -name "*.mp4" -o -name "*.mov" -o -name "*.flv" -o -name "*.ts" -o -name "*.mkv" -o -name "*.webm" \)';

        $files = explode("\n",shell_exec($command));

        $files = array_filter($files, function($file){
            return !in_array($file,array('','.','..'))?true:false;
        });

        $files = preg_replace('/^\./' ,'', $files);


        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OmarevTvBundle:Files')->findAll();

        $entities = array_map(function($entity){
            return $entity->getFile();
        }, $entities);

        $files = array_diff($files, $entities);


        return $this->render('OmarevTvBundle:Files:import.html.twig', array(
            'files' => $files,
        ));
    }

    /**
     * @Route("/files/upload", methods={"POST"}, name="file_upload")
     */
    function uploadAction(Request $request)
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

            $command = $this->getParameter('ffmpeg_path') . ' -hide_banner -i ' . escapeshellarg($fileName) . ' 2>&1';

            $output = shell_exec($command);
            $time = 0;
            if(preg_match('/\n\s+Duration: (.*?),/', $output, $matches))
            {
                // valid media file
                $parts = explode(':',$matches[1]);

                $time = 0;

                $time += $parts[0]*60*60;
                $time += $parts[1]*60;
                $time += $parts[2];
                $time = round($time);
            }

            $entity = new File();
            $entity->setName(basename($originalFilename));
            $entity->setFile($originalFilename);
            $entity->setDuration($time);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }

        return new JsonResponse($output, 201);
    }
}
