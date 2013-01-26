<?php

namespace Kodify\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Kodify\AdminBundle\Repository\PornstarRepository")
 * @ORM\Table(name="Pornstar", indexes={@ORM\Index(name="status_idx", columns={"enabled"}), @ORM\Index(name="name_idx", columns={"name"})})
 * @UniqueEntity("name")
 * @codeCoverageIgnore
 */
class Pornstar
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
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $alias = '';

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $description = '';

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $twitter = '';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $thumbnailUrl = '';

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
     * description getter
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * description setter
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * Returns the alias
     * @return string
     */

    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias
     *
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }


    /**
     * twitter getter
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * twitter setter
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
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
     * thumbnailUrl getter
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * thumbnailUrl setter
     * @param string $thumbnailUrl
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        $this->thumbnailUrl = $thumbnailUrl;
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
