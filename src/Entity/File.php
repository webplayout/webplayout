<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="files")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"file" = "App\Entity\File", "clip" = "App\Entity\Clip"})
 */
class File implements ResourceInterface
{
    /**
     * @var string $name
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string $file
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $file;

    /**
    * @ORM\OneToMany(targetEntity="ClipFile", mappedBy="clip",
    *      cascade={"persist", "remove"}, orphanRemoval=true)
    * @ORM\OrderBy({"ord" = "ASC"})
    */
    protected $files;

    /**
     * @var integer $duration
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var UUID $id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    function __construct()
    {
        $this->files = new ArrayCollection;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Files
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Files
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set files
     *
     * @param string $files
     * @return Clip
     */
    public function setFiles($files)
    {
        foreach ($files as $file) {
            $file->setClip($this);
        }

        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return string
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Files
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
