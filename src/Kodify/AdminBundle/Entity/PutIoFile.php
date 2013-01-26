<?php

namespace Kodify\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Kodify\AdminBundle\Repository\PutIoFileRepository")
 * @ORM\Table(name="PutioFile")
 * @codeCoverageIgnore
 */
class PutIoFile
{
    const PENDING = 1;
    const DOWNLOADING = 2;
    const FAIL = 3;
    const DOWNLOADED = 4;
    const UPLOADING = 5;
    const UPLOAD_SENT = 6;
    const UPLOAD_FAILED = 7;
    const DUPLICATED = 8;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $putioId;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="This field is required")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="This field is required")
     */
    protected $downloadUrl;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="This field is required")
     */
    protected $size;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastTry;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $attempts = 0;

    /**
     * @ORM\Column(type="text")
     */
    protected $localPath = '';

    /**
     * @ORM\ManyToOne(targetEntity="Kodify\AdminBundle\Entity\PutIoFile" )
     */
    protected $duplicated = null;

    /**
     * @ORM\OneToOne(targetEntity="Kodify\AdminBundle\Entity\Video")
     */
    protected $video;


    public function setDuplicated($putIoFile)
    {
        $this->duplicated = $putIoFile;

        return $this;
    }

    public function getDuplicated()
    {
        return $this->duplicated;
    }

    public function setVideo($video)
    {
        $this->video = $video;
    }

    public function getVideo()
    {
        return $this->video;
    }


    public function getLocalPath()
    {
        return $this->localPath;
    }

    public function setLocalPath($localPath)
    {
        $this->localPath = $localPath;
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
     * Set putioId
     *
     * @param integer $putioId
     *
     * @return PutIoFile
     */
    public function setPutioId($putioId)
    {
        $this->putioId = $putioId;

        return $this;
    }

    /**
     * Get putioId
     *
     * @return integer
     */
    public function getPutioId()
    {
        return $this->putioId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PutIoFile
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
     * Set $createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return PutIoFile
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get $createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set downloadUrl
     *
     * @param string $downloadUrl
     *
     * @return PutIoFile
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    /**
     * Get downloadUrl
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return PutIoFile
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return PutIoFile
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
     * Set $lastTry
     *
     * @param \DateTime $lastTry
     *
     * @return PutIoFile
     */
    public function setLastTry($lastTry)
    {
        $this->lastTry = $lastTry;

        return $this;
    }

    /**
     * Get $lastTry
     *
     * @return \DateTime
     */
    public function getLastTry()
    {
        return $this->lastTry;
    }

    /**
     * Set $attempts
     *
     * @param integer $attempts
     *
     * @return PutIoFile
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Get $attempts
     *
     * @return integer
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    public function markAsStatus($status)
    {
        $previousStatus = $this->getStatus();
        if ($previousStatus != $status) {
            $this->setAttempts(0);
        }
        $this->setStatus($status);
        $this->setLastTry(new \DateTime());
        $this->setAttempts($this->getAttempts() + 1);
    }

}
