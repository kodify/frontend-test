<?php
namespace Kodify\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Kodify\AdminBundle\Entity\PutIoFile;
use Kodify\AdminBundle\Entity\Video;


class PutIoFileRepository extends EntityRepository
{
    public function getPutioNotDeletedFilesCount()
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.video', 'v')
            ->where('v.status < ' . Video::READY)
            ->orWhere('v.status IS NULL')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function isNewFile($putioFileId)
    {
        $obj = $this->findOneBy(array('putioId' => $putioFileId));
        return (!$obj instanceof PutIoFile);
    }

    public function fetchDuplicatedFile($putioFileName)
    {
        return $this->findOneBy(array('name' => $putioFileName));
    }


    public function getOneByStatus($status)
    {
        $obj = null;
        $obj = $this->findOneBy(array('status' => $status));

        return $obj;
    }

    public function getNthByStatus($status, $position)
    {
        $putIoFile = null;
        $putIoFilesList = $this->findBy(array('status' => $status), null, $position);

        if (count($putIoFilesList) == $position) {
            $putIoFile = end($putIoFilesList);
        }

        return $putIoFile;
    }

    public function getDuplicatedVideoInfo($videoId)
    {

        $putIoFile = $this->findOneBy(array('video' => $videoId));

        if (!$putIoFile instanceof PutIoFile) {
            return false;
        }
        $duplicated = $putIoFile->getDuplicated();

        return array(
            'name' => $duplicated->getName(),
            'duration' => $duplicated->getVideo()->getDurationInMilliseconds(),
            'oldSize' => $duplicated->getSize(),
            'newSize' => $putIoFile->getSize(),
        );
    }

}
