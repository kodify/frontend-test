<?php

namespace Kodify\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="Kodify\AdminBundle\Repository\ClipRepository")
 * @ORM\Table(name="Clip")
 * @codeCoverageIgnore
 */
class Clip
{
    const SUCCESS = 1;
    const PENDING = 2;
    const FAIL = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $contentManager;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $startTime;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $endTime;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="This field is required")
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="clips")
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id")
     */
    protected $video;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $pornstars = '';

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="This field is required")
     */
    protected $tags;

    /**
     * Get id
     *
     * @return integer
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
     * Set title
     *
     * @param string $title
     * @return Clip
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set contentManager
     *
     * @param string $contentManager
     * @return Clip
     */
    public function setContentManager($contentManager)
    {
        $this->contentManager = $contentManager;

        return $this;
    }

    /**
     * Get contentManager
     *
     * @return string
     */
    public function getContentManager()
    {
        return $this->contentManager;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return Clip
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Clip
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Video $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return Video
     */
    public function getVideo()
    {
        return $this->video;
    }



    public static function getPossibleStatus()
    {
        return array(
            self::SUCCESS => 'Success',
            self::PENDING => 'Pending',
            self::FAIL => 'Fail',
        );
    }

    /**
     * Set startTime
     *
     * @param string $startTime
     * @return Clip
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    
        return $this;
    }

    /**
     * Get startTime
     *
     * @return string 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param string $endTime
     * @return Clip
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    
        return $this;
    }

    /**
     * Get endTime
     *
     * @return string 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set pornstars
     *
     * @param string $pornstars
     * @return Clip
     */
    public function setPornstars($pornstars)
    {
        $this->pornstars = $pornstars;
    
        return $this;
    }

    /**
     * Get pornstars
     *
     * @return string 
     */
    public function getPornstars()
    {
        return $this->pornstars;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Clip
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    
        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }
}