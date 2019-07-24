<?php

namespace App\Entity\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 * @ORM\Table(name="schedule")
 */
class Schedule implements ResourceInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
