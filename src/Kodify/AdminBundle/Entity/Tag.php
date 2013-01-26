<?php

namespace Kodify\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Kodify\AdminBundle\Repository\TagRepository")
 * @ORM\Table(name="Tag", indexes={@ORM\Index(name="status_idx", columns={"enabled"})})
 * @UniqueEntity("name")
 *
 * @codeCoverageIgnore
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 1;

    /**
     * Id getter
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Id setter
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the name
     * @return string
     */

    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * enabled getter
     * @return string
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * enabled setter
     * @param string $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Returns a string representation of the class
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
