<?php

declare(strict_types=1);

namespace App\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ClipPersistListener
{
    function __construct(string $media_dir, RegistryInterface $doctrine)
    {
        $this->media_dir = $media_dir;
        $this->manager = $doctrine->getManager();
    }

    function writeMlt(ResourceControllerEvent $event)
    {
        $id = $event->getSubject()->getId();
        $files = $event->getSubject()->getFiles();

        $filename = sprintf('%s.xml', $id);

        $xml = new \simpleXmlElement('<mlt/>');

        if (sizeof($files)) {

            $logoSetting = $this->manager->getRepository('App\Entity\Setting')
                ->findOneBy(['name' => 'logo']);

            $logo = $logoSetting ? $logoSetting->getValue() : '';

            $producers = [];
            foreach ($files as $producerIndex => $resource) {
                $resource = $resource->getFile();
                $producerIndex = $resource->getId();
                if (in_array($producerIndex, $producers)) continue;
                $producers[] = $producerIndex;
                $producer = $xml->addChild('producer');
                $producer->addAttribute('id', 'producer'.$producerIndex);
                $producer->addChild('property', ltrim($resource->getFile(),'/'))->addAttribute('name', 'resource');
            }

            $duration = array_sum($event->getSubject()->getFiles()->map(function($v) {
                return $v->getFile()->getDuration();
            })->toArray());

            $playlist = $xml->addChild('playlist');
            $playlist->addAttribute('id', 'playlist-' . $id);

            foreach ($files as $producerIndex => $resource) {
                $resource = $resource->getFile();
                $producerIndex = $resource->getId();
                $entry = $playlist->addChild('entry');
                $entry->addAttribute('producer', 'producer' . $producerIndex);
                $entry->addAttribute('in', '0');
                $out = (round(($duration*25)/100, 0) - 1);
                $entry->addAttribute('out', '' . $out);
            }

            if ($logo) {
                $filter = $xml->addChild('filter');
                foreach([
                    'mlt_service' => 'watermark',
                    'resource' => $logo,
                    'composite.progressive' => '1',
                    'composite.valign' => 'top',
                    'composite.halign' => 'right',
                    'composite.sliced_composite' => '1',
                    'composite.geometry' => '0/50:100%x100%:100',
                    'composite.distort' => '0',
                    ] as $prop_name => $prop_value) {
                    $filter_prop = $filter->addChild('property', $prop_value);
                    $filter_prop->addAttribute('name', $prop_name);
                }
            }
        }

        file_put_contents($this->media_dir . DIRECTORY_SEPARATOR . $filename, $xml->asXML());

        $this->manager->persist(
            $event->getSubject()
                ->setFile($filename)
                ->setDuration($duration)
        );

        $this->manager->flush();
    }
}
