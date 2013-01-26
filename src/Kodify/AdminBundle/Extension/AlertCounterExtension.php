<?php

namespace Kodify\AdminBundle\Extension;

use Symfony\Bridge\Doctrine\RegistryInterface;

class AlertCounterExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            'get_putio_alert' => new \Twig_Function_Method($this, 'getPutioAlert'),
            'get_ready_alert' => new \Twig_Function_Method($this, 'getReadyAlert'),
        );
    }

    public function getPutioAlert()
    {
        return $this->doctrine
            ->getRepository('KodifyAdminBundle:PutIoFile')
            ->getPutioNotDeletedFilesCount();
    }

    public function getReadyAlert()
    {
        return $this->doctrine
            ->getRepository('KodifyAdminBundle:Video')
            ->getReadyToCutVideosCount();
    }

    public function getName()
    {
        return 'alertcounter_extension';
    }

}