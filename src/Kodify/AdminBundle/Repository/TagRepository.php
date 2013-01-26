<?php
namespace Kodify\AdminBundle\Repository;

use Kodify\SimpleCrudBundle\Repository\AbstractCrudRepository;

class TagRepository extends AbstractCrudRepository
{
    public function getTagList($term = '', $limit = 10)
    {
        $dql = '
            SELECT partial t.{name, id}
            FROM KodifyAdminBundle:Tag t
            WHERE t.enabled = true AND t.name LIKE :searchTerm
            ORDER BY t.name ASC';

        $response = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('searchTerm', $term.'%')
            ->setMaxResults($limit)
            ->getResult();

        return $response;
    }

    public function validateTagList($list)
    {
        $list = array_filter($list, 'trim');
        if (empty($list)) {
            return array();
        }

        $dql = '
            SELECT partial t.{id, name}
            FROM KodifyAdminBundle:Tag t
            WHERE t.enabled = true AND t.name IN (:searchTerm)';

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
