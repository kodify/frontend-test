<?php
namespace Kodify\AdminBundle\Repository;

use Kodify\SimpleCrudBundle\Repository\AbstractCrudRepository;

class PornstarRepository extends AbstractCrudRepository
{
    public function getPornstarList($term = '', $limit = 10)
    {
        $dql = '
            SELECT p
            FROM KodifyAdminBundle:Pornstar p
            WHERE p.enabled = true AND p.name LIKE :searchTerm
            ORDER BY p.name ASC';

        $list = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('searchTerm', $term.'%')
            ->setMaxResults($limit)
            ->getResult();

        return $list;
    }

    public function validatePornstarList($list)
    {
        $list = array_filter($list, 'trim');
        if (empty($list)) {
            return array();
        }

        $dql = '
            SELECT partial p.{id, name, thumbnailUrl}
            FROM KodifyAdminBundle:Pornstar p
            WHERE p.enabled = true AND p.name IN (:searchTerm)';


        $found = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('searchTerm', $list)
            ->getArrayResult();

        $arrayFound = array();
        foreach ($found as $f) {
            $arrayFound[] = $f['name'];
        }

        return array_diff($list, $arrayFound);
    }
}
