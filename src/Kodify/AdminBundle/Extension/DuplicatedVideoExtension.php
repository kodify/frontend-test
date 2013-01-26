<?php

namespace Kodify\AdminBundle\Extension;

use Symfony\Bridge\Doctrine\RegistryInterface;

class DuplicatedVideoExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            'get_duplicated_video_info' => new \Twig_Function_Method($this, 'getDuplicatedVideoInfo'),
        );
    }

    public function getDuplicatedVideoInfo($videoId)
    {
        return $this->doctrine
            ->getRepository('KodifyAdminBundle:PutIoFile')
            ->getDuplicatedVideoInfo($videoId);
    }

    public function getName()
    {
        return 'duplicatedvideo_extension';
    }

}