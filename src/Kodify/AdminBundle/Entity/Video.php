<?php

namespace Kodify\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Video entity
 *
 * @ORM\Entity(repositoryClass="Kodify\AdminBundle\Repository\VideoRepository")
 * @ORM\Table(name="Video", indexes={@ORM\Index(name="status_idx", columns={"status"})})
 * @codeCoverageIgnore
 */
class Video
{
    /* Video is being uploaded now */
    const UPLOADING = 1;
    /* Something happened during upload */
    const UPLOAD_FAILED = 2;
    /* Video is being Transcoded */
    const TRANSCODING = 3;
    /* Ready */
    const READY = 4;
    /* video is blocked by another user */
    const BLOCKED = 5;
    /* video has been cut */
    const CUT = 6;
    /* video is masked as not valid */
    const CANCELLED = 7;
    /* Transcoding Failed */
    const TRANSCODING_FAILED = 8;
    /** Duplicate warning */
    const DUPLICATE_WARNING = 9;
    /** Not duplicated confirmed */
    const MARKED_AS_NOT_DUPLICATED = 10;
    /** Duplicated confirmed */
    const MARKED_AS_DUPLICATED = 11;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $filename = '';

    /**
     * Video duration in milliseconds
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $durationInMilliseconds = 0;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $transcoderId = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $originalVideoTranscoderId = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $thumbnailsTranscoderId = 0;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $originalName = '';

    /**
     * @ORM\Column(type="smallint")
     */
    protected $retries = 1;

    /**
     * @ORM\Column(type="string", length=150)
     */
    protected $blockedBy = '';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $thumbnailsDeleted = 0;

    /*
     * @ORM\OneToMany(targetEntity="Clip", mappedBy="video")
     */
    protected $clips;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clips = new ArrayCollection();
        $this->thumbs = new ArrayCollection();
    }

    /**
     * Get thumbnailsDeleted
     *
     * @return string
     */
    public function getThumbnailsDeleted()
    {
        return $this->thumbnailsDeleted;
    }

    /**
     * Set thumbnailsDeleted
     *
     * @param string $thumbnailsDeleted
     *
     * @return Video
     */
    public function setThumbnailsDeleted($thumbnailsDeleted)
    {
        $this->thumbnailsDeleted = $thumbnailsDeleted;

        return $this;
    }

    /**
     * Get transcoderId
     *
     * @return string
     */
    public function getTranscoderId()
    {
        return $this->transcoderId;
    }

    /**
     * Set transcoderId
     *
     * @param string $transcoderId
     *
     * @return Video
     */
    public function setTranscoderId($transcoderId)
    {
        $this->transcoderId = $transcoderId;

        return $this;
    }

    /**
     * Get thumbnailsTranscoderId
     *
     * @return string
     */
    public function getThumbnailsTranscoderId()
    {
        return $this->thumbnailsTranscoderId;
    }

    /**
     * Set thumbnailsTranscoderId
     *
     * @param string $thumbnailsTranscoderId
     *
     * @return Video
     */
    public function setThumbnailsTranscoderId($thumbnailsTranscoderId)
    {
        $this->thumbnailsTranscoderId = $thumbnailsTranscoderId;

        return $this;
    }

    /**
     * Get OriginalVideoTranscoderId
     *
     * @return string
     */
    public function getOriginalVideoTranscoderId()
    {
        return $this->originalVideoTranscoderId;
    }

    /**
     * Set OriginalVideoTranscoderId
     *
     * @param string $originalVideoTranscoderId
     *
     * @return Video
     */
    public function setOriginalVideoTranscoderId($originalVideoTranscoderId)
    {
        $this->originalVideoTranscoderId = $originalVideoTranscoderId;

        return $this;
    }

    /**
     * Get Original Name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return Video
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get Retries
     *
     * @return integer
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * Set Retries
     *
     * @param int $retries
     *
     * @return Video
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;

        return $this;
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

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Video
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Video
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
     *
     * @return Video
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
     * Set clips
     *
     * @param \Doctrine\Common\Collections\ArrayCollection  $clips
     *
     * @return Video
     */
    public function setClips($clips)
    {
        $this->clips = $clips;

        return $this;
    }

    /**
     * get CLips
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getClips()
    {
        return $this->clips;
    }

    /**
     * Set Thumbs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $thumbs
     *
     * @return Video
     */
    public function setThumbs($thumbs)
    {
        $this->thumbs = $thumbs;

        return $this;
    }

    /**
     * Get Thumbs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }

    /**
     * Gets a list of possible status
     *
     * @return array
     */
    public static function getPossibleStatus()
    {
        return array(
            static::READY                       => 'Ready',
            static::BLOCKED                     => 'Blocked',
            static::CUT                         => 'Cut',
            static::CANCELLED                   => 'Cancelled',
            static::READY                       => 'Ready',
            static::UPLOADING                   => 'Uploading',
            static::TRANSCODING                 => 'Transcoding',
            static::UPLOAD_FAILED               => 'Upload KO',
            static::TRANSCODING_FAILED          => 'Transcoding KO',
            static::DUPLICATE_WARNING           => 'Duplicated?',
            static::MARKED_AS_DUPLICATED        => 'Duplicated',
            static::MARKED_AS_NOT_DUPLICATED    => 'Not duplicated',
        );
    }

    /**
     * Set blockedBy
     *
     * @param string $blockedBy
     *
     * @return Video
     */
    public function setBlockedBy($blockedBy)
    {
        $this->blockedBy = $blockedBy;

        return $this;
    }

    /**
     * Get blockedBy
     *
     * @return string
     */
    public function getBlockedBy()
    {
        return $this->blockedBy;
    }

    /**
     * Add clips
     *
     * @param \Kodify\AdminBundle\Entity\Clip $clips
     *
     * @return Video
     */
    public function addClip(Clip $clips)
    {
        $this->clips[] = $clips;

        return $this;
    }

    /**
     * Remove clips
     *
     * @param \Kodify\AdminBundle\Entity\Clip $clips
     */
    public function removeClip(Clip $clips)
    {
        $this->clips->removeElement($clips);
    }

    /**
     * Get duration in milliseconds
     *
     * @return int
     */
    public function getDurationInMilliseconds()
    {
        return $this->durationInMilliseconds;
    }

    /**
     * @param int $duration
     *
     * @return Video
     */
    public function setDurationInMilliseconds($duration)
    {
        $this->durationInMilliseconds = $duration;

        return $this;
    }

    /**
     * @param int $interval
     *
     * @return array
     */
    public function getThumbnails($interval)
    {
        $list = array();
        $total = round($this->getDurationInMilliseconds() / 1000);
        $baseName = $this->thumbnailsTranscoderId;

        for ($i = 1; $i <= $total; $i += $interval) {
            $list[] = array(
                'url' => "{$baseName}_{$i}.jpg",
                'time' => $i
            );

        }

        return $list;
    }
}
