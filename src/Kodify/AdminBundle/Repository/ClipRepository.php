<?php
namespace Kodify\AdminBundle\Repository;

use Kodify\SimpleCrudBundle\Repository\AbstractCrudRepository;

class ClipRepository extends AbstractCrudRepository
{
    protected $selectEntities = 'p, Video';
    protected $selectLeftJoin = array(array('field' => 'p.video', 'alias' => 'Video'));
}
