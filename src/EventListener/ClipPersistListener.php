<?php

declare(strict_types=1);

namespace App\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
//use App\Entity\ClipFile;
use App\Entity\File;

class ClipPersistListener
{
    function __construct(string $media_dir, string $logo, $doctrine)
    {
        $this->media_dir = $media_dir;
        $this->logo = $logo;
        $this->manager = $doctrine->getManager();
    }

    function writeMlt(ResourceControllerEvent $event)
    {
        $date = date('Y-m-d');
        $date = $event->getSubject()->getId();
        $files = $event->getSubject()->getFiles();

        $filename = sprintf('%s.xml', $date);

        $xml = new \simpleXmlElement('<mlt/>');

        if (sizeof($files)) {

            $logo = $this->logo;

            if ($logo) {
                $producer = $xml->addChild('producer');
                $producer->addAttribute('id', 'logo0');
                $producer->addChild('property', $logo)->addAttribute('name', 'resource');
            }

            foreach ($files as $producerIndex => $resource) {
                $resource = $resource->getFile();
                $producer = $xml->addChild('producer');
                $producer->addAttribute('id', 'producer'.$producerIndex);
                $producer->addChild('property', ltrim($resource->getFile(),'/'))->addAttribute('name', 'resource');
            }

            if ($logo) {
                $playlistLogo = $xml->addChild('playlist');
                $playlistLogo->addAttribute('id', 'playlistLogo');

                $entry = $playlistLogo->addChild('entry');
                $entry->addAttribute('producer', 'logo0');
                $entry->addAttribute('repeat', '99999');
            }

            $playlist = $xml->addChild('playlist');
            $playlist->addAttribute('id', 'playlist-' . $date);

            foreach ($files as $producerIndex => $resource) {
                $resource = $resource->getFile();
                $entry = $playlist->addChild('entry');
                $entry->addAttribute('producer', 'producer' . $producerIndex);
                $entry->addAttribute('in', '0');
                $entry->addAttribute('out', '' . $resource->getDuration()*100);
            }

            if ($logo) {
                $tracktor = $xml->addChild('tracktor');
                $multitrack = $tracktor->addChild('multitrack');

                $trackPlaylist = $multitrack->addChild('track');
                $trackPlaylist->addAttribute('producer', 'playlist-' . $date);

                $trackLogo = $multitrack->addChild('track');
                $trackLogo->addAttribute('producer','playlistLogo');

                $transition = $tracktor->addChild('transition');
                $transition->addAttribute('in', '0.0');

                $prop_mlt_service = $transition->addChild('property', 'composite');
                $prop_mlt_service->addAttribute('name', 'mlt_service');

                $prop_track_a = $transition->addChild('property','0');
                $prop_track_a->addAttribute('name', 'a_track');

                $prop_track_b = $transition->addChild('property', '1');
                $prop_track_b->addAttribute('name', 'b_track');

                $prop_progressive = $transition->addChild('property', '1');
                $prop_progressive->addAttribute('name', 'progressive');

                $prop_start = $transition->addChild('property', '0/50:100%x100%:100');
                $prop_start->addAttribute('name', 'start');

                $prop_stop = $transition->addChild('property', '0/50:100%x100%:100');
                $prop_stop->addAttribute('name', 'end');

                $prop_halign = $transition->addChild('property', 'right');
                $prop_halign->addAttribute('name', 'halign');

                $prop_valign = $transition->addChild('property', 'top');
                $prop_valign->addAttribute('name', 'valign');

                $prop_distort = $transition->addChild('property', '0');
                $prop_distort->addAttribute('name', 'distort');
            }
        }

        file_put_contents($this->media_dir . DIRECTORY_SEPARATOR . $filename, $xml->asXML());


        $duration = array_sum($event->getSubject()->getFiles()->map(function($v) {
                return $v->getFile()->getDuration();
        })->toArray());

        if (!$event->getSubject()->getFile()) {

            $file = new File;
            $file->setFile($filename);
            $file->setName($event->getSubject()->getName());
            $file->setDuration($duration);
            $file->setType('clip');

            $this->manager->persist($file);

            $event->getSubject()->setFile($file);

            $this->manager->persist($event->getSubject());

        } else {
            $this->manager->persist(
                $event->getSubject()->getFile()
                    ->setDuration($duration)
                    ->setName($event->getSubject()->getName())
            );
        }

        $this->manager->flush();
    }
}
