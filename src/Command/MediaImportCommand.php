<?php

namespace App\Command;

use App\Entity\File;
use App\Service\MediaDuration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MediaImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webplayout:media-import')
            ->setDescription('webplayout media import command')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'path'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('path');
        $file = basename($file);

        $media_dir = $this->getContainer()->getParameter('media_dir');
        $mediaDuration = $this->getContainer()->get('App\Service\MediaDuration');

        if($duration = $mediaDuration->getDuration($media_dir . DIRECTORY_SEPARATOR . $file))
        {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $path = DIRECTORY_SEPARATOR . $file;

            if (!$em->getRepository('App\Entity\File')->findBy(['file' => $path])) {

                $entity = new File();
                $entity->setName($file);
                $entity->setFile($path);
                $entity->setType('file');
                $entity->setDuration($duration);

                $em->persist($entity);
                $em->flush();
            } else {
                throw new \Exception('File exist. Skipped!');
            }
        }
        else {
            throw new \Exception('Metadata not found');
        }
    }
}
