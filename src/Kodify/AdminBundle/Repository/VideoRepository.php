<?php
namespace Kodify\AdminBundle\Repository;

use Kodify\SimpleCrudBundle\Repository\AbstractCrudRepository;
use Kodify\AdminBundle\Entity\Video;

class VideoRepository extends AbstractCrudRepository
{
    public function getReadyToCutVideosCount()
    {
        $query = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.status = :status')
            ->setParameter('status', Video::READY)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getNextReadyVideo()
    {
        return $this->findOneBy(array('status' => array(Video::READY)));
    }

    public function getQuery($filters = array(), $pageSize = 10, $currentPage = 0, $sort = null, $defaultSort = null)
    {
        if (!isset($filters['status']) || empty($filters['status'])) {
            $filters['status'] = array(
                'value' => array(Video::BLOCKED, Video::READY, Video::DUPLICATE_WARNING),
                'operator' => 'in'
            );
        }

        return parent::getQuery($filters, $pageSize, $currentPage, $sort, $defaultSort);
    }

    public function getAllByStatus($status)
    {
        $obj = $this->findBy(array('status' => $status));

        return $obj;
    }

    public function getThumbnailDeletePending()
    {
        $query = $this->createQueryBuilder('v')
            ->select('v')
            ->where('v.thumbnailsDeleted != 1')
            ->andWhere('v.status IN (:param)')
            ->setParameter('param', array(Video::CUT, Video::CANCELLED));

        return $query->getQuery()->getResult();
    }

}
