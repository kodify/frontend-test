<?php

namespace Kodify\AdminBundle\Tests\Repository;

use Kodify\AdminBundle\Repository\TagRepository;
use Kodify\TestsBundle\Tests\RepositoryBaseClass;

class TagRepositoryTest extends RepositoryBaseClass
{
    public function testGetQuery()
    {
        $queryMock = \Mockery::mock()
            ->shouldReceive('setParameter')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('setMaxResults')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('getResult')->times(1)->andReturn(\Mockery::self())
            ->andReturn(true)
            ->getMock();


        $emMock = \Mockery::mock()
            ->shouldReceive('createQuery')->times(1)->andReturn($queryMock)
            ->getMock();

        $tagRepo = new TagRepository($emMock, $this->metaData);
        $this->assertTrue($tagRepo->getTagList());
    }

    public function testValidateTagList()
    {
        $queryMock = \Mockery::mock()
            ->shouldReceive('setParameter')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('getArrayResult')->times(1)
            ->andReturn(array(array('name' => 'tag 1')))
            ->getMock();

        $emMock = \Mockery::mock()
            ->shouldReceive('createQuery')->times(1)->andReturn($queryMock)
            ->getMock();

        $tagRepo = new TagRepository($emMock, $this->metaData);
        $response = $tagRepo->validateTagList(array('tag 1', 'tag 2', 'tag 3'));
        $diff = array_intersect($response, array('tag 2', 'tag 3'));
        $this->assertEquals(2, count($diff));
    }

    public function testValidatePornstarListEmptyInput()
    {
        $tagRepo = new TagRepository($this->em, $this->metaData);
        $response = $tagRepo->validateTagList(array(' ', ' ', ' '));
        $this->assertEquals(count($response), 0);
    }
}