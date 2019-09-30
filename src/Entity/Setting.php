<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="settings")
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * Get id
     *
     * @return UUID
     */
    public function getId():string
    {
        return $this->id;
    }

    /**
     * @var string $name
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $name;

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
    public function getName():string
    {
        return $this->name;
    }

    /**
     * @var string $value
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $value;

    /**
     * Set value
     *
     * @param string $value
     * @return Clips
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue():string
    {
        return $this->value;
    }
}
