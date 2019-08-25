<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity()
 * @ORM\Table(name="clips")
 */
class Clip implements ResourceInterface
{
    /**
    * @ORM\OneToMany(targetEntity="ClipFile", mappedBy="clip",
    *      cascade={"persist", "remove"}, orphanRemoval=true)
    * @ORM\OrderBy({"ord" = "ASC"})
    */
    private $files;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Clips
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
