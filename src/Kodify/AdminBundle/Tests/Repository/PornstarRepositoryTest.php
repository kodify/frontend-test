<?php

namespace Kodify\AdminBundle\Tests\Repository;

use Kodify\AdminBundle\Repository\PornstarRepository;
use Kodify\TestsBundle\Tests\RepositoryBaseClass;

class PornstarRepositoryTest extends RepositoryBaseClass
{
    public function testGetQuery()
    {
        $queryMock = \Mockery::mock()
            ->shouldReceive('setParameter')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('getResult')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('setMaxResults')->times(1)->andReturn(\Mockery::self())
            ->getMock();

        $emMock = \Mockery::mock()
            ->shouldReceive('createQuery')->times(1)->andReturn($queryMock)
            ->getMock();

        $pornstarRepo = new PornstarRepository($emMock, $this->metaData);
        $pornstarRepo->getPornstarList();
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testValidatePornstarList()
    {
        $queryMock = \Mockery::mock()
            ->shouldReceive('setParameter')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('getArrayResult')->times(1)
            ->andReturn(array(array('name' => 'p 1')))
            ->getMock();

        $emMock = \Mockery::mock()
            ->shouldReceive('createQuery')->times(1)->andReturn($queryMock)
            ->getMock();

        $pornstarRepo = new PornstarRepository($emMock, $this->metaData);
        $response = $pornstarRepo->validatePornstarList(array('p 1', 'p 2', 'p 3'));
        $diff = array_intersect($response, array('p 2', 'p 3'));
        $this->assertEquals(2, count($diff));
    }

    public function testValidatePornstarListEmptyInput()
    {
        $pornstarRepo = new PornstarRepository($this->em, $this->metaData);
        $response = $pornstarRepo->validatePornstarList(array(' ', ' ', ' '));
        $this->assertEquals(count($response), 0);
    }
}