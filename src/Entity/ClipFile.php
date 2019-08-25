<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClipFileRepository")
 * @ORM\Table(name="clip_files")
 */
class ClipFile implements ResourceInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="Clip", inversedBy="files")
     * @ORM\JoinColumn(name="clip_id", referencedColumnName="id")
     */
    private $clip;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="clips")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ord;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClip(): ?Clip
    {
        return $this->clip;
    }

    public function setClip(Clip $clip): self
    {
        $this->clip = $clip;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getOrd(): ?int
    {
        return $this->ord;
    }

    public function setOrd(?int $ord): self
    {
        $this->ord = $ord;

        return $this;
    }
}
